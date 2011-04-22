<?php
/**
 * Handles caching the pages if it is turned on.
 *
 * @package STRUTS
 * @author Johnathan Pulos
 */
require_once('vendors/functions/recursive_directory_scan.php');
class Caching
{
    /**
   	 * The current page settings
   	 *
   	 * @var array
   	 * @access private
   	 */
    private $currentPage = array();
    /**
   	 * The global settings
   	 *
   	 * @var array
   	 * @access private
   	 */
    private $globalSettings = array();
    /**
	 * The singleton instance of the cache class
	 *
	 * @var Object
	 * @access private
	 */
	private static $cachingInstance;
	/**
	 * The singleton instance of the logging class
	 *
	 * @var Object
	 * @access private
	 */
	public static $loggingInstance;
	/**
	 * The singleton instance of the configure class
	 *
	 * @var Object
	 * @access private
	 */
	public static $configureInstance;
	
	/**
	 * Only allow one instance of this class.  To setup this class use Caching::init()
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
	 * setup the Caching class
	 *
	 * @return object
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function init() { 
        if (!self::$cachingInstance) { 
            self::$cachingInstance = new Caching(); 
        }
        return self::$cachingInstance;
	}
	
	/**
	 * Takes the given request and determines if caching is set,  if so it either starts caching, or it delivers over the cached file.
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function handleRequest() {
        self::trace('Starting handleRequest()', __LINE__);
        $this->currentPage = $this->configureInstance->getSetting('current_page');
        $this->globalSettings = $this->configureInstance->getSetting('global_settings');
        if(empty($this->currentPage)) {
            trigger_error('You must first set the Configure class var current_page before calling this method.', E_USER_ERROR);
        }
        if(empty($this->globalSettings)) {
            trigger_error('You must first set the Configure class var global_settings before calling this method.', E_USER_ERROR);
        }
        if($this->currentPage['page'] == 'clear_cache') {
            $this->clearCache();
            trigger_error('The cache has been cleared.', E_USER_ERROR);
        }
        if($this->globalSettings['enable_caching'] === true) {
            $pageSettings = $this->configureInstance->getSetting('page_settings');
            if($pageSettings['cache'] === true) {
                self::trace('Caching is on!', __LINE__);
                if($this->deliverPage()) {
                    exit;
                }else {
                    $this->startCaching();
                }
            }
        }
	}
	
	/**
	 * Clear all cache files, including min files so that we can reset everything
	 *
	 * @return void
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function clearCache() {
	    self::trace('Starting clearCache()', __LINE__);
	    /**
		 * remove CSS temp files
		 * 
		 *
		 * @author Technoguru Aka. Johnathan Pulos
		 */
		 $this->recursivelyRemove(APP_PATH . $this->globalSettings['css_compress_directory'], 'css');
		/**
		 * remove Javascript temp files
		 *
		 * @author Technoguru Aka. Johnathan Pulos
		 */
		$this->recursivelyRemove(APP_PATH . $this->globalSettings['js_compress_directory'], 'js');
		/**
		 * Remove all temp files
		 *
		 * @author Technoguru Aka. Johnathan Pulos
		 */
		$this->recursivelyRemove(APP_PATH . $this->configureInstance->getDirectory('cache'), $this->configureInstance->getSetting('cache_ext'));
	    self::trace('Completing clearCache()', __LINE__);
	}
	
	/**
	 * Checks if the current cache file exists, and compares its creation time with the current time, the either delivers the page, or returns false.
	 *
	 * @return boolean
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function deliverPage() {
	    self::trace('Starting deliverPage()', __LINE__);
		$cachefile = $this->getCacheFileLocation();
		if(!file_exists($cachefile)) {
        	self::trace('Completing deliverPage() - Returning false', __LINE__);
		    return false;
		}
		$cache_time = $this->configureInstance->getSetting('cache_time');
        /**
         * Get the time the file was created
         *
         * @author Johnathan Pulos
         */
		$cachefile_created = (@file_exists($cachefile)) ? @filemtime($cachefile) : 0; 
		@clearstatcache(); 
        /**
         * If the $cachefile_created is less then the set $cache_time then load the file directly and exit() the code
         *
         * @author Johnathan Pulos
         */
		if (time() > $cachefile_created + $cache_time) {
			@readfile($cachefile);  
			return true;
		}else {
        	self::trace('Completing deliverPage() - Returning false', __LINE__);
		    return false;
		}
	}
	
	/**
	 * Start the page caching
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	private function startCaching() {
	    self::trace('Starting startCaching()', __LINE__);
	    ob_start();
	    self::trace('Completing startCaching()', __LINE__);
	}
	
	private function finalizeCaching() {
	}
	
	/**
	 * Get the file name and location for the cached file
	 *
	 * @return string
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function getCacheFileLocation() {
	    return APP_PATH . $this->configureInstance->getDirectory('cache') . md5($this->currentPage['page_file']) . '.' . $this->configureInstance->getSetting('cache_ext');
	}
	
	/**
	 * Recursively remove all files with a set extension
	 *
	 * @param string $directory directory ti recursively scan
	 * @param string $extension extension to delete
	 * @return void
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function recursivelyRemove($directory, $extension) {
	    self::trace('Starting recursivelyRemove("'.$directory.'","'.$extension.'")', __LINE__);
	    $directoryResults = scan_directory_recursively($directory);
		foreach($directoryResults as $tempFile) {
			if($tempFile['extension'] == $extension){
			    self::trace('<strong>Deleting: '.$tempFile['path'].'</strong>', __LINE__);
				unlink($tempFile['path']);
			}
		}
		self::trace('Completing recursivelyRemove()', __LINE__);
	}
	
	/**
	 * convienence method for logging traces
	 *
	 * @param string $message message to add to stack 
	 * @return void
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function trace($message, $line = '') {
	    $this->loggingInstance->logTrace('<strong>Caching (line# '.$line.')</strong>: '.$message);
	}
}
?>