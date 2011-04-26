<?php
/**
 * strutsEngine Templating Engine Class
 *
 * A simplistic PHP Templating engine that seperates all code from design.
 * 
 * This class provides a templating engine to easily share 1 template over multiple PHP files
 * In your page files put a ##variable name##,  and set the variable in the php file
 * This class will recursively replace all ##var## in a HTML file with the variable provided in PHP.
 * 
 * @package STRUTS
 * @author Technoguru Aka. Johnathan Pulos  (mailto:johnathan@jpulos.com)
 **/
 require_once(APP_PATH . 'engine' . DS . 'configure.class.php');
 require_once(APP_PATH . 'engine' . DS . 'routing.class.php');
 require_once(APP_PATH . 'engine' . DS . 'logging.class.php');
 require_once(APP_PATH . 'engine' . DS . 'caching.class.php');
 require_once(APP_PATH . 'engine' . DS . 'templating.class.php');
class strutsEngine
{
	/**
	 * The singleton instance of the class
	 *
	 * @var Object
	 */
	private static $strutsInstance;
	/**
	 * The singleton instance of the configure class
	 *
	 * @var Object
	 */
	private static $configureInstance;
	/**
	 * The singleton instance of the routing class
	 *
	 * @var Object
	 */
	private static $routingInstance;
	/**
	 * The singleton instance of the logging class
	 *
	 * @var Object
	 */
	private static $loggingInstance;
	/**
	 * The singleton instance of the caching class
	 *
	 * @var Object
	 */
	private static $cachingInstance;
	/**
	 * The singleton instance of the templating class
	 *
	 * @var Object
	 */
	private static $templatingInstance;
	
	/**
	 * Only allow one instance of this class.  To setup this class use strutsEngine::scaffold()
	 *
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function __construct() {
	}
	
	/**
     * prevent cloning of the object: issues an E_USER_ERROR if this is attempted
     *
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function __clone()
    {
        trigger_error('Cloning the STRUT is not permitted.', E_USER_ERROR);
    }
	
	/**
	 * setup the STRUTS Templating Engine.  Initializes are the necessary classes, and sneds necessary objects to the other classes
	 *
	 * @return object
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function init() {
	    if (!self::$loggingInstance) { 
            self::$loggingInstance = Logging::init();
             
            self::trace('Completed initializing Logging Class', __LINE__);
        }
	    if (!self::$strutsInstance) {
            self::trace('Started initializing strutsEngine Class', __LINE__); 
            
            self::$strutsInstance = new strutsEngine(); 
            
            self::trace('Completed initializing strutsEngine Class', __LINE__);
        }
        if (!self::$routingInstance) { 
            self::trace('Started initializing Routing Class', __LINE__);
            
            self::$routingInstance = Routing::init();
            self::$routingInstance->loggingInstance = self::$loggingInstance;
            
            self::trace('Completed initializing Routing Class', __LINE__); 
        } 
        if (!self::$cachingInstance) {
            self::trace('Started initializing Caching Class', __LINE__);
             
            self::$cachingInstance = Caching::init();
            self::$cachingInstance->loggingInstance = self::$loggingInstance;
            
            self::trace('Completed initializing Caching Class', __LINE__); 
        }
        if (!self::$templatingInstance) {
            self::trace('Started initializing Templating Class', __LINE__);
             
            self::$templatingInstance = Templating::init();
            self::$templatingInstance->loggingInstance = self::$loggingInstance;
            
            self::trace('Completed initializing Templating Class', __LINE__); 
        }
        if (!self::$configureInstance) {
            self::trace('Started initializing Configure Class', __LINE__);
             
            self::$configureInstance = Configure::init();
            self::$configureInstance->loggingInstance = self::$loggingInstance;
            
            self::$routingInstance->configureInstance = self::$configureInstance;
            self::$loggingInstance->configureInstance = self::$configureInstance;
            self::$cachingInstance->configureInstance = self::$configureInstance;
            self::$templatingInstance->configureInstance = self::$configureInstance;
            
            self::trace('Completed initializing Configure Class', __LINE__); 
        }
	    
        self::trace('Completed strutsEngine::init()', __LINE__); 
    
        return self::$strutsInstance;
	}
	
	/**
	 * Set the configuration settings
	 *
	 * @param string $key The configuration key
	 * @param mixed $value The configuration value
	 * @return void
	 * @access public
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function setSetting($key, $value) {
	    $printed_value = (is_array($value)) ? var_export($value,true) : $value;
	    self::trace('setSetting("'.$key.'", "'.$printed_value.'")', __LINE__);
	    self::$configureInstance->setSetting($key, $value);
	}
	
	/**
	 * Get the configuration settings
	 *
	 * @param string $key The configuration key
	 * @return mixed
	 * @access public
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function getSetting($key) {
	    self::trace('getSetting("'.$key.'")', __LINE__);
	    $setting = self::$configureInstance->getSetting($key);
	    self::trace('<em>getSetting() Returning</em> - '.$setting, __LINE__);
	    return $setting;
	}
	
	/**
	 * Get the configuration directory
	 *
	 * @param string $dir The directory your looking for
	 * @param boolean $forRequire is this going to be used for a require statement,  it will replace directory seprator if so
	 * @return string
	 * @access public
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function getDirectory($dir, $forRequire = false) {
	    self::trace('getDirectory("'.$dir.'")', __LINE__);
	    $directory = self::$configureInstance->getDirectory($dir, $forRequire);
	    self::trace('<em>getDirectory() Returning</em> - '.$directory, __LINE__);
	    return $directory;
	}
	
	/**
	 * spits out the complete backtrace and exits the code.
	 *
	 * @return void
	 * @access public
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function debug() {
	    self::$loggingInstance->errorHandler();
	}
	
	/**
	 * Begins the process of handling the requested page.  Must be called after all settings or it will use default directories
	 *
	 * @param string $requestedUrl the url that is eing requested.  It is passed in $_GET['url]
	 * @return void
	 * @access public
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function processRequest($requestedUrl) {
	    self::trace('Starting processRequest("'.$requestedUrl.'")', __LINE__);
	    /**
	     * Run SPYC to get the current settings file
	     *
	     * @author Johnathan Pulos
	     */
	    self::$configureInstance->setSPYCSettings();
	    /**
	     * Start determining the current page by passing the request to the Routing
	     *
	     * @author Johnathan Pulos
	     */
	    self::$routingInstance->SPYCSettings = self::$configureInstance->SPYCSettings;
	    $currentPage = self::$routingInstance->getCurrentPage($requestedUrl);
	    $this->setSetting('current_page', $currentPage);
	     /**
	      * Get the specific settings by setting them in the Configure class
	      *
	      * @author Johnathan Pulos
	      */
	    self::$configureInstance->initGlobalSettings();
	    self::$configureInstance->initPageSettings();
	    /**
	     * Check caching,  if on then deliver necessary file, or start up the caching
	     *
	     * @author Johnathan Pulos
	     */
	    self::$cachingInstance->processRequest();
	    self::$templatingInstance->processRequest();
     	self::trace('Completing processRequest()', __LINE__);
	}
	
	/**
	 * Render the requested page.  This should be called last after all modules and pageinfo has been defined.
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function renderRequest() {
	    self::trace('Starting renderRequest()', __LINE__);
	    self::$templatingInstance->completeRequest();
	     /**
	      * Do any final clean up in the caching engine
	      *
	      * @author Johnathan Pulos
	      */
	    self::$cachingInstance->completeRequest();
	    self::trace('Completing renderRequest()', __LINE__);
	}
	
	/**
	 * Set a template tag for any view
	 *
	 * @param string $tag reference for this specific variable
	 * @param string $value value for this variable 
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function setTemplateTag($tag, $value) {
	    self::trace('Starting setTemplateTag("'.$tag.'", "'.htmlspecialchars($value).'")', __LINE__);
	    self::$templatingInstance->setATemplateTag($tag, $value);
	    self::trace('Completing setTemplateTag()', __LINE__);
	}
	
	/**
	 * Sets multiple template tags based on a supplied array formated like array($tag => $value)
	 *
	 * @param string $arrayOfTags 
	 * @param string $prefix 
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function setTemplateTagsWithArray($arrayOfTags, $prefix = '') {
	    self::trace('Starting setTemplateTagsWithArray("'.var_export($arrayOfTags,true).'", "'.htmlspecialchars($prefix).'")', __LINE__);
	    $tags = array();
	    foreach($arrayOfTags as $key => $val)
		{
			$title = $prefix . "" . $key;
			$tags = array_merge($tags,array($title => $val));
		}
		self::$templatingInstance->setTemplateTagsWithArray($tags);
		self::trace('Completing setTemplateTagsWithArray()', __LINE__);
	}
	
	/**
	 * convienence method for logging traces
	 *
	 * @param string $message message to add to stack 
	 * @return void
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function trace($message, $line = '') {
	    self::$loggingInstance->logTrace('<strong>strutsEngine (line# '.$line.')</strong>: '.$message);
	}
	
	/**
	 * Deprecated Method
	 *
	 * @var	string	$tag	reference of the variable (##variable##)
	 * @var	string	$value	the value you want to set the variable to	
	 * @return Boolean
	 * 
	 * @access	public
	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setLayoutVar($tag, $value){
		trigger_error('<strong>Deprecated Method strutsEngine::setLayoutVar($tag, $value)</strong> please use strutsEngine::setTemplateTag($tag, $value)', E_USER_WARNING);
		$this->setTemplateTag($tag, $value);
		return true;
	}
	
	/**
	 * Deprecated Method
	 * 
	 * All variables can be displayed by adding ##tag## in the page design file.
	 *
	 * @var string $tag reference for this specific variable
	 * @var string $value value for this variable
	 * 
	 * @return Boolean
	 * 
	 * @access	public
	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setPageVar($tag, $value){
    	trigger_error('<strong>Deprecated Method strutsEngine::setLayoutVar($tag, $value)</strong> please use strutsEngine::setTemplateTag($tag, $value)', E_USER_WARNING);
		$this->setTemplateTag($tag, $value);
		return true;
	}
	
	/**
	 * Deprecated
	 *
	 * @var	array 	$layout_vars	an array of variables set up like array($tag => $value)	
	 * @var	string	$prefix			prefix to append to all references of the variable (Helps to protect from overwriting other variables)
	 * @return Boolean
	 * 
	 * @access	public
	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setLayoutVarFromArray($layout_vars, $prefix = ''){
        trigger_error('<strong>Deprecated Method strutsEngine::setLayoutVarFromArray($layout_vars, $prefix)</strong> please use strutsEngine::setTemplateTagsWithArray($layout_vars, $prefix)', E_USER_WARNING);
        $this->setTemplateTagsWithArray($layout_vars, $prefix);
		return true;
	}
	
	/**
	 * Deprecated
	 *
	 * @var	array 	$page_vars	An array of page variables
	 * @var	string	$prefix		prefix to append to all references of the variable (Helps to protect from overwriting other variables)
	 * 
	 * @return Boolean
	 * 
	 * @access	public
	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setPageVarFromArray($page_vars, $prefix = '') {
        trigger_error('<strong>Deprecated Method strutsEngine::setPageVarFromArray($layout_vars, $prefix)</strong> please use strutsEngine::setTemplateTagsWithArray($layout_vars, $prefix)', E_USER_WARNING);
        $this->setTemplateTagsWithArray($page_vars, $prefix);
		return true;
	}
	
	/**
	 * Deprecated
	 * 
	 * @var	string	$js_files			an array of javascript files with extension
	 * @var	string	$directory			optional directory for files
	 * @var	Boolean	$compress			should we set the url to a compression file ($js_compress_dir/$cache_js/$files_to_compress (commas seperated))
	 * @var	String	$js_compress_dir	location of the javascript compression file
	 * @var	Boolean	$cache_js			Do you want the Javascript files cached	
	 *
	 * @return Boolean
	 * 
	 * @access	public
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setLayoutJavascriptFromArray($js_files, $directory = null) {
        trigger_error('<strong>Deprecated Method strutsEngine::setLayoutJavascriptFromArray($js_files, $directory)</strong>', E_USER_WARNING);
        return true;
	}
	
	/**
	 * Deprecated
	 *
	 * @param array $sitewide_js_files sitewide js file names
	 * @param array $page_js_files page specific file names
	 * @param string $directory directory where compressed files are stored
	 * @param string $page_url the current page url
	 * @return boolean
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function setLayoutJSWithCompression($sitewide_js_files, $page_js_files, $directory, $page_url) {
		trigger_error('<strong>Deprecated Method strutsEngine::setLayoutJSWithCompression($sitewide_js_files, $page_js_files, $directory, $page_url)</strong>', E_USER_WARNING);
		return true;
	}
	
	/**
	 * Deprecated
	 *
	 * @param array $curr_files array of current files
	 * @param string $new_filename the new filename
	 * @param string $directory path to compressed directory
	 * @return string
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function processJSFilesForCompression($curr_files, $new_filename, $directory) {
	    trigger_error('<strong>Deprecated Method strutsEngine::processJSFilesForCompression($curr_files, $new_filename, $directory)</strong>', E_USER_WARNING);
		return '';
	}
	
	
	
	
	/**
	 * Deprecated
	 * 
	 * @var	string	$css_files			an array of CSS files with extension
	 * @var	string	$directory			optional directory for files
	 * @var	Boolean	$compress			should we set the url to a compression file ($css_compress_dir/$cache_css/$files_to_compress (commas seperated))
	 * @var	String	$css_compress_dir	location of the CSS compression file
	 * @var	Boolean	$cache_css			Do you want the CSS files cached	
	 *
	 * @return Boolean
 	 * @deprecated
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setLayoutCSSFromArray($css_files, $directory = null) {
    	trigger_error('<strong>Deprecated Method strutsEngine::setLayoutCSSFromArray($css_files, $directory)</strong>', E_USER_WARNING);
		return true;
	}
	
	/**
	 * Deprecated
	 *
	 * @param array $sitewide_css_files sitewide css file names
	 * @param array $page_css_files page specific file names
	 * @param string $directory directory where compressed files are stored
	 * @param string $page_url the current page url
	 * @return boolean
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function setLayoutCSSWithCompression($sitewide_css_files, $page_css_files, $directory, $page_url) {
		trigger_error('<strong>Deprecated Method strutsEngine::setLayoutCSSWithCompression($sitewide_css_files, $page_css_files, $directory, $page_url)</strong>', E_USER_WARNING);
        return true;
	}
	
	/**
	 * Deprecated
	 *
	 * @param array $curr_files array of current files
	 * @param string $new_filename the new filename
	 * @param string $directory path to compressed directory
	 * @return string
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function processCSSFilesForCompression($curr_files, $new_filename, $directory) {
		trigger_error('<strong>Deprecated Method strutsEngine::processCSSFilesForCompression($curr_files, $new_filename, $directory)</strong>', E_USER_WARNING);
        return '';
	}
	
	/**
	 * Deprecated
	 *
	 * @param string $process_url the url for compressing the files
	 * @param array $files array of filenames
	 * @param string $directory the directory to place the files
	 * @param string $filename the final file name 
	 * @return void
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function createCompressedFile($process_url, $files, $directory, $filename) {
    	trigger_error('<strong>Deprecated Method strutsEngine::createCompressedFile($process_url, $files, $directory, $filename)</strong>', E_USER_WARNING);
	}
	
	/**
	 * Deprecated
	 *
	 * @param string $page_url the current page url
	 * @return string
	 * @access private
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function getCompressedFileName($page_url) {
        trigger_error('<strong>Deprecated Method strutsEngine::getCompressedFileName($page_url)</strong>', E_USER_WARNING);
        return '';
	}
	
	/**
	 * Deprecated
	 *
	 * @return Boolean
	 * 
	 * @access	public
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function printlayoutVars() {
        trigger_error('<strong>Deprecated Method strutsEngine::printlayoutVars()</strong>', E_USER_WARNING);
		return true;
	}
	
	/**
	 * Deprecated
	 *
	 * @return Boolean
	 * 
	 * @access	public
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function emptyLayoutVars() {
        trigger_error('<strong>Deprecated Method strutsEngine::emptyLayoutVars()</strong>', E_USER_WARNING);
		return true;
	}
	
	/**
	 * Deprecated
	 * @return Boolean
	 * 
	 * @access	public
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function printPageVars() {
        trigger_error('<strong>Deprecated Method strutsEngine::printPageVars()</strong>', E_USER_WARNING);
		return true;
	}
	
	/**
	 * Deprecated
	 *
	 * @return Boolean
	 * 
	 * @access	public
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function emptyPageVars() {
        trigger_error('<strong>Deprecated Method strutsEngine::emptyPageVars()</strong>', E_USER_WARNING);
		return true;
	}
	
	/**
	 * Deprecated
	 *
	 * @var	string	$file	The page specific design file
	 * @return Boolean
	 * 
	 * @access	public
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setPageElement($file) {
        trigger_error('<strong>Deprecated Method strutsEngine::setPageElement($file)</strong>', E_USER_WARNING);
		return true;
	}
	
	/**
	 * Deprecated
	 *
	 * @var	string	$file	The page specific design file
	 * @return Boolean
	 * 
	 * @access	public
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setLayoutElement($file) {
        trigger_error('<strong>Deprecated Method strutsEngine::setLayoutElement($file)</strong>', E_USER_WARNING);
		return true;
	}
	
	/**
	 * Deprecated
	 *
	 * @return string $this->strutTemplate the final layout with all tags replaced with the variable values
	 * 
	 * @access	public
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function renderLayout() {
        trigger_error('<strong>Deprecated Method strutsEngine::renderLayout()</strong>', E_USER_WARNING);
        return '';
	}
	
	/**
	 * Deprecated
	 *
	 * @var	string	$file	the file to prepare
	 * @return string	the prepared file
	 * 
	 * @access	private	
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	private function prepareFile($file) {
		trigger_error('<strong>Deprecated Method strutsEngine::prepareFile($file)</strong>', E_USER_WARNING);
		return '';
	}
	
	/**
	 * Deprecated
	 *
	 * @var	array 	$vars		The variables for the specific file setup like array($tag => $value)
	 * @var	string	$file_code	The prepared file code
	 * 
	 * @return string	the file_code with all variables set to the correct values
	 * 
	 * @access private
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	private function replaceAllVars($vars, $file_code) {
        trigger_error('<strong>Deprecated Method strutsEngine::replaceAllVars($vars, $file_code)</strong>', E_USER_WARNING);
		return '';
	}
	
	/**
	 * Deprecated
	 *
	 * @param string $path path to check
	 * @return string
 	 * @deprecated
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function stripFirstSlashInPath($path) {
        trigger_error('<strong>Deprecated Method strutsEngine::stripFirstSlashInPath($path)</strong>', E_USER_WARNING);
        return '';
	}
	
	public function arrayMethod() {
	    $newArray = array();
	    $newArray['test'] = true;
	    return $newArray;
	}
	
}// END class 
?>