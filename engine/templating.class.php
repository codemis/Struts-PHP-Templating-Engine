<?php
/**
 * This class handles all the templating processes and renders the final code
 *
 * @package STRUTS
 * @author Johnathan Pulos
 */
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
        trigger_error('Cloning the Routing is not permitted.', E_USER_ERROR);
    }
	
	/**
	 * setup the Routing class
	 *
	 * @return object
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function init() { 
        if (!self::$templatingInstance) { 
            self::$templatingInstance = new Templating(); 
        }
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
	    $this->addSettingsToTemplateTags();
    	self::trace('Completing processRequest()', __LINE__);
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
    	$this->findRestricted($settingsArray);
    	$this->templateTags = $settingsArray;
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