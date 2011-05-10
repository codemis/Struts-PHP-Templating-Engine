<?php
/**
 * Testing for caching.class.php
 *
 * @package STRUTS
 * @author Technoguru Aka. Johnathan Pulos
 */
require_once('../routing.class.php');
require_once('../logging.class.php');
require_once('../configure.class.php');
use \Mockery as m;
class RoutingTest extends PHPUnit_Framework_TestCase {
    /**
	 * The singleton instance of the class
	 *
	 * @var Object
	 */
	private static $routingInstance;
	/**
	 * The instance of the cache Reflection Class
	 *
	 * @var Object
	 */
	private static $routingReflectionInstance;
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
	private static $loggingMock;
	/**
     * Factory for current page settings
     *
     * @var array
     */
	private $expectedCurrentPage = array(   'request' => 'test', 
                                            'page' => 'test', 
                                            'file_name' => 'test', 
                                            'page_file' => 'engine/tests/test_routes/pages/test.html', 
                                            'php_file' => 'engine/tests/test_routes/php/test.php'
                                );
	/**
	 * A Factory for the SPYCSettings
	 *
	 * @var array
	 */ 
	private $factorySPYCSettings = array('global' => array( 'domain' => 'http://test.local',
	                                                        'title' => 'Default Title',
	                                                        'description' => 'Default Description',
	                                                        'keywords' => 'Default Keywords',
	                                                        'template' => 'example.html',
	                                                        'javascript' => 'test.js,test2.js,test3.js',
	                                                        'css' => 'example.css',
	                                                        'compress_js' => false,
	                                                        'compress_css' => false,
	                                                        'css_compress_directory' => 'design/css/min/',
	                                                        'js_compress_directory' => 'design/js/min/',
	                                                        'enable_caching' => false,
	                                                    ), 
	                                    'test' => array(    'title' => 'Test Page',
	                                                        'description' => 'Test Description',
	                                                        'keywords' => 'Test Keywords',
	                                                        'template' => '',
	                                                        'javascript' => '',
	                                                        'css' => '',
	                                                        'landing_page' => false,
	                                                        'cache' => true
	                                                    ) 
	                                    );  
	
	/**
	 * Setup the testing case
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
    public function setUp() {
        self::$routingInstance = Routing::init();
        /**
          * Setup instances of each object as a mock object
          *
          * @author Johnathan Pulos
          */
        self::$configureMock = m::mock('Configure');
        self::$configureMock->shouldReceive('getSetting');
        self::$loggingMock = m::mock('Logging');
        self::$loggingMock->shouldReceive('logTrace');
        self::$routingInstance = '';
        self::$routingInstance = Routing::init(true);
        self::$routingInstance->configureInstance = self::$configureMock;
        self::$routingInstance->loggingInstance = self::$loggingMock;
        self::$routingReflectionInstance = new ReflectionClass('Routing');
    }
    
    /**
     * Setup the SPYCSettings class var
     *
     * @param array $settings array of settings to use
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function setupSPYCSettings($settings) {
        $spycSettingProp = self::$routingReflectionInstance->getProperty("SPYCSettings");
        $spycSettingProp->setAccessible(true);
        $spycSettingProp->setValue(self::$routingInstance, $settings);
    }
    
    /**
     * Setup the currentPage class var
     *
     * @param array $currentPage array of the currentPage
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function setupCurrentPage($currentPage) {
        $currentPageProp = self::$routingReflectionInstance->getProperty("currentPage");
        $currentPageProp->setAccessible(true);
        $currentPageProp->setValue(self::$routingInstance, $currentPage);
    }
    
    /**
     * Teardown the SPYCSettings class var
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function teardownSPYCSettings() {
        $spycSettingProp = self::$routingReflectionInstance->getProperty("SPYCSettings");
        $spycSettingProp->setAccessible(true);
        $spycSettingProp->setValue(self::$routingInstance, array());
    }
    
    /**
     * Teardown the currentPage class var
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function teardownCurrentPage() {
        $currentPageProp = self::$routingReflectionInstance->getProperty("currentPage");
        $currentPageProp->setAccessible(true);
        $currentPageProp->setValue(self::$routingInstance, array());
    }
    
    /**
     * Complete the testing case
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function tearDown() {
        self::$routingInstance = '';
        self::$routingReflectionInstance = '';
        self::$loggingMock = '';
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
        $method = self::$routingReflectionInstance->getMethod("__construct");
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
            $method = self::$routingReflectionInstance->getMethod("__clone");
            $method->invoke(self::$routingInstance);
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
        $property = self::$routingReflectionInstance->getProperty("loggingInstance");
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
        $property = self::$routingReflectionInstance->getProperty("configureInstance");
        $this->assertTrue(is_object($property));
    }
    
    /**
     * Without SPYCSettings being set getCurrentPage should fail
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldThrowErrorIfMissingSpycSettingsOnGetCurrentPage() {
        $spycSettingProp = self::$routingReflectionInstance->getProperty("SPYCSettings");
        $spycSettingProp->setAccessible(true);
        $spycSettingProp->setValue(self::$routingInstance, array());
        
        try {
            $method = self::$routingReflectionInstance->getMethod("getCurrentPage");
            $method->invoke(self::$routingInstance, '/home');
        }catch (PHPUnit_Framework_Error $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * tests to make sure currentPage gets set correctly in getCurrentPage()
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldSetCurrentPageOnGetCurrentPage() {
        $expectedRequest = 'test';
        $expectedPage = 'test';
        $expectedFileName = 'test';
        $expectedPageFile = 'engine/tests/test_routes/pages/test.html';
        $expectedPHPFile = 'engine/tests/test_routes/php/test.php';
        
        $this->setupSPYCSettings($this->factorySPYCSettings);
        
        self::$configureMock->shouldReceive('getDirectory')->times(1)->with('pages')->andReturn('engine/tests/test_routes/pages');
        self::$configureMock->shouldReceive('getDirectory')->times(1)->with('pages_code')->andReturn('engine/tests/test_routes/php');
        
        $method = self::$routingReflectionInstance->getMethod("getCurrentPage");
        $result = $method->invoke(self::$routingInstance, $expectedRequest);
        
        $this->assertEquals($expectedRequest, $result['request']);
        $this->assertEquals($expectedPage, $result['page']);
        $this->assertEquals($expectedFileName, $result['file_name']);
        $this->assertEquals($expectedPageFile, $result['page_file']);
        $this->assertEquals($expectedPHPFile, $result['php_file']);
        
        $currentPageProp = self::$routingReflectionInstance->getProperty("currentPage");
        $currentPageProp->setAccessible(true);
        $currentPage = $currentPageProp->getValue(self::$routingInstance);
        $this->assertEquals($result, $currentPage);
        
        $this->teardownSPYCSettings();
        $this->teardownCurrentPage();
    }
    
    /**
     * tests to make sure currentPage gets set correctly in getCurrentPage() using a landing page is referencing the index.html file
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldSetCurrentIndexReferencedLandingPageOnGetCurrentPage() {
        $expectedRequest = 'test/index.html';
        $expectedPage = 'test';
        $expectedFileName = 'test/index';
        $expectedPageFile = 'engine/tests/test_routes/pages/test/index.html';
        $expectedPHPFile = 'engine/tests/test_routes/php/test/index.php';
        
        $spycSettings = $this->factorySPYCSettings;
        $spycSettings['test']['landing_page'] = true;
        
        $this->setupSPYCSettings($spycSettings);
        
        self::$configureMock->shouldReceive('getDirectory')->times(1)->with('pages')->andReturn('engine/tests/test_routes/pages');
        self::$configureMock->shouldReceive('getDirectory')->times(1)->with('pages_code')->andReturn('engine/tests/test_routes/php');
        
        $method = self::$routingReflectionInstance->getMethod("getCurrentPage");
        $result = $method->invoke(self::$routingInstance, $expectedRequest);
        
        $this->assertEquals($expectedRequest, $result['request']);
        $this->assertEquals($expectedPage, $result['page']);
        $this->assertEquals($expectedFileName, $result['file_name']);
        $this->assertEquals($expectedPageFile, $result['page_file']);
        $this->assertEquals($expectedPHPFile, $result['php_file']);
        
        $currentPageProp = self::$routingReflectionInstance->getProperty("currentPage");
        $currentPageProp->setAccessible(true);
        $currentPage = $currentPageProp->getValue(self::$routingInstance);
        $this->assertEquals($result, $currentPage);
        
        $this->teardownSPYCSettings();
        $this->teardownCurrentPage();
    }
    
    /**
     * tests to make sure currentPage gets set correctly in getCurrentPage() using a landing page is referencing the parent directory only
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldSetCurrentDirectoryReferencedLandingPageOnGetCurrentPage() {
        $expectedRequest = 'test';
        $expectedPage = 'test';
        $expectedFileName = 'test/index';
        $expectedPageFile = 'engine/tests/test_routes/pages/test/index.html';
        $expectedPHPFile = 'engine/tests/test_routes/php/test/index.php';
        
        $spycSettings = $this->factorySPYCSettings;
        $spycSettings['test']['landing_page'] = true;
        
        $this->setupSPYCSettings($spycSettings);
        
        self::$configureMock->shouldReceive('getDirectory')->times(1)->with('pages')->andReturn('engine/tests/test_routes/pages');
        self::$configureMock->shouldReceive('getDirectory')->times(1)->with('pages_code')->andReturn('engine/tests/test_routes/php');
        
        $method = self::$routingReflectionInstance->getMethod("getCurrentPage");
        $result = $method->invoke(self::$routingInstance, $expectedRequest);
        
        $this->assertEquals($expectedRequest, $result['request']);
        $this->assertEquals($expectedPage, $result['page']);
        $this->assertEquals($expectedFileName, $result['file_name']);
        $this->assertEquals($expectedPageFile, $result['page_file']);
        $this->assertEquals($expectedPHPFile, $result['php_file']);
        
        $currentPageProp = self::$routingReflectionInstance->getProperty("currentPage");
        $currentPageProp->setAccessible(true);
        $currentPage = $currentPageProp->getValue(self::$routingInstance);
        $this->assertEquals($result, $currentPage);
        
        $this->teardownSPYCSettings();
        $this->teardownCurrentPage();
    }
    
    /**
     * getRequestedPage() should remove extensions from the requested page
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldRemoveExtensionOnGetRequestedPage() {
        $expectedRequest = 'test';
        $currentPage = $this->expectedCurrentPage;
        $currentPage['request'] = 'test.html';
        
        $this->setupSPYCSettings($this->factorySPYCSettings);
        $this->setupCurrentPage($currentPage);
        
        $method = self::$routingReflectionInstance->getMethod("getRequestedPage");
        $method->setAccessible(true);
        $result = $method->invoke(self::$routingInstance);
        $this->assertEquals($expectedRequest, $result);
        
        $this->teardownSPYCSettings();
        $this->teardownCurrentPage();
    }
    
    /**
     * getRequestedPage() should set page to 404 if it does not exits
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShould404OnGetRequestedPage() {
        $expectedRequest = '404';
        $currentPage = $this->expectedCurrentPage;
        $currentPage['request'] = 'guess_who_page.html';
        
        $this->setupSPYCSettings($this->factorySPYCSettings);
        $this->setupCurrentPage($currentPage);
        
        $method = self::$routingReflectionInstance->getMethod("getRequestedPage");
        $method->setAccessible(true);
        $result = $method->invoke(self::$routingInstance);
        $this->assertEquals($expectedRequest, $result);
        
        $this->teardownSPYCSettings();
        $this->teardownCurrentPage();
    }
    
    /**
     * getRequestedPage() should remove index.html from the requested page
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldRemoveIndexOnGetRequestedPage() {
        $expectedRequest = 'test';
        $currentPage = $this->expectedCurrentPage;
        $currentPage['request'] = 'test/index.html';
        
        $this->setupSPYCSettings($this->factorySPYCSettings);
        $this->setupCurrentPage($currentPage);
        
        $method = self::$routingReflectionInstance->getMethod("getRequestedPage");
        $method->setAccessible(true);
        $result = $method->invoke(self::$routingInstance);
        $this->assertEquals($expectedRequest, $result);
        
        $this->teardownSPYCSettings();
        $this->teardownCurrentPage();
    }
    
    /**
     * When requesting a landing page,  getFileName() should return an index file
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldReturnCorrectLandingPageOnGetFileName() {
        $expectedRequest = 'test/index';
        $currentPage = $this->expectedCurrentPage;
        $currentPage['request'] = 'test';
        $spycSettings = $this->factorySPYCSettings;
        $spycSettings['test']['landing_page'] = true;
        $this->setupSPYCSettings($spycSettings);
        $this->setupCurrentPage($currentPage);
        
        
        $method = self::$routingReflectionInstance->getMethod("getFileName");
        $method->setAccessible(true);
        $result = $method->invoke(self::$routingInstance);
        $this->assertEquals($expectedRequest, $result);
        
        $this->teardownSPYCSettings();
        $this->teardownCurrentPage();
    }
    
    /**
     * When requesting a regular page,  getFileName() should return the current page file
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldReturnCorrectPageOnGetFileName() {
        $expectedRequest = 'test';
        $currentPage = $this->expectedCurrentPage;
        $currentPage['request'] = 'test';
        $spycSettings = $this->factorySPYCSettings;
        $spycSettings['test']['landing_page'] = false;
        $this->setupSPYCSettings($spycSettings);
        $this->setupCurrentPage($currentPage);
        
        
        $method = self::$routingReflectionInstance->getMethod("getFileName");
        $method->setAccessible(true);
        $result = $method->invoke(self::$routingInstance);
        $this->assertEquals($expectedRequest, $result);
        
        $this->teardownSPYCSettings();
        $this->teardownCurrentPage();
    }
}
?>