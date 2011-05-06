<?php
/**
 * Testing for struts_engine.class.php.  Uses Mockery for Mock Objects
 *
 * @link https://github.com/padraic/mockery
 * 
 * @package STRUTS
 * @author Technoguru Aka. Johnathan Pulos
 */
require_once('../struts_engine.class.php');
use \Mockery as m;
class strutsEngineTest extends PHPUnit_Framework_TestCase {
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
	 * The Mock instance of the Configure class
	 *
	 * @var Object
	 */
	private static $configureMock;
	/**
	 * The Mock instance of the Configure class
	 *
	 * @var Object
	 */
	private static $routingMock;
	/**
	 * The Mock instance of the Routing class
	 *
	 * @var Object
	 */
	private static $cachingMock;
	/**
	 * The Mock instance of the Logging class
	 *
	 * @var Object
	 */
	private static $loggingMock;
	/**
	 * The Mock instance of the Templating class
	 *
	 * @var Object
	 */
	private static $templatingMock;
	
	/**
	 * Setup the testing case
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
    public function setUp() {
        
        self::$strutsInstance = strutsEngine::init();
        /**
         * Setup instances of ecah object as a mock object
         *
         * @author Johnathan Pulos
         */
         self::$configureMock = m::mock('Configure');
         self::$routingMock = m::mock('Routing');
         self::$cachingMock = m::mock('Caching');
         self::$templatingMock = m::mock('Templating');
         self::$loggingMock = m::mock('Logging');
         self::$loggingMock->shouldReceive('logTrace');
         self::$strutsInstance = '';
         strutsEngine::$configureInstance = self::$configureMock;
         strutsEngine::$routingInstance = self::$routingMock;
         strutsEngine::$cachingInstance = self::$cachingMock;
         strutsEngine::$templatingInstance = self::$templatingMock;
         strutsEngine::$loggingInstance = self::$loggingMock;
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
        self::$configureMock = '';
        self::$routingMock = '';
        self::$cachingMock = '';
        self::$templatingMock = '';
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
        $testKey = 'MyVar';
        $testVal = 'MyVal';
        self::$configureMock->shouldReceive('setSetting')->times(1)->with($testKey, $testVal);
        $method = self::$strutsReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$strutsInstance, $testKey, $testVal);
    }
    
    /**
     * getDirectory should deliver correct directory
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldGetADirectory() {
        $testKey = 'directories';
        $testVal = array('test_directory' => 'test/mess/duck/');
        self::$configureMock->shouldReceive('setSetting')->times(1)->with($testKey, $testVal)->ordered();
        $method = self::$strutsReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$strutsInstance, $testKey, $testVal);
        
        self::$configureMock->shouldReceive('getDirectory')->times(1)->with('test_directory', false)->andReturn($testVal['test_directory'])->ordered();
        $getMethod = self::$strutsReflectionInstance->getMethod("getDirectory");
        $this->assertEquals('test/mess/duck/', $getMethod->invoke(self::$strutsInstance, 'test_directory'));
    }
    
    public function testSingleton() {
        $testUrl = 'example';
        $expectedCurrentPage = array(   'request' => 'example', 
                                        'page' => 'example', 
                                        'file_name' => 'example', 
                                        'page_file' => 'design/pages/example.html', 
                                        'php_file' => 'code/pages/example.php'
                                    );
                                    
        self::$configureMock->shouldReceive('setSPYCSettings')->times(1)->ordered();
        self::$routingMock->shouldReceive('getCurrentPage')->times(1)->with($testUrl)->andReturn($expectedCurrentPage)->ordered();
        self::$configureMock->shouldReceive('setSetting')->times(1)->with('current_page', $expectedCurrentPage)->ordered();
        self::$configureMock->shouldReceive('initGlobalSettings')->times(1)->ordered();
        self::$configureMock->shouldReceive('initPageSettings')->times(1)->ordered();
        self::$cachingMock->shouldReceive('processRequest')->times(1)->ordered();
        self::$templatingMock->shouldReceive('processRequest')->times(1)->ordered();
        $method = self::$strutsReflectionInstance->getMethod("processRequest");
        $method->invoke(self::$strutsInstance, $testUrl);
    }

}
?>