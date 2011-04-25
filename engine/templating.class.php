<?php
/**
 * This class handles all the templating processes and renders the final code
 *
 * @package STRUTS
 * @author Johnathan Pulos
 */
require_once(APP_PATH . 'engine' . DS . 'compression.class.php');
class Templating
{
    /**
	 * The singleton instance of the templating class
	 *
	 * @var Object
	 * @access private
	 */
	private static $templatingInstance;
	/**
	 * The singleton instance of the configure class
	 *
	 * @var Object
	 * @access public
	 */
	public static $configureInstance;
	/**
	 * The singleton instance of the logging class
	 *
	 * @var Object
	 * @access public
	 */
	public static $loggingInstance;
	/**
	 * The singleton instance of the logging class
	 *
	 * @var Object
	 * @access public
	 */
	public static $compressionInstance;
	/**
	 * An array of template tags set by the developer
	 *
	 * @var array
	 * @access private
	 */
    private $templateTags = array();
    /**
     * An array of tags restricted to the templating tool
     *
     * @var array
     */
    private $restrictedTags = array('strutCSS', 'strutJavascript', 'strutContent');
    /**
     * An array of tags that are unneccessar and need to be removed
     *
     * @author Johnathan Pulos
     */
     private $unnecessaryTags = array('css', 'javascript', 'template', 'landing_page', 'css_compress_directory', 'js_compress_directory', 'enable_caching', 'compress_js', 'compress_css', 'cache');
	
	/**
	 * Only allow one instance of this class.  To setup this class use Templating::init()
	 *
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function __construct() {
	}
	
	/**
     * prevent cloning of the object: issues an E_USER_ERROR if this is attempted
     *
 	 * @access public
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function __clone()
    {
        trigger_error('Cloning the Templating is not permitted.', E_USER_ERROR);
    }
	
	/**
	 * setup the Templating class
	 *
	 * @return object
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function init() { 
        if (!self::$templatingInstance) { 
            self::$templatingInstance = new Templating(); 
        }
        self::$compressionInstance = Compression::init(); 
        return self::$templatingInstance;
	}
	
	/**
	 * Complete anything necessary to process the curent requested page
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function processRequest() {
	    self::trace('Starting processRequest()', __LINE__);
        self::$compressionInstance->configureInstance = $this->configureInstance;
        self::$compressionInstance->loggingInstance = $this->loggingInstance;
	    $this->addSettingsToTemplateTags();
	    $this->processJavascript();
	    $this->processStylesheets();
    	self::trace('Completing processRequest()', __LINE__);
	}
	
	/**
	 * Close up the request by rendering the final template
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function completeRequest() {
	    self::trace('Starting completeRequest()', __LINE__);
	    
    	self::trace('Completing completeRequest()', __LINE__);
	}
	/**
	 * Sets a template tag for the view
	 *
	 * @param string $key the tag name
	 * @param string $val the value of that tag
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function setATemplateTag($key, $val) {
	    self::trace('Starting setATemplateTag("'.$key.'", "'.htmlspecialchars($val).'")', __LINE__);
	    if(in_array($key, $this->restrictedTags)) {
	        trigger_error('Restricted: You are not allowed to use <strong>'.$key.'</strong>.  These tags are not allowed '.implode(', ',$this->restrictedTags), E_USER_ERROR);
	    }else {
	        $this->templateTags[$key] = $val;   
	    }
	}
	
	/**
	 * Sets a template tag for the view from an array.  This is a wrapper method for mergeWithTemplateTags()
	 *
	 * @param array $arrayOfTags an array of tags with key value
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function setTemplateTagsWithArray($arrayOfTags) {
	    self::trace('Starting setATemplateTag("'.var_export($arrayOfTags,true).'")', __LINE__);
        $this->mergeWithTemplateTags($arrayOfTags);
        self::trace('Completing setATemplateTag()', __LINE__);
	}
	
	/**
	 * Sets the templateTags for the logging engine
	 *
	 * @return void
	 * @access private
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function refreshLoggingTemplateTags() {
	    $this->loggingInstance->templateTags = $this->templateTags;
	}
	
	/**
	 * Merge the current templateTags with an array of vars.  mergeWithArray takes precedence
	 *
	 * @param array $mergeWithArray an array to merge with
	 * @return void
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function mergeWithTemplateTags($mergeWithArray) {
	    self::trace('Starting mergeWithTemplateTags("'.var_export($mergeWithArray,true).'")', __LINE__);
	    $this->findRestricted($mergeWithArray);
	    $mergeWithArray = $this->removeUnnecessaryTags($mergeWithArray);
	    $this->templateTags = array_merge($this->templateTags, $mergeWithArray);
	    $this->loggingInstance->templateTags = $this->templateTags;
    	self::trace('Completing mergeWithTemplateTags()', __LINE__);
	}
	
	/**
	 * add the global and page specific settings to the templateTags array.  Page specific vars overwrite global settings.
	 *
	 * @return void
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function addSettingsToTemplateTags() {
	    self::trace('Starting addSettingsToTemplateTags()', __LINE__);
	    $globalSettings = $this->configureInstance->getSetting('global_settings');
    	$pageSettings = $this->configureInstance->getSetting('page_settings');
    	$settingsArray = array_merge($globalSettings, $pageSettings);
    	$this->mergeWithTemplateTags($settingsArray);
    	self::trace('Completing addSettingsToTemplateTags()', __LINE__);
	}
	
	/**
	 * Look for any restricted vars in the templateTags and throw an error
	 *
	 * @return void
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function findRestricted($searchArray) {
	    self::trace('Starting findRestricted("'.var_export($searchArray,true).'")', __LINE__);
	    foreach($this->restrictedTags as $restricted) {
	        if(array_key_exists($restricted, $searchArray)) {
	            trigger_error('Restricted: You are not allowed to use <strong>'.$restricted.'</strong>.  These tags are not allowed '.implode(', ',$this->restrictedTags), E_USER_ERROR);
	        }
	    }
	    self::trace('Completing findRestricted()', __LINE__);
	}
	
	/**
	 * Removes any unnecessary tags before setting the templatetags class variable
	 *
	 * @param array $searchArray array to search for tags
	 * @return array
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function removeUnnecessaryTags($searchArray){
	    self::trace('Starting removeUnnecessaryTags("'.var_export($searchArray,true).'")', __LINE__);
	    foreach($this->unnecessaryTags as $removeTag) {
	        if (array_key_exists($removeTag, $searchArray)) {
	            unset($searchArray[$removeTag]);
	        }
	    }
	    
        self::trace('Completing removeUnnecessaryTags()', __LINE__);
        return $searchArray;
	}
	
	/**
	 * Process the javascript by determining if it needs to be compressed and set the correct javascript tags. Sets the strutJavascript templating tag.
	 *
	 * @return void
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function processJavascript() {
	    self::trace('Starting processJavascript()', __LINE__);
	    $javascript = '';
	    $javascriptArray = array();
    	$globalSettings = $this->configureInstance->getSetting('global_settings');
    	$pageSettings = $this->configureInstance->getSetting('page_settings');
	    $debug_level = $this->configureInstance->getSetting('debug_level');
        $jsFormat = $this->configureInstance->getSetting('js_tag_format');
        if($globalSettings['compress_js'] === true && $debug_level <= 3) {
            /**
             * Compress the javascript files
             *
             * @author Technoguru Aka. Johnathan Pulos
             */
             $javascriptArray = self::$compressionInstance->compressJavascript();

        }else {
            $javascriptArray = self::createResourceTags($globalSettings['javascript'], $pageSettings['javascript']);
        }
        foreach($javascriptArray as $js) {
            $javascript .= sprintf($jsFormat, $js);
        }
        $this->templateTags['strutJavascript'] = $javascript;
        self::refreshLoggingTemplateTags();
        self::trace('Completing processJavascript()', __LINE__);
	}
	
	/**
	 * Process the global and page stylesheets by determining if compression is on, and creating the appropriate stylesheet tags. Sets the strutCSS templating tag.
	 *
	 * @return void
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function processStylesheets() {
	    self::trace('Starting processStylesheets()', __LINE__);
        $stylesheet = '';
	    $stylesheetArray = array();
    	$globalSettings = $this->configureInstance->getSetting('global_settings');
    	$pageSettings = $this->configureInstance->getSetting('page_settings');
	    $debug_level = $this->configureInstance->getSetting('debug_level');
        $cssFormat = $this->configureInstance->getSetting('css_tag_format');
        if($globalSettings['compress_css'] === true && $debug_level <= 1) {
            /**
             * Compress the css files
             *
             * @author Technoguru Aka. Johnathan Pulos
             */
             $stylesheetArray = self::$compressionInstance->compressCSS();

        }else {
            $stylesheetArray = self::createResourceTags($globalSettings['css'], $pageSettings['css']);
        }
        foreach($stylesheetArray as $css) {
            $stylesheet .= sprintf($cssFormat, $css);
        }
        $this->templateTags['strutCSS'] = $stylesheet;
        self::refreshLoggingTemplateTags();
        self::trace('Completing processStylesheets()', __LINE__);
	}
	
	/**
	 * Takes the global and page specific resources and creates an appropriate array of each resource.
	 *
	 * @return array
	 * @access private
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function createResourceTags($global, $pageSpecific) {
	    self::trace('Starting createResourceTags("'.$global.'", "'.$pageSpecific.'")', __LINE__);
	    $tags = '';
	    $globalResources = $pageResources = $allResources = array();
        
        if($global != '') {
            $globalResources = explode(',', $global);
        }
        if($pageSpecific != '') {
            $pageResources = explode(',', $pageSpecific);   
        }
        if((!empty($globalResources)) && (!empty($pageResources))) {
            $allResources = array_unique(array_merge($globalResources, $pageResources));
        }else if(!empty($globalResources)) {
            $allResources = $globalResources;
        }else if(!empty($pageResources)) {
            $allResources = $pageResources;
        }
        self::trace('Completing createResourceTags(): returning ' . var_export($allResources, true), __LINE__);
        return $allResources;
	}	
	
	/**
	 * convienence method for logging traces
	 *
	 * @param string $message message to add to stack 
	 * @return void
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function trace($message, $line = '') {
	    $this->loggingInstance->logTrace('<strong>Templating (line# '.$line.')</strong>: '.$message);
	}
}
?>