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
     * An array holding specific information about the current page.
     *
     * @var array
     */
    private $currentPage = array();
    /**
	 * The singleton instance of the routing class
	 *
	 * @var Object
	 */
	private static $routingInstance;
	/**
	 * The singleton instance of the configure class
	 *
	 * @var Object
	 */
	public static $configureInstance;
	/**
	 * The singleton instance of the logging class
	 *
	 * @var Object
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
	    self::trace('Starting Routing::getCurrentPage(), $requestedUrl = "'.$requestedUrl.'"', __LINE__);
	    $this->currentPage['request'] = $requestedUrl;
	    $this->currentPage['page'] = $this->getRequestedPage();
	    $pages_code_directory = $this->configureInstance->getDirectory('pages_code');
	    $pages_directory = $this->configureInstance->getDirectory('pages');
	    if(!empty($this->currentPage['page'])){
            $this->currentPage['page_file'] = $pages_directory . '/' . $this->currentPage['page'];
            $this->currentPage['php_file'] = $pages_code_directory . '/' . $this->currentPage['page'];
    	}else{
            $this->currentPage['page_file'] = $pages_directory;
            $this->currentPage['php_file'] = $pages_code_directory;
    	}
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
	    $requestedUrl = $this->currentPage['request'];
	    $requestedPage = '';
	    if(isset($requestedUrl)) {
	        $requestedPage = trim($requestedUrl);
	    }else {
	        $requestedPage = 'index.html'; 
	    }
        /**
         * If an extension exists on $page_url then remove it
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
    	if (strpos($requestedPage, '.') != false) {
    	    $requestedPage = substr($requestedPage, 0, strrpos($requestedPage, '.')); 
    	}	
	    return $requestedPage;
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