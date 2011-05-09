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
     * all expectations for the recurivelyRemove method.
     *
     * @param string $cacheDir The directory storing the cache files
     * @param string $cacheExt The extension on the directory
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function setupExpectationsForRecursivelyRemove($cacheDir, $cacheExt) {
        self::$configureMock->shouldReceive('getDirectory')->times(1)->with('cache', true)->andReturn($cacheDir);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('cache_ext')->andReturn($cacheExt);
    }
    
    /**
     * Setup common expectations for the processRequest method
     *
     * @param array $expectedCurrentPage array of expected page settings
     * @param array $expectedGlobalSettings array of expected global settings
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function setupExpectationsForProcessRequest($expectedCurrentPage, $expectedGlobalSettings) {
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('current_page')->andReturn($expectedCurrentPage);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('global_settings')->andReturn($expectedGlobalSettings);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('debug_level')->andReturn(2);
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
        $this->setupExpectationsForProcessRequest($expectedCurrentPage, $expectedGlobalSettings);
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
            $this->setupExpectationsForProcessRequest($expectedCurrentPage, $expectedGlobalSettings);
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
            $this->setupExpectationsForProcessRequest($expectedCurrentPage, $expectedGlobalSettings);
            $method = self::$cachingReflectionInstance->getMethod("processRequest");
            $method->invoke(self::$cachingInstance);
        }catch (PHPUnit_Framework_Error $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * Test that clear_cache page triggers the clear cache method
     *
     * @return void
     * @todo I am having difficult partial mocking clearCache method on this singleton class.  I want to only make sure it gets trigger,  but to get it to work,  I had to run expectations for the 
     * clearCache method.  YUK!
     * @expectedException PHPUnit_Framework_Error
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldTriggerClearCacheMessage() {
        $expectedCurrentPage = $this->expectedCurrentPage;
        $expectedCurrentPage['page'] = 'clear_cache';
        $expectedGlobalSettings = $this->expectedGlobalSettings;
        $this->setupExpectationsForProcessRequest($expectedCurrentPage, $expectedGlobalSettings);
        $this->setupExpectationsForRecursivelyRemove('tmp/', 'cache');
        $method = self::$cachingReflectionInstance->getMethod("processRequest");
        $method->invoke(self::$cachingInstance);
    }
    
    /**
     * clearCache() should actually clear the cache files
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldClearCache() {
        $expectedCurrentPage = $this->expectedCurrentPage;
        $expectedCurrentPage['page'] = 'clear_cache';
        $expectedCacheDir = 'test_cache' . DS . 'tmp' . DS;
        /**
         * Add temp files in each cache directory to test against
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
        $cacheFileNames = array('cache1.cache', 'cache2.cache', 'cache3.cache');
        foreach($cacheFileNames as $file) {
            $cacheFile = $expectedCacheDir . $file;
            $currentFile = fopen($cacheFile, 'w') or die("can't open file");
            fclose($currentFile);
            $this->assertTrue(file_exists($cacheFile));
        }
        $expectedGlobalSettings = $this->expectedGlobalSettings;
        $this->setupExpectationsForRecursivelyRemove('engine' . DS . 'tests' . DS . $expectedCacheDir, 'cache');
        $method = self::$cachingReflectionInstance->getMethod("clearCache");
        $method->setAccessible(true);
        $method->invoke(self::$cachingInstance);
        /**
         * Make sure all files are deleted
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
        foreach($cacheFileNames as $oldFile) {
            $oldCacheFile = $expectedCacheDir . $oldFile;
            $this->assertFalse(file_exists($oldCacheFile));
        }
    }
    
    /**
     * If startCaching(), var cachingFile should be set to true 
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldSetCachingFileTrueIfStartCaching() {
        /**
         * Verify cachingFile is defaulted to false
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
        $currentCachingFile = self::$cachingReflectionInstance->getProperty("cachingFile");
        $currentCachingFile->setAccessible(true);
        $this->assertFalse($currentCachingFile->getValue(self::$cachingInstance));
        $method = self::$cachingReflectionInstance->getMethod("startCaching");
        $method->setAccessible(true);
        $method->invoke(self::$cachingInstance);
        /**
         * Verify cachingFile is now set to true
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
        $currentCachingFile = self::$cachingReflectionInstance->getProperty("cachingFile");
        $currentCachingFile->setAccessible(true);
        $this->assertTrue($currentCachingFile->getValue(self::$cachingInstance));
    }
    
    /**
     * A cache file should be created as a result of finalizeCaching
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldCreateACacheFileOnFinalizeCaching() {
        $expectedCurrentPage = $this->expectedCurrentPage;
        $expectedGlobalSettings = $this->expectedGlobalSettings;
        $expectedCacheDir = 'test_cache' . DS . 'tmp' . DS;
        $method = self::$cachingReflectionInstance->getMethod("startCaching");
        $method->setAccessible(true);
        $method->invoke(self::$cachingInstance);
        
        $this->setupExpectationsForRecursivelyRemove('engine' . DS . 'tests' . DS . $expectedCacheDir, 'cache');
        
        $method = self::$cachingReflectionInstance->getMethod("finalizeCaching");
        $method->setAccessible(true);
        $method->invoke(self::$cachingInstance);
        
        $this->assertTrue(file_exists($expectedCacheDir . md5($expectedCurrentPage['page_file']) . '.cache'));
        /**
         * Clean up by deleting the file
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
         if(file_exists($expectedCacheDir . md5($expectedCurrentPage['page_file']) . '.cache')) {
             unlink($expectedCacheDir . md5($expectedCurrentPage['page_file']) . '.cache');
         }
    }

    /**
     * Make sure the correct file is passed back in getCacheFileLocation()
     *
     * @return void
     * @todo Is this a necessary test
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldReturnCorrectCacheFileInGetCacheFileLocation() {
        $expectedCurrentPage = $this->expectedCurrentPage;
        $expectedGlobalSettings = $this->expectedGlobalSettings;
        $expectedCacheDir = 'test_cache' . DS . 'tmp' . DS;
        $expectedFileReturned = APP_PATH . 'engine' . DS . 'tests' . DS . $expectedCacheDir. md5($expectedCurrentPage['page_file']) . '.cache';
        
        $this->setupExpectationsForRecursivelyRemove('engine' . DS . 'tests' . DS . $expectedCacheDir, 'cache');
        
        $method = self::$cachingReflectionInstance->getMethod("getCacheFileLocation");
        $method->setAccessible(true);
        $result = $method->invoke(self::$cachingInstance);
        $this->assertEquals($result, $expectedFileReturned);
    }
    
    /**
     * test recurivelyRemove should remove all files in a given directory with a set extension
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldRemoveAllTestFilesInRecursivelyRemove() {
        /**
         * Create some test files in the test_cache directory
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
         $expectedCacheDir = 'test_cache';
         /**
          * Add temp files in each cache directory to test against
          *
          * @author Technoguru Aka. Johnathan Pulos
          */
         $cacheFileNames = array('cache1.cache', 'cache2.cache', 'cache3.cache', 'tmp' . DS . 'cache4.cache');
         foreach($cacheFileNames as $file) {
             $cacheFile = $expectedCacheDir . DS . $file;
             $currentFile = fopen($cacheFile, 'w') or die("can't open file");
             fclose($currentFile);
             $this->assertTrue(file_exists($cacheFile));
         }
         
         $method = self::$cachingReflectionInstance->getMethod("recursivelyRemove");
         $method->setAccessible(true);
         
         $method->invoke(self::$cachingInstance, APP_PATH . 'engine' . DS . 'tests' . DS . $expectedCacheDir, 'cache');
         foreach($cacheFileNames as $file) {
             $this->assertFalse(file_exists($cacheFile));
         }
    }
}
?>