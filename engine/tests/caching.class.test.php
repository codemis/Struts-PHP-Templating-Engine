<?php
/**
 * Testing for struts_engine.class.php
 *
 * @package STRUTS
 * @author Technoguru Aka. Johnathan Pulos
 */
require_once('../caching.class.php');
require_once('../logging.class.php');
require_once('../configure.class.php');
use \Mockery as m;
class CachingTest extends PHPUnit_Framework_TestCase
{
    /**
	 * The singleton instance of the class
	 *
	 * @var Object
	 */
	private static $cachingInstance;
	/**
	 * The instance of the cache Reflection Class
	 *
	 * @var Object
	 */
	private static $cachingReflectionInstance;
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
     * Factory for current page settings
     *
     * @var array
     */
	private $expectedCurrentPage = array(   'request' => 'example', 
                                            'page' => 'example', 
                                            'file_name' => 'example', 
                                            'page_file' => 'design/pages/example.html', 
                                            'php_file' => 'code/pages/example.php'
                                );
    /**
     * Factory for global settings
     *
     * @var string
     */
    private $expectedGlobalSettings = array(    'domain' => 'http://struts.local',
                                                'title' => 'Default Title',
                                                'description' => 'Default Description',
                                                'keywords' => 'Default Keywords',
                                                'template' => 'example.html',
                                                'javascript' => 'test.js,test2.js,test3.js',
                                                'css' => 'example.css',
                                                'compress_js' => true,
                                                'compress_css' => true,
                                                'css_compress_directory' => 'design/css/min/',
                                                'js_compress_directory' => 'design/js/min/',
                                                'enable_caching' => true
    );
	
	/**
	 * Setup the testing case
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
    public function setUp() {
        self::$cachingInstance = Caching::init();
    /**
      * Setup instances of ecah object as a mock object
      *
      * @author Johnathan Pulos
      */
        self::$configureMock = m::mock('Configure');
        self::$loggingMock = m::mock('Logging');
        self::$loggingMock->shouldReceive('logTrace');
        self::$cachingInstance = '';
        self::$cachingInstance = Caching::init();
        self::$cachingInstance->loggingInstance = self::$loggingMock;
        self::$cachingInstance->configureInstance = self::$configureMock;
        self::$cachingReflectionInstance = new ReflectionClass('Caching');
    }
    
    /**
     * Complete the testing case
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function tearDown() {
        self::$cachingInstance = '';
        self::$cachingReflectionInstance = '';
        self::$configureMock = '';
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
        $method = self::$cachingReflectionInstance->getMethod("__construct");
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
            $method = self::$cachingReflectionInstance->getMethod("__clone");
            $method->invoke(self::$cachingInstance);
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
        $property = self::$cachingReflectionInstance->getProperty("configureInstance");
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
        $property = self::$cachingReflectionInstance->getProperty("loggingInstance");
        $this->assertTrue(is_object($property));
    }
    
    /**
     * test should properly process the request
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldProcessRequest() {
        $expectedCurrentPage = $this->expectedCurrentPage;
        $expectedGlobalSettings = $this->expectedGlobalSettings;
        $expectedGlobalSettings['enable_caching'] = false;
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('current_page')->andReturn($expectedCurrentPage);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('global_settings')->andReturn($expectedGlobalSettings);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('debug_level')->andReturn(2);
        $method = self::$cachingReflectionInstance->getMethod("processRequest");
        $method->invoke(self::$cachingInstance);
    }
    
    /**
     * test should processRequest should throw error if missing current_page
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldThrowErrorOnProcessRequestIfNoCurrentPage() {
        $expectedCurrentPage = array();
        $expectedGlobalSettings = $this->expectedGlobalSettings;
        $expectedGlobalSettings['enable_caching'] = false;
        try {
            self::$configureMock->shouldReceive('getSetting')->times(1)->with('current_page')->andReturn($expectedCurrentPage);
            self::$configureMock->shouldReceive('getSetting')->times(1)->with('global_settings')->andReturn($expectedGlobalSettings);
            self::$configureMock->shouldReceive('getSetting')->times(1)->with('debug_level')->andReturn(2);
            $method = self::$cachingReflectionInstance->getMethod("processRequest");
            $method->invoke(self::$cachingInstance);
        }catch (PHPUnit_Framework_Error $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * test should processRequest should throw error if missing global_settings
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldThrowErrorOnProcessRequestIfNoGlobalSettings() {
        $expectedCurrentPage = $this->expectedCurrentPage;
        $expectedGlobalSettings = array();
        try {
            self::$configureMock->shouldReceive('getSetting')->times(1)->with('current_page')->andReturn($expectedCurrentPage);
            self::$configureMock->shouldReceive('getSetting')->times(1)->with('global_settings')->andReturn($expectedGlobalSettings);
            self::$configureMock->shouldReceive('getSetting')->times(1)->with('debug_level')->andReturn(2);
            $method = self::$cachingReflectionInstance->getMethod("processRequest");
            $method->invoke(self::$cachingInstance);
        }catch (PHPUnit_Framework_Error $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
}
?>