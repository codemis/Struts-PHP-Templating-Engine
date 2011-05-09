<?php
/**
 * Testing for caching.class.php
 *
 * @package STRUTS
 * @author Technoguru Aka. Johnathan Pulos
 */
require_once('../compression.class.php');
require_once('../logging.class.php');
require_once('../configure.class.php');
use \Mockery as m;
class CompressionTest extends PHPUnit_Framework_TestCase {
    /**
	 * The singleton instance of the class
	 *
	 * @var Object
	 */
	private static $compressionInstance;
	/**
	 * The instance of the cache Reflection Class
	 *
	 * @var Object
	 */
	private static $compressionReflectionInstance;
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
	private $expectedPageSettings = array(  'title' => 'Page Title',
	                                        'description' => 'Page Description',
	                                        'keywords' => 'Page keywords',
	                                        'template' => 'second_layout.html',
	                                        'javascript' => '',
	                                        'css' => '',
	                                        'landing_page' => true,
	                                        'cache' => true
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
	 * Setup the testing case
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
    public function setUp() {
        self::$compressionInstance = Compression::init();
    /**
      * Setup instances of ecah object as a mock object
      *
      * @author Johnathan Pulos
      */
        self::$configureMock = m::mock('Configure');
        self::$loggingMock = m::mock('Logging');
        self::$loggingMock->shouldReceive('logTrace');
        self::$compressionInstance = '';
        self::$compressionInstance = Compression::init();
        self::$compressionInstance->loggingInstance = self::$loggingMock;
        self::$compressionInstance->configureInstance = self::$configureMock;
        self::$compressionReflectionInstance = new ReflectionClass('Compression');
    }
    
    /**
     * Complete the testing case
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function tearDown() {
        self::$compressionInstance = '';
        self::$compressionReflectionInstance = '';
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
        $method = self::$compressionReflectionInstance->getMethod("__construct");
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
            $method = self::$compressionReflectionInstance->getMethod("__clone");
            $method->invoke(self::$compressionInstance);
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
        $property = self::$compressionReflectionInstance->getProperty("configureInstance");
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
        $property = self::$compressionReflectionInstance->getProperty("loggingInstance");
        $this->assertTrue(is_object($property));
    }
    
    /**
     * compressJavascript() should return an array with the page and global compressed final file names
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldReturnAppropriateJavascriptArrayFromCompressJavascript() {
        $expectedPageSettings = $this->expectedPageSettings;
        $expectedPageSettings['javascript'] = 'javscript1.js,javascript2.js';
        $expectedGlobalSettings = $this->expectedGlobalSettings;
        $expectedGlobalSettings['javascript'] = 'javscript3.js,javascript4.js';
        $expectedGlobalSettings['js_compress_directory'] = 'engine' . DS . 'tests' . DS . 'test_compress' . DS . 'js' . DS;
        $expectedDir = $expectedGlobalSettings['js_compress_directory'];
        $expectedCompressedName = 'sitewide.min';
        $expectedCurrentPage = $this->expectedCurrentPage;
        $expectedCurrentPage['page'] = 'my_special_page';
        $expectedFinalFiles = array('sitewide.min.js', 'my_special_page_scripts.min.js');
        
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('global_settings')->andReturn($expectedGlobalSettings);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('page_settings')->andReturn($expectedPageSettings);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('sitewide_compressed_filename')->andReturn($expectedCompressedName);
        self::$configureMock->shouldReceive('getDirectory')->times(3)->with('js', false)->andReturn($expectedDir);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('current_page')->andReturn($expectedCurrentPage);
        $method = self::$compressionReflectionInstance->getMethod("compressJavascript");
        $result = $method->invoke(self::$compressionInstance);
        
        $this->assertFalse(empty($result));
        $this->assertEquals(2, count($result));
        $cleanedFileNames = array();
        foreach($result as $path){
            $path_pieces = explode('?', basename($path));
            array_push($cleanedFileNames, $path_pieces[0]);
        }
        $this->assertEquals($cleanedFileNames, $expectedFinalFiles);
    }
    

    /**
     * compressCSS() should return an array with the page and global compressed final file names
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldReturnAppropriateCSSArrayFromCompressCSS() {
        $expectedPageSettings = $this->expectedPageSettings;
        $expectedPageSettings['css'] = 'css1.css,css2.css';
        $expectedGlobalSettings = $this->expectedGlobalSettings;
        $expectedGlobalSettings['css'] = 'css3.css,css4.css';
        $expectedGlobalSettings['css_compress_directory'] = 'engine' . DS . 'tests' . DS . 'test_compress' . DS . 'css' . DS;
        $expectedDir = $expectedGlobalSettings['css_compress_directory'];
        $expectedCompressedName = 'sitewide.min';
        $expectedCurrentPage = $this->expectedCurrentPage;
        $expectedCurrentPage['page'] = 'my_special_page';
        $expectedFinalFiles = array('sitewide.min.css', 'my_special_page_styles.min.css');
        
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('global_settings')->andReturn($expectedGlobalSettings);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('page_settings')->andReturn($expectedPageSettings);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('sitewide_compressed_filename')->andReturn($expectedCompressedName);
        self::$configureMock->shouldReceive('getDirectory')->times(3)->with('css', false)->andReturn($expectedDir);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('current_page')->andReturn($expectedCurrentPage);
        $method = self::$compressionReflectionInstance->getMethod("compressCSS");
        $result = $method->invoke(self::$compressionInstance);
        
        $this->assertFalse(empty($result));
        $this->assertEquals(2, count($result));
        $cleanedFileNames = array();
        foreach($result as $path){
            $path_pieces = explode('?', basename($path));
            array_push($cleanedFileNames, $path_pieces[0]);
        }
        $this->assertEquals($cleanedFileNames, $expectedFinalFiles);
    }
    
    /**
     * resourceFileExistsOrCreate() should return the file url with the last modified date appended with a ? 
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldReturnFileUrlWithDateFromResourceFileExistsOrCreate() {
        $testFile = 'engine' . DS . 'tests' . DS . 'test_compress' . DS . 'css' . DS . 'sitewide.min.css';
        self::$configureMock->shouldReceive('getDirectory')->times(1)->with('css', false);
        $expectedModifiedDate = date("ymdGis", filemtime(APP_PATH . $testFile));
        $expectedResult = '/' . $testFile . '?' . $expectedModifiedDate;
        
        $method = self::$compressionReflectionInstance->getMethod("resourceFileExistsOrCreate");
        $method->setAccessible(true);
        $result = $method->invoke(self::$compressionInstance, str_replace('/', DS, $testFile), APP_PATH . $testFile, '', 'css');
        $this->assertEquals($result, $expectedResult);
    }
    
    /**
     * test should clean up filename so it has the finle safe symbols
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldCleanFilenameOnGetPageCompressionFileName() {
        $testFilename = 'my/test-file';
        $expectedFilename = 'my_test_file'; 
        $expectedCurrentPage = $this->expectedCurrentPage;
        $expectedCurrentPage['page'] = $testFilename;
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('current_page')->andReturn($expectedCurrentPage);
        
        $method = self::$compressionReflectionInstance->getMethod("getPageCompressionFileName");
        $method->setAccessible(true);
        $result = $method->invoke(self::$compressionInstance);
        $this->assertEquals($result, $expectedFilename);
    }
    
}
?>