<?php
/**
 * This class handles the logging of various information that can be easily displayed for easier access later.
 *
 * @package STRUTS
 * @author Technoguru Aka. Johnathan Pulos
 */
class Logging
{
    /**
     * An array holding each step completed in the stacktrace
     *
     * @var array
     * @access private
     */
    private $stacktrace = array();
    /**
	 * The singleton instance of the routing class
	 *
	 * @var Object
	 * @access private
	 */
	private static $loggingInstance;
	/**
	 * The singleton instance of the configure class
	 *
	 * @var Object
	 * @access public
	 */
	public static $configureInstance;
	/**
	 * An array of templateTags set in the templating class, so they can be printed out in the stack trace
	 *
	 * @var array
	 * @access public
	 */
	public $templateTags = array();
	/**
	 * The location of the log file
	 *
	 * @var string
	 * @access private
	 */
	private $log_file = 'logs/stack_trace.log';

	
	/**
	 * Only allow one instance of this class.  To setup this class use Logging::init()
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
        trigger_error('Cloning the Logging is not permitted.', E_USER_ERROR);
    }
	
	/**
	 * setup the Routing class
	 *
	 * @return object
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function init() { 
        if (!self::$loggingInstance) { 
            self::$loggingInstance = new Logging(); 
        }
        set_error_handler(array(self::$loggingInstance, 'errorHandler'));
        return self::$loggingInstance;
	} 
	
	/**
	 * Add to the stack of the backtrace.  It puts the new trace on the top of the stack
	 *
	 * @param string $trace The trace message to stack
	 * @return void
	 * @access public
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function logTrace($trace) {
	    array_unshift($this->stacktrace, $trace.' @ '.date("d/m/y : H:i:s", time()));
	}
	
	/**
	 * Get the current backtrace.
	 *
	 * @return array
	 * @access public
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function getStacktrace() {
	    return $this->stacktrace;
	}
	
	/**
	 * custom error handling that adds a backtrace to the error.
 	 *
 	 * @param string $errno PHP error number
 	 * @param string $errstr PHP error string
 	 * @param string $errfile PHP error file name
 	 * @param string $errline PHP error line number
	 * @return boolean
	 * @access public
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function errorHandler($errno, $errstr, $errfile, $errline) {
	    if (!(error_reporting() & $errno)) {
            /**
             * This error code is not included in error_reporting
             *
             * @author Technoguru Aka. Johnathan Pulos
             */
            return;
        }
        $this->displayError($errno, $errstr, $errfile, $errline);
	    return true;
	}
	
	/**
	 * Write to the log file
	 *
	 * @param string $message the message to write to the log
	 * @return void
	 * @access private
	 * @author Technoguru Aka. Johnathan Pulos
	 */
    private function writeToLog($message){
        $log_file = APP_PATH . 'engine' . DS . str_replace('/', DS, $this->log_file);
        $this->cleanUpLog($log_file);
        /**
         * open log file for writing only; place the file pointer at the end of the file 
         * if the file does not exist, attempt to create it
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
        $fp = fopen($log_file, 'a+') or exit("Unable to open the log file @ ".$log_file);
        /**
         * Define the script name
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
        fwrite($fp, "$message\n");
        fclose($fp);
    }
    
    /**
     * Checks if the log file has been retain tool long based on retain_logs setting, and truncates it
     *
     * @param string $logFile 
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    private function cleanUpLog($logFile) {
        if(file_exists($logFile)) {
            $retainLogTime = $this->configureInstance->getSetting('retain_logs');
            $logCreated = @filemtime($logFile);
            if(time() > $logCreated+$retainLogTime) {
                /**
                 * truncate the log file
                 *
                 * @author Johnathan Pulos
                 */
                $fh = fopen($logFile, 'w');
                fclose($fh);
            } 
        }
    }
	
	/**
	 * Display the error to the current view
	 *
	 * @param string $errno PHP error number
	 * @param string $errstr PHP error string
	 * @param string $errfile PHP error file name
	 * @param string $errline PHP error line number
	 * @return void
	 * @access private
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function displayError($errno, $errstr, $errfile, $errline) {
	    $message = "";
	    switch ($errno) {
            case E_USER_ERROR:
                $message = "<strong>PHP error (In $errfile Line #$errline code: #$errno)</strong>: $errstr<br>";
                $message .= "<h3>STRUTS PHP Stack Trace</h3>";
                $stacktrace = $this->stacktrace;
                foreach($stacktrace as $trace) {
        	        $message .= $trace . '<br>';
        	    }
        	    if (!empty($this->templateTags)) {
        	        $message .= "<h3>STRUTS PHP Available Tags</h3>";
        	        foreach($this->templateTags as $key => $val) {
            	        $message .= '##' . $key . '## = "' . htmlspecialchars($val) . '"<br>';
            	    }
        	    }
                break;
            case E_USER_WARNING:
                $message .= "<strong>PHP Warning (In $errfile Line #$errline code: #$errno line: #$errline)</strong>: $errstr<br>";
                break;
            case E_USER_NOTICE:
                $message .= "<strong>PHP Notice (In $errfile Line #$errline code: #$errno line: #$errline)</strong>: $errstr<br>";
                break;
            default:
                $message .= "<strong>Unknown error type (In $errfile $errfile code: #$errno line: #$errline)</strong>: $errstr<br>";
                break;
        }
        $debug_level = $this->configureInstance->getSetting('debug_level');
        if($debug_level == 3 || $debug_level == 2 || $debug_level == 1) {
            $this->writeToLog(strip_tags(str_replace("<br>", "\n", $message)));
        }
        if($debug_level == 4 || $debug_level == 3) {
            echo $message;
            if($errno == E_USER_ERROR) {
                /**
                 * Fatal Error
                 * 
                 *
                 * @author Technoguru Aka. Johnathan Pulos
                 */
                exit(1);
            }
        }
	}
	
}
?>