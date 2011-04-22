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
            trigger_error('The cache hs been cleared.', E_USER_ERROR);
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