<?php
/**
 * Handles all the routing.  It determines the page selected based on the URL, and sets the required parameters.
 *
 * @package STRUTS
 * @todo Create ability to define custom routing
 * @author Technoguru Aka. Johnathan Pulos
 */
class Routing
{
    /**
     * The current settings array parsed from the settings yaml file
     *
     * @var array
     * @access public
     */
    public $SPYCSettings = array();
    /**
     * An array holding specific information about the current page.
     *
     * @var array
     * @access private
     */
    private $currentPage = array();
    /**
     * An array of custom urls that are used specifically in the code, but do not have a setting in the YML.  It will bypass settings check for these urls.
     *
     * @var array
     */
    private $customByPassUrls = array('clear_cache');
    /**
	 * The singleton instance of the routing class
	 *
	 * @var Object
	 * @access public
	 */
	private static $routingInstance;
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
	 * Only allow one instance of this class.  To setup this class use Routing::init()
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
        if (!self::$routingInstance) { 
            self::$routingInstance = new Routing(); 
        }
        return self::$routingInstance;
	}
	
	/**
	 * Sets up the current page array based on the requested url.
	 *
	 * @param string $requestedUrl the requested url
	 * @return array
	 * @access public
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function getCurrentPage($requestedUrl) {
        if(empty($this->SPYCSettings)) {
            trigger_error('You must first set the class var SPYCSettings before calling this method.', E_USER_ERROR);
        }
	    self::trace('Starting getCurrentPage("'.$requestedUrl.'")', __LINE__);
	    $this->currentPage['request'] = $requestedUrl;
	    $this->currentPage['page'] = $this->getRequestedPage();
	    $pages_code_directory = $this->configureInstance->getDirectory('pages_code');
	    $pages_directory = $this->configureInstance->getDirectory('pages');
	    if(!empty($this->currentPage['page']) && (!in_array($this->currentPage['page'], $this->customByPassUrls))){
	        $this->currentPage['file_name'] = $this->getFileName();
            $this->currentPage['page_file'] = $pages_directory . '/' . $this->currentPage['file_name'] . '.html';
            $this->currentPage['php_file'] = $pages_code_directory . '/' . $this->currentPage['file_name'] . '.php';
    	}else{
            $this->currentPage['page_file'] = $pages_directory;
            $this->currentPage['php_file'] = $pages_code_directory;
    	}
        self::trace('<em>getCurrentPage() Returning</em> - '.var_export($this->currentPage, true), __LINE__);
	    return $this->currentPage;
	}
	
	/**
	 * Takes the requested url and figures out what page is specifically being requested.
	 * 
	 * @return string
	 * @access private
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function getRequestedPage() {
	    self::trace('Starting getRequestedPage()', __LINE__);
	    $requestedUrl = $this->currentPage['request'];
	    $requestedPage = '';
	    if(isset($requestedUrl)) {
	        $requestedPage = trim($requestedUrl);
	    }else {
	        $requestedPage = 'index'; 
	    }
	    /**
    	 * If they append a index.html,  see if it is valid, if not remove it
    	 * @author Johnathan Pulos
    	 */
    	if (strpos($requestedPage, 'index.html') != false) {
    	    if(!array_key_exists($requestedPage, $this->SPYCSettings)) {
    			$requestedPage = substr($requestedPage, 0, strrpos($requestedPage, '/index.html')); 
    		}
    	}
    	/**
         * If an extension exists on $page_url then remove it
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
    	if (strpos($requestedPage, '.') != false) {
    	    $requestedPage = substr($requestedPage, 0, strrpos($requestedPage, '.')); 
    	}
    	/**
    	 * Handle 404 errors
    	 *
    	 * @author Technoguru Aka. Johnathan Pulos
    	 */
     	if (isset($requestedPage) && !array_key_exists($requestedPage, $this->SPYCSettings) && (!in_array($requestedPage, $this->customByPassUrls))) {
     	    $requestedPage = '404';
     	}
    	self::trace('<em>getRequestedPage() Returning</em> - '.$requestedPage, __LINE__);	
	    return $requestedPage;
	}
	
	/**
	 * If the page is marked as a landing page then it looks for index files else it matches the page name.
	 *
	 * @return string
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function getFileName() {
	    if ($this->SPYCSettings[$this->currentPage['page']]['landing_page'] === true){
    		return 'index';
    	}else {
    	    return $this->currentPage['page'];
    	}
	}
	
	/**
	 * convienence method for logging traces
	 *
	 * @param string $message message to add to stack 
	 * @return void
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function trace($message, $line = '') {
	    $this->loggingInstance->logTrace('<strong>Routing (line# '.$line.')</strong>: '.$message);
	}
}
?>