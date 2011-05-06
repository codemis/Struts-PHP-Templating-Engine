<?php
/**
 * Testing for struts_engine.class.php
 *
 * @package STRUTS
 * @author Technoguru Aka. Johnathan Pulos
 */
/**
* Define the DOMAIN for the current site
*/
define("DOMAIN", "http://struts.local/");
/**
* Define the directory seperator type for the current file system
*/
define("DS", '/');
/**
* Defines the root directory for this application, force it to the directory root
*/
define("APP_PATH", "../../");
require_once('../struts_engine.class.php');
class strutsEngineTest extends PHPUnit_Framework_TestCase
{
    /**
	 * The singleton instance of the class
	 *
	 * @var Object
	 */
	private static $strutsInstance;
	/**
	 * The instance of the strutsEngine Reflection Class
	 *
	 * @author Johnathan Pulos
	 */
	private static $strutsReflectionInstance;
	
	/**
	 * Setup the testing case
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
    public function setUp() {
        self::$strutsInstance = strutsEngine::init();
        self::$strutsReflectionInstance = new ReflectionClass('strutsEngine');
    }
    
    /**
     * Complete the testing case
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function tearDown() {
        self::$strutsInstance = '';
        self::$strutsReflectionInstance = '';
    }

    /**
     * You should not be able to construct this class.  You should use the init() method
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testConstructShouldBePrivate() {
        $method = self::$strutsReflectionInstance->getMethod("__construct");
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
            $method = self::$strutsReflectionInstance->getMethod("__clone");
            $method->invoke(self::$strutsInstance);
        }catch (PHPUnit_Framework_Error $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * this class should have an instance for the strutsEngine Class
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldHaveStrutInstanceObject() {
        $property = self::$strutsReflectionInstance->getProperty("strutsInstance");
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
        $property = self::$strutsReflectionInstance->getProperty("configureInstance");
        $this->assertTrue(is_object($property));
    }
    
    /**
     * this class should have an instance for the Routing Class
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldHaveRoutingInstanceObject() {
        $property = self::$strutsReflectionInstance->getProperty("routingInstance");
        $this->assertTrue(is_object($property));
    }
    
    /**
     * this class should have an instance for the Logging Class
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldHaveLoggingInstanceObject() {
        $property = self::$strutsReflectionInstance->getProperty("loggingInstance");
        $this->assertTrue(is_object($property));
    }
    
    /**
     * this class should have an instance for the Caching Class
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldHaveCachingInstanceObject() {
        $property = self::$strutsReflectionInstance->getProperty("cachingInstance");
        $this->assertTrue(is_object($property));
    }
    
    /**
     * this class should have an instance for the Templating Class
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldHaveTemplatingInstanceObject() {
        $property = self::$strutsReflectionInstance->getProperty("templatingInstance");
        $this->assertTrue(is_object($property));
    }
    
    /**
     * Need to figure out how to setup Mock Objects for a singleton class. Make sure setSetting sets a setting for the engine.
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testSetSettingShouldSetASetting() {
        $method = self::$strutsReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$strutsInstance, 'MyVar', 'MyVal');
        
        $getMethod = self::$strutsReflectionInstance->getMethod("getSetting");
        $this->assertEquals('MyVal', $getMethod->invoke(self::$strutsInstance, 'MyVar'));
    }
    
    /**
     * getDirectory should deliver correct directory
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldGetADirectory() {
        $expectedDirectory = array('test_directory' => 'test/mess/duck/', 'another_test_directory' => 'test/horse_and_cow/');
        $method = self::$strutsReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$strutsInstance, 'directories', $expectedDirectory);
        
        $getMethod = self::$strutsReflectionInstance->getMethod("getDirectory");
        $this->assertEquals('test/mess/duck/', $getMethod->invoke(self::$strutsInstance, 'test_directory'));
        $this->assertEquals('test/horse_and_cow/', $getMethod->invoke(self::$strutsInstance, 'another_test_directory'));
    }
    
    public function testSingleton() {
        self::$strutsInstance = '';
        $configureMock = $this->getMock('Configure', array('setSetting', 'trace'), array(), '', false);
        strutsEngine::$configureInstance = $configureMock;
        self::$strutsInstance = strutsEngine::init();
        
        $configureMock->expects($this->any())->method('setSetting');
        $method = self::$strutsReflectionInstance->getMethod("processRequest");
        $method->invoke(self::$strutsInstance, '/test/test');
    }
    
    
}
?>