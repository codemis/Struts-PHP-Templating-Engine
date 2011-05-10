<?php
/**
 * Testing for templating.class.php
 *
 * @package STRUTS
 * @author Technoguru Aka. Johnathan Pulos
 */
require_once('../templating.class.php');
require_once('../logging.class.php');
require_once('../configure.class.php');
require_once('../compression.class.php');
use \Mockery as m;
class TemplatingTest extends PHPUnit_Framework_TestCase {
    /**
	 * The singleton instance of the class
	 *
	 * @var Object
	 */
	private static $templatingInstance;
	/**
	 * The instance of the Reflection Class
	 *
	 * @var Object
	 */
	private static $templatingReflectionInstance;
	/**
	 * The Mock instance of the Configure class
	 *
	 * @var Object
	 */
	private static $configureMock;
	/**
	 * The Mock instance of the Logging class
	 *
	 * @var Object
	 */
	private static $loggingMock;
	/**
	 * The Mock instance of the Compression class
	 *
	 * @var Object
	 */
	private static $compressionMock;
	
	/**
	 * Setup the testing case
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
    public function setUp() {
        self::$templatingInstance = Templating::init();
        /**
          * Setup instances of each object as a mock object
          *
          * @author Johnathan Pulos
          */
        self::$configureMock = m::mock('Configure');
        self::$loggingMock = m::mock('Logging');
        self::$loggingMock->shouldReceive('logTrace');
        self::$compressionMock = m::mock('Compression');
        self::$templatingInstance = '';
        self::$templatingInstance = Templating::init(true);
        self::$templatingInstance->configureInstance = self::$configureMock;
        self::$templatingInstance->loggingInstance = self::$loggingMock;
        self::$templatingInstance->compressionInstance = self::$compressionMock;
        self::$templatingReflectionInstance = new ReflectionClass('Templating');
    }
    
    /**
     * Complete the testing case
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function tearDown() {
        self::$templatingInstance = '';
        self::$templatingReflectionInstance = '';
        self::$loggingMock = '';
        self::$configureMock = '';
        self::$compressionMock = '';
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
        $method = self::$templatingReflectionInstance->getMethod("__construct");
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
            $method = self::$templatingReflectionInstance->getMethod("__clone");
            $method->invoke(self::$templatingInstance);
        }catch (PHPUnit_Framework_Error $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * this class should have an instance for the Logging Class
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldHaveLoggingInstanceObject() {
        $property = self::$templatingReflectionInstance->getProperty("loggingInstance");
        $this->assertTrue(is_object($property));
    }
    
    /**
     * this class should have an instance for the Configure Class
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldHaveConfigureInstanceObject() {
        $property = self::$templatingReflectionInstance->getProperty("configureInstance");
        $this->assertTrue(is_object($property));
    }
    
    /**
     * this class should have an instance for the Compression Class
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldHaveCompressionInstanceObject() {
        $property = self::$templatingReflectionInstance->getProperty("compressionInstance");
        $this->assertTrue(is_object($property));
    }
    
}
?>