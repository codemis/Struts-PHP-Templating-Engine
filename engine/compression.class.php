<?php
/**
 * This class handles the compression of the Javascript and CSS files.  It does not preform the compression, but rather triggers the compression using cURL.  Compression is 
 * implemented in the css.php and js.php files located with the css and javascript folders respectively.
 *
 * @package STUTS
 * @author Technoguru Aka. Johnathan Pulos
 */
class Compression
{
    /**
	 * The singleton instance of the compression class
	 *
	 * @var Object
	 * @access private
	 */
	private static $compressionInstance;
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
        trigger_error('Cloning the Compression is not permitted.', E_USER_ERROR);
    }
	
	/**
	 * setup the Compression class
	 *
	 * @return object
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function init() { 
        if (!self::$compressionInstance) { 
            self::$compressionInstance = new Compression(); 
        }
        return self::$compressionInstance;
	} 
	
	/**
	 * Compress the site wide and page specific Javascript files.  Returns an array with the sitewitde compressed javascript file url, and the page specific compressed javascript file url
	 *
	 * @return array
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function compressJavascript() {
	    self::trace('Starting compressJavascript()', __LINE__);
	    $javascript = array();
    	$globalSettings = $this->configureInstance->getSetting('global_settings');
    	$pageSettings = $this->configureInstance->getSetting('page_settings');
    	$sitewideCompressedFilename = $this->configureInstance->getSetting('sitewide_compressed_filename');
    	$jsDirectory = $this->configureInstance->getDirectory('js', false);
    	if($globalSettings != '') {
    	    $fileUrl = $jsDirectory . '/' . $sitewideCompressedFilename . '.js';
    	    $filePath = APP_PATH . str_replace('/', DS, $globalSettings['js_compress_directory']) . $sitewideCompressedFilename . '.js';
    	    $finalJsUrl = $this->resourceFileExistsOrCreate($fileUrl, $filePath, $globalSettings['javascript'], 'js');
    	    array_push($javascript, $finalJsUrl);
    	}
    	if($pageSettings != '') {
            $pageJsName = $this->getPageCompressionFileName() . "_scripts.min.js";
    	    $fileUrl = $jsDirectory . '/' . $pageJsName;
    	    $filePath = APP_PATH . str_replace('/', DS, $globalSettings['js_compress_directory']) . $pageJsName;
    	    $finalJsUrl = $this->resourceFileExistsOrCreate($fileUrl, $filePath, $pageSettings['javascript'], 'js');
    	    array_push($javascript, $finalJsUrl);
    	}
        self::trace('Completing compressJavascript() : returning ' . var_export($javascript, true), __LINE__);
        return $javascript;
	}
	
	/**
	 * Compress the site wide and page specific CSS files.  Returns an array with the sitewitde compressed css file url, and the page specific compressed css file url
	 *
	 * @return array
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function compressCSS() {
	    self::trace('Starting compressCSS()', __LINE__);
	    $css = array();
    	$globalSettings = $this->configureInstance->getSetting('global_settings');
    	$pageSettings = $this->configureInstance->getSetting('page_settings');
    	$sitewideCompressedFilename = $this->configureInstance->getSetting('sitewide_compressed_filename');
    	$cssDirectory = $this->configureInstance->getDirectory('css', false);
    	if($globalSettings != '') {
    	    $fileUrl = $cssDirectory . '/' . $sitewideCompressedFilename . '.css';
    	    $filePath = APP_PATH . str_replace('/', DS, $globalSettings['css_compress_directory']) . $sitewideCompressedFilename . '.css';
    	    $finalCSSUrl = $this->resourceFileExistsOrCreate($fileUrl, $filePath, $globalSettings['css'], 'css');
    	    array_push($css, $finalCSSUrl);
    	}
    	if($pageSettings != '') {
            $pageCSSName = $this->getPageCompressionFileName() . "_styles.min.css";
    	    $fileUrl = $cssDirectory . '/' . $pageCSSName;
    	    $filePath = APP_PATH . str_replace('/', DS, $globalSettings['css_compress_directory']) . $pageCSSName;
    	    $finalCSSUrl = $this->resourceFileExistsOrCreate($fileUrl, $filePath, $pageSettings['css'], 'css');
    	    array_push($css, $finalCSSUrl);
    	}
        self::trace('Completing compressCSS() : returning ' . var_export($css, true), __LINE__);
        return $css;
	}
	
	/**
	 * Checks if the compressed Javascript file exists,  if it does, it returns the last modified date in the fileUrl.  If it doesn't exist,  it calls the compression, and passes back the new fileUrl
	 * and current date. 
	 *
	 * @param string $fileUrl The url to the compressed file location (ie. design/js/test.min.js)
	 * @param string $filePath The path to the compressed file location from documet root
	 * @param string $files a string of files to compress
	 * @param string $type js/css type of resource you are compressing
	 * @return string
	 * @access private
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function resourceFileExistsOrCreate($fileUrl, $filePath, $files, $type) {
	    $resourceDirectory = $this->configureInstance->getDirectory($type, false);
	    if(file_exists($filePath)) {
	        /**
	         * No need to compress, hand over the file with a datestamp
	         *
	         * @author Technoguru Aka. Johnathan Pulos
	         */
	        $last_modified = date("ymdGis", filemtime($filePath));
	    }else {
	        /**
	         * Run the compression
	         *
	         * @author Technoguru Aka. Johnathan Pulos
	         */
	        $url = DOMAIN . $resourceDirectory . '/' . $type . '.php';
            $url = $url . '?files='.$files.'&final_file='.$filePath;
	        $this->requestCompression($url);
	        $last_modified = date("ymdGis");
	    }
	    return '/' . $fileUrl . "?" . $last_modified;
	}
	
	/**
     * Touches the $url file in order to compress the files together
     *
     * @param string $url the url for compressing the files 
     * @return void
     * @access private
     * @author Technoguru Aka. Johnathan Pulos
     */
	private function requestCompression($url) {
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
	 * generate a unique name for the file based on the current_page configuration setting
	 *
	 * @return string
	 * @access private
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function getPageCompressionFileName() {
	    $currentPage = $this->configureInstance->getSetting('current_page');
	    $filename = str_replace('/','_',$currentPage['page']);
	    $filename = str_replace('-','_',$filename);
    	return $filename;
	}
	
	/**
	 * convienence method for logging traces
	 *
	 * @param string $message message to add to stack 
	 * @return void
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function trace($message, $line = '') {
	    $this->loggingInstance->logTrace('<strong>Compression (line# '.$line.')</strong>: '.$message);
	}
}
?>