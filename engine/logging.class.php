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
     * An array holding each step completed in the backtrace
     *
     * @var array
     */
    private $backtrace = array();
    /**
	 * The singleton instance of the routing class
	 *
	 * @var Object
	 */
	private static $loggingInstance;
	
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
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function logTrace($trace) {
	    array_unshift($this->backtrace, $trace.' @ '.date("d/m/y : H:i:s", time()));
	}
	
	/**
	 * Get the current backtrace.
	 *
	 * @return array
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function getBacktrace() {
	    return $this->backtrace;
	}
	
	/**
	 * custom error handling that adds a backtrace to the error.
	 *
	 * @return void
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function errorHandler($errno, $errstr, $errfile, $errline) {
	    $backtrace = $this->backtrace;
	    if (!(error_reporting() & $errno)) {
            /**
             * This error code is not included in error_reporting
             *
             * @author Technoguru Aka. Johnathan Pulos
             */
            return;
        }
        switch ($errno) {
            case E_USER_ERROR:
                echo "<strong>PHP error (Line #$errline code: #$errno)</strong>: $errstr<br>";
                echo "<h3>STRUTS PHP Backtrace</h3>";
                foreach($backtrace as $trace) {
        	        echo $trace . '<br>';
        	    }
                exit(1);
                break;
            case E_USER_WARNING:
                echo "<strong>PHP Warning (Line #$errline code: #$errno)</strong>: $errstr<br>";
                break;
            case E_USER_NOTICE:
                echo "<strong>PHP Notice (Line #$errline code: #$errno)</strong>: $errstr<br>";
                break;
            default:
                echo "<strong>Unknown error type (Line #$errline code: #$errno)</strong>: $errstr<br>";
                break;
        }
	    return true;
	}
	
}
?>