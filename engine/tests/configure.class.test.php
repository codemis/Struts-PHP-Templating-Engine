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
class ConfigureTest extends PHPUnit_Framework_TestCase {
    /**
	 * The singleton instance of the class
	 *
	 * @var Object
	 */
	private static $configureInstance;
	/**
	 * The instance of the cache Reflection Class
	 *
	 * @var Object
	 */
	private static $configureReflectionInstance;
	/**
	 * The Mock instance of the Logging class
	 *
	 * @var Object
	 */
	private static $loggingMock;
	
	/**
	 * Setup the testing case
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
    public function setUp() {
        self::$configureInstance = Configure::init();
    /**
      * Setup instances of ecah object as a mock object
      *
      * @author Johnathan Pulos
      */
        self::$loggingMock = m::mock('Logging');
        self::$loggingMock->shouldReceive('logTrace');
        self::$configureInstance = '';
        self::$configureInstance = Configure::init();
        self::$configureInstance->loggingInstance = self::$loggingMock;
        self::$configureReflectionInstance = new ReflectionClass('Configure');
    }
    
    /**
     * Complete the testing case
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function tearDown() {
        self::$configureInstance = '';
        self::$configureReflectionInstance = '';;
        self::$loggingMock = '';
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
        $method = self::$configureReflectionInstance->getMethod("__construct");
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
            $method = self::$configureReflectionInstance->getMethod("__clone");
            $method->invoke(self::$configureInstance);
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
        $property = self::$configureReflectionInstance->getProperty("loggingInstance");
        $this->assertTrue(is_object($property));
    }
    
    /**
     * setSetting() should create a new class var for the setting
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldSetAClassVarInSetSetting() {
        $expectedKey = "cowName";
        $expectedValue = "Betsy";
        $method = self::$configureReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$configureInstance, $expectedKey, $expectedValue);
        if (isset(self::$configureInstance->{$expectedKey})) {
            $this->assertEquals($expectedValue, self::$configureInstance->{$expectedKey});
        }else {
            $this->fail('The class var does not exist.');
        }
    }
    
    /**
     * getSetting() shoulf get the correct class var value
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldReturnCorrectSettingOnGetSetting() {
        $expectedKey = "cowName";
        $expectedValue = "Betsy";
        $method = self::$configureReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$configureInstance, $expectedKey, $expectedValue);
        
        $method = self::$configureReflectionInstance->getMethod("getSetting");
        $result = $method->invoke(self::$configureInstance, $expectedKey);
        $this->assertEquals($expectedValue, $result);
    }
    
    public function testShouldReturnAUserSetDirectoryOnGetDirectory() {
        $expectedKey = "directories";
        $expectedValue = array('cache' => 'my_new_cache_dir/');
        $method = self::$configureReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$configureInstance, $expectedKey, $expectedValue);
        
        $method = self::$configureReflectionInstance->getMethod("getDirectory");
        $result = $method->invoke(self::$configureInstance, 'cache', false);
        $this->assertEquals($expectedValue['cache'], $result);
    }
}
?>