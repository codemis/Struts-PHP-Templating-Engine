<?php
/**
 * Testing for caching.class.php
 *
 * @package STRUTS
 * @author Technoguru Aka. Johnathan Pulos
 */
require_once('../logging.class.php');
require_once('../configure.class.php');
use \Mockery as m;
class LoggingTest extends PHPUnit_Framework_TestCase {
    /**
	 * The singleton instance of the class
	 *
	 * @var Object
	 */
	private static $loggingInstance;
	/**
	 * The instance of the cache Reflection Class
	 *
	 * @var Object
	 */
	private static $loggingReflectionInstance;
	/**
	 * The Mock instance of the Configure class
	 *
	 * @var Object
	 */
	private static $configureMock;   
	
	/**
	 * Setup the testing case
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
    public function setUp() {
        self::$loggingInstance = Logging::init(true);
        /**
          * Setup instances of each object as a mock object
          *
          * @author Johnathan Pulos
          */
        self::$configureMock = m::mock('Configure');
        self::$configureMock->shouldReceive('getSetting');
        self::$loggingInstance = '';
        self::$loggingInstance = Logging::init(true);
        self::$loggingInstance->configureInstance = self::$configureMock;
        self::$loggingReflectionInstance = new ReflectionClass('Logging');
    }
    
    /**
     * Add a message to the stack trace
     *
     * @param string $message message to add
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function addToStacktrace($message) {
        $method = self::$loggingReflectionInstance->getMethod("logTrace");
        $method->invoke(self::$loggingInstance, $message);
    }
    
    /**
     * get the current stacktrace
     *
     * @return array
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function getStacktrace() {
        $stack_trace_prop = self::$loggingReflectionInstance->getProperty("stacktrace");
        $stack_trace_prop->setAccessible(true);
        return $stack_trace_prop->getValue(self::$loggingInstance);
    }
    
    /**
     * tear down the stacktrace by resetting its value
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function tearDownStacktrace() {
        $stack_trace_prop = self::$loggingReflectionInstance->getProperty("stacktrace");
        $stack_trace_prop->setAccessible(true);
        $stack_trace_prop->setValue(self::$loggingInstance, array());
    }
    
    /**
     * Complete the testing case
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function tearDown() {
        self::$loggingInstance = '';
        self::$loggingReflectionInstance = '';
        self::$configureMock = '';
        m::close();
    }
    
    /**
     * You should not be able to construct this class.  You should use the init() method
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testConstructShouldBePrivate() {
        $method = self::$loggingReflectionInstance->getMethod("__construct");
        $this->assertTrue($method->isPrivate());
    }
    
    /**
     * __clone should trigger an error since we adopted a singleton pattern
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testCloneShouldTriggerError() {
        try {
            $method = self::$loggingReflectionInstance->getMethod("__clone");
            $method->invoke(self::$loggingInstance);
        }catch (PHPUnit_Framework_Error $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * this class should have an instance for the Configure Class
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldHaveConfigureInstanceObject() {
        $property = self::$loggingReflectionInstance->getProperty("configureInstance");
        $this->assertTrue(is_object($property));
    }
    
    /**
     * logTrace() should add a message to the top of the stack trace
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldAddToStackTraceOnLogTrace() {
        $expectedMessage = "Cows are lovely.";
        $this->addToStacktrace($expectedMessage);
        $stack_trace = $this->getStacktrace();
        
        $this->assertContains('Cows are lovely', $stack_trace[0]);
        $this->tearDownStacktrace();
    }
    
    /**
     * getStacktrace() should deliver the accurate array for the current stacktrace
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldGetStacktrace() {
        $this->addToStacktrace('Cows are really Lovely!');
        $expectedValue = $this->getStacktrace();
        
        $method = self::$loggingReflectionInstance->getMethod("getStacktrace");
        $result = $method->invoke(self::$loggingInstance);
        $this->assertEquals($expectedValue, $result);
        $this->tearDownStacktrace();
    }
    
    /**
     * writeToLog() should write to the log file
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShoulWriteToLog() {
        $expectedFileUrl = 'test_log' . DS . 'test.log';
        $stack_trace_prop = self::$loggingReflectionInstance->getProperty("log_file");
        $stack_trace_prop->setAccessible(true);
        $stack_trace_prop->setValue(self::$loggingInstance, 'tests' . DS . $expectedFileUrl);
        
        $method = self::$loggingReflectionInstance->getMethod("writeToLog");
        $method->setAccessible(true);
        $result = $method->invoke(self::$loggingInstance, 'A test log file!');
        
        $this->assertFileExists($expectedFileUrl);
        /**
         * cleanup
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
         unlink($expectedFileUrl);
    }
    
    /**
     * The log file should be truncated once the file has been retained for a wile
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldCleanupLogFiles() {
        $expectedFileUrl = 'test_log' . DS . 'test.log';
        $expectedLines = 0;
        $stack_trace_prop = self::$loggingReflectionInstance->getProperty("log_file");
        $stack_trace_prop->setAccessible(true);
        $stack_trace_prop->setValue(self::$loggingInstance, 'tests' . DS . $expectedFileUrl);
        
        $method = self::$loggingReflectionInstance->getMethod("writeToLog");
        $method->setAccessible(true);
        $result = $method->invoke(self::$loggingInstance, "A test log file! \r\nA test log file! \r\nA test log file! \r\nA test log file! \r\nA test log file!");
        
        $lines = count(file($expectedFileUrl));
        $this->assertNotEquals($lines, $expectedLines);
        /**
         * Sleep so the log will get clensed
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
        sleep(2);
        
        self::$configureMock->shouldReceive('getSetting')->with('retain_logs')->andReturn(1);
        $method = self::$loggingReflectionInstance->getMethod("cleanUpLog");
        $method->setAccessible(true);
        $currentLogFile = APP_PATH . 'engine' . DS . 'tests' . DS . $expectedFileUrl;
        $result = $method->invoke(self::$loggingInstance, $currentLogFile);
        
        $finalLines = count(file($expectedFileUrl));
        $this->assertEquals($finalLines, $expectedLines);
        
        /**
         * cleanup
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
         unlink($expectedFileUrl);
    }
    
}
?>