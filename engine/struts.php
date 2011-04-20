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
 * @package default
 * @author Technoguru Aka. Johnathan Pulos  (mailto:johnathan@jpulos.com)
 **/
 require_once('configure.class.php');
 require_once('routing.class.php');
 require_once('logging.class.php');
class strutsEngine
{
	/**
	 * The array of variables for the layout
	 *
	 * @var Array
	 * @access private
	 */
	private $layoutVars = array();
	/**
	 * The array of variables for the page
	 *
	 * @var Array
	 * @access private
	 */
	private $pageVars = array();
	/**
	 * The content for the specific page
	 *
	 * @var string
	 * @access private
	 */
	private $strutContents = '';
	/**
	 * The final layout that will be rendered
	 *
	 * @var string
	 * @access private
	 */
	private $strutTemplate = '';
	/**
	 * The HTML format for including javascript files in sprint_f() format
	 *
	 * @var string
	 */
	public $jsFormat = "<script src=\"%s\"></script>\r\n";
	/**
	 * The HTML format for including CSS files in sprint_f() format
	 *
	 * @var string
	 */
	public $cssFormat = "<link rel=\"stylesheet\" href=\"%s\">\r\n";
	/**
	 * url for current site
	 *
	 * @var string
	 */
	public $siteUrl = '';
	/**
	 * a file name for the compressed sitewide css
	 *
	 * @var string
	 */
	public $siteWideFilename = 'sitewide';
	/**
	 * path to css file
	 *
	 * @var string
	 */
	public $CSSDir = 'design/css';
	/**
	 * path to js file
	 *
	 * @var string
	 */
	public $JSDir = 'design/js';
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
        if (!self::$configureInstance) {
            self::trace('Started initializing Configure Class', __LINE__);
             
            self::$configureInstance = Configure::init();
            self::$configureInstance->loggingInstance = self::$loggingInstance;
            
            self::$routingInstance->configureInstance = self::$configureInstance;
            self::$loggingInstance->configureInstance = self::$configureInstance;
            
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
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function readSetting($key) {
	    self::trace('readSetting("'.$key.'")', __LINE__);
	    $setting = self::$configureInstance->getSetting($key);
	    self::trace('<em>readSetting() Returning</em> - '.$setting, __LINE__);
	    return $setting;
	}
	
	/**
	 * spits out the complete backtrace and exits the code.
	 *
	 * @return void
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
	public function handleRequest($requestedUrl) {
	    self::trace('handleRequest("'.$requestedUrl.'")', __LINE__);
	    $currentPage = self::$routingInstance->getCurrentPage($requestedUrl);
	    $this->setSetting('current_page', $currentPage);
	}
	
	/**
	 * Sets an individual variable for the layout file
	 *
	 * @var	string	$tag	reference of the variable (##variable##)
	 * @var	string	$value	the value you want to set the variable to	
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setLayoutVar($tag, $value)
	{
		$this->layoutVars = array_merge($this->layoutVars,array($tag => $value));
		return true;
	}
	
	/**
	 * Sets multiple variables from an array for the layout file
	 *
	 * @var	array 	$layout_vars	an array of variables set up like array($tag => $value)	
	 * @var	string	$prefix			prefix to append to all references of the variable (Helps to protect from overwriting other variables)
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setLayoutVarFromArray($layout_vars, $prefix = '')
	{
		foreach($layout_vars as $key => $val)
		{
			$title = $prefix . "" . $key;
			$this->layoutVars = array_merge($this->layoutVars,array($title => $val));
		}
		return true;
	}
	
	/**
	 * Sets the ##strutJavascript## from an array of javascript filenames
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
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setLayoutJavascriptFromArray($js_files, $directory = null)
	{
		$page_javascript = '';
		$files_to_compress = '';
		if(!empty($js_files)){
			foreach($js_files as $key => $val)
			{
				$page_javascript .= sprintf($this->jsFormat, $directory . '/' . $val);
			}
		}else{
			$page_javascript = '';
		}
		$this->setLayoutVar("strutJavascript", $page_javascript);
		return true;
	}
	
	/**
	 * Compress the layout js.  two files are created, a sitewide and a page specific.  If the files were not modified recently, and exist hand the current files.  Else, compress them.
	 *
	 * @param array $sitewide_js_files sitewide js file names
	 * @param array $page_js_files page specific file names
	 * @param string $directory directory where compressed files are stored
	 * @param string $page_url the current page url
	 * @return boolean
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function setLayoutJSWithCompression($sitewide_js_files, $page_js_files, $directory, $page_url)
	{
		$directory = $this->stripFirstSlashInPath($directory);
		$site_wide_filename = $this->siteWideFilename.'.js';
		$page_filename = $this->getCompressedFileName($page_url)."_scripts.js";
		$js_files = $this->processJSFilesForCompression($sitewide_js_files, $site_wide_filename, $directory);
		$js_files .= $this->processJSFilesForCompression($page_js_files, $page_filename, $directory);
		$this->setLayoutVar("strutJavascript", $js_files);
		return true;
	}
	
	/**
	 * process the given js files.  It first checks if the compressed file exists.  If it does,  then it checks the last modified, and compares to the existing file.  If file does not
	 * exist, or one of the files has been modified,  it recompresses the file.
	 *
	 * @param array $curr_files array of current files
	 * @param string $new_filename the new filename
	 * @param string $directory path to compressed directory
	 * @return string
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function processJSFilesForCompression($curr_files, $new_filename, $directory)
	{
		$process_url = $this->siteUrl . '/'.$this->stripFirstSlashInPath($this->JSDir).'/js.php';
		if(empty($curr_files)) {
			return '';
		}
		if(file_exists($new_filename)) {
			$last_modified = date("ymdGis", filemtime($new_filename));
		}else {
			$this->createCompressedFile($process_url, $curr_files, $directory, $new_filename);
			$last_modified = date("ymdGis");
		}
		return sprintf($this->jsFormat, '/' . $directory . '/' . $new_filename . '?' . $last_modified);
	}
	
	
	
	
	/**
	 * Sets the ##strutCSS## from an array of CSS filenames
	 * 
	 * @var	string	$css_files			an array of CSS files with extension
	 * @var	string	$directory			optional directory for files
	 * @var	Boolean	$compress			should we set the url to a compression file ($css_compress_dir/$cache_css/$files_to_compress (commas seperated))
	 * @var	String	$css_compress_dir	location of the CSS compression file
	 * @var	Boolean	$cache_css			Do you want the CSS files cached	
	 *
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setLayoutCSSFromArray($css_files, $directory = null)
	{
		$page_css = '';
		if(!empty($css_files)){
			foreach($css_files as $key => $val)
			{
				$page_css .= sprintf($this->cssFormat, $directory . '/' . $val);				
			}
		}else{
			$page_css = '';
		}
		$this->setLayoutVar("strutCSS", $page_css);
		return true;
	}
	
	/**
	 * Compress the layout css.  two files are created, a sitewide and a page specific.  If the files were not modified recently, and exist hand the current files.  Else, compress them.
	 *
	 * @param array $sitewide_css_files sitewide css file names
	 * @param array $page_css_files page specific file names
	 * @param string $directory directory where compressed files are stored
	 * @param string $page_url the current page url
	 * @return boolean
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function setLayoutCSSWithCompression($sitewide_css_files, $page_css_files, $directory, $page_url)
	{
		$directory = $this->stripFirstSlashInPath($directory);
		$site_wide_filename = $this->siteWideFilename.'.css';
		$page_filename = $this->getCompressedFileName($page_url)."_styles.css";
		$css_files = $this->processCSSFilesForCompression($sitewide_css_files, $site_wide_filename, $directory);
		$css_files .= $this->processCSSFilesForCompression($page_css_files, $page_filename, $directory);
		$this->setLayoutVar("strutCSS", $css_files);
		return true;
	}
	
	/**
	 * process the given css files.  It first checks if the compressed file exists.  If it does,  then it checks the last modified, and compares to the existing file.  If file does not
	 * exist, or one of the files has been modified,  it recompresses the file.
	 *
	 * @param array $curr_files array of current files
	 * @param string $new_filename the new filename
	 * @param string $directory path to compressed directory
	 * @return string
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function processCSSFilesForCompression($curr_files, $new_filename, $directory)
	{
		$process_url = $this->siteUrl . '/'.$this->stripFirstSlashInPath($this->CSSDir).'/css.php';
		if(empty($curr_files)) {
			return '';
		}
		if(file_exists($new_filename)) {
			$last_modified = date("ymdGis", filemtime($new_filename));
		}else {
			$this->createCompressedFile($process_url, $curr_files, $directory, $new_filename);
			$last_modified = date("ymdGis");
		}
		return sprintf($this->cssFormat, '/' . $directory . '/' . $new_filename . '?' . $last_modified);
	}
	
	/**
	 * Touches the $process_url file in order to compress the files together
	 *
	 * @param string $process_url the url for compressing the files
	 * @param array $files array of filenames
	 * @param string $directory the directory to place the files
	 * @param string $filename the final file name 
	 * @return void
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function createCompressedFile($process_url, $files, $directory, $filename)
	{
		$files = implode(',', $files);
		$url = $process_url . '?files='.$files.'&directory='.$directory.'&filename='.$filename;

		$ch = curl_init();    // initialize curl handle
    curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
    curl_setopt($ch, CURLOPT_TIMEOUT, 3); // times out after 4s
    $cc_result = curl_exec($ch); // run the whole process
    curl_close($ch);
	}
	
	/**
	 * Create a filename for the specific page.
	 *
	 * @param string $page_url the current page url
	 * @return string
	 * @access private
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function getCompressedFileName($page_url)
	{
		$filename = str_replace('-','_',$page_url);
		$filename = str_replace('/','_',$filename);
		return $filename;
	}
	
	/**
	 * A debug function to print out the current set layout variables
	 * 
	 * This function exits code, and displays all the current layout variables set up to where this function is called.
	 *
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function printlayoutVars()
	{
		foreach($this->layoutVars as $key => $val)
		{
			echo $key . " = " . $val . "<br />";
		}
		exit();
		return true;
	}
	
	/**
	 * Unsets all layout variables in $this->layoutVars
	 *
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function emptyLayoutVars()
	{
		unset($this->layoutVars);
		$this->layoutVars = array();
		return true;
	}
	
	/**
	 * Set an individual variable for the page design file
	 * 
	 * All variables can be displayed by adding ##tag## in the page design file.
	 *
	 * @var string $tag reference for this specific variable
	 * @var string $value value for this variable
	 * 
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setPageVar($tag, $value)
	{
		$this->pageVars = array_merge($this->pageVars,array($tag => $value));
		return true;
	}
	
	/**
	 * Sets multiple variables for a page design file based on a supplied array formated like array($tag => $value)
	 *
	 * @var	array 	$page_vars	An array of page variables
	 * @var	string	$prefix		prefix to append to all references of the variable (Helps to protect from overwriting other variables)
	 * 
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setPageVarFromArray($page_vars, $prefix)
	{
		foreach($page_vars as $key => $val)
		{
			$title = $prefix . "" . $key;
			$this->pageVars = array_merge($this->pageVars,array($title => $val));
		}
		return true;
	}
	
	/**
	 * A debug function to print out the current set page variables
	 * 
	 * This function exits code, and displays all the current page specific variables set up to where this function is called.
	 *
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function printPageVars()
	{
		foreach($this->pageVars as $key => $val)
		{
			echo $key . " = " . $val . "<br />";
		}
		exit();
		return true;
	}
	
	/**
	 * Unsets all page specific variables in $this->pageVars
	 *
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function emptyPageVars()
	{
		unset($this->pageVars);
		$this->pageVars = array();
		return true;
	}
	
	/**
	 * Get the page content, and parse it.
	 * 
	 * Searches the supplied page design file, and replaces all ##tag## with the set variable in $this->pageVars
	 * then it puts the content into ##strutContent## layout variable.
	 *
	 * @var	string	$file	The page specific design file
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setPageElement($file)
	{
		$file_code = $this->prepareFile($file);
		$this->strutContents .= utf8_encode($this->replaceAllVars($this->pageVars, $file_code));
		$this->setLayoutVar("strutContent", $this->strutContents);
		return true;
	}
	
	/**
	 * Get the layout content, and parse it.
	 * 
	 * Searches the supplied layout design file, and replaces all ##tag## with the set variable in $this->layoutVars
	 * then it puts the content into $this->strutTemplate for final displaying.
	 *
	 * @var	string	$file	The page specific design file
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setLayoutElement($file)
	{
		$file_code = $this->prepareFile($file);
		$this->strutTemplate .= $this->replaceAllVars($this->layoutVars, $file_code);
		return true;
	}
	
	/**
	 * render the final layout
	 *
	 * @return string $this->strutTemplate the final layout with all tags replaced with the variable values
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function renderLayout()
	{
		return $this->strutTemplate;
	}
	
	/**
	 * Prepares the given file for variable replacment
	 *
	 * @var	string	$file	the file to prepare
	 * @return string	the prepared file
	 * 
	 * @access	private	
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	private function prepareFile($file)
	{
		return implode(file($file),'');
	}
	
	/**
	 * Replaces the tags with the variable values
	 * 
	 * This function itterates through the variables and finds them based on ##tag## in the file.
	 * Then it replaces the tag with the value.  This is the core of the templating engine.
	 *
	 * @var	array 	$vars		The variables for the specific file setup like array($tag => $value)
	 * @var	string	$file_code	The prepared file code
	 * 
	 * @return string	the file_code with all variables set to the correct values
	 * 
	 * @access private
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	private function replaceAllVars($vars, $file_code)
	{
		if(!empty($vars)){
		    foreach($vars as $key => $val)
			{
				$file_code = str_replace("##$key##",$val,$file_code);
			}		
		}
		return $file_code;
	}
	
	/**
	 * strips the first slash in path
	 *
	 * @param string $path path to check
	 * @return string
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function stripFirstSlashInPath($path)
	{
		return (substr($path, 0, 1) == '/') ? substr($path,1) : $path;
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
	
}// END class 
?>