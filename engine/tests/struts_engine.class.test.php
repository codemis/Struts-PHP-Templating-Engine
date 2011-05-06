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
	 * @var Object
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
    
    /**
     * Test should allow users to debug the code
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDebug() {
        self::$loggingMock->shouldReceive('errorHandler')->withAnyArgs()->times(1);
        $method = self::$strutsReflectionInstance->getMethod("debug");
        $method->invoke(self::$strutsInstance);
    }
    
    /**
     * Test should trigger correct methods in the Process Request process
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldProcessRequest() {
        $testUrl = 'example';
        $expectedCurrentPage = array(   'request' => 'example', 
                                        'page' => 'example', 
                                        'file_name' => 'example', 
                                        'page_file' => 'design/pages/example.html', 
                                        'php_file' => 'code/pages/example.php'
                                    );
                                    
        self::$configureMock->shouldReceive('setSPYCSettings')->times(1);
        self::$routingMock->shouldReceive('getCurrentPage')->times(1)->with($testUrl)->andReturn($expectedCurrentPage);
        self::$configureMock->shouldReceive('setSetting')->times(1)->with('current_page', $expectedCurrentPage);
        self::$configureMock->shouldReceive('initGlobalSettings')->times(1);
        self::$configureMock->shouldReceive('initPageSettings')->times(1);
        self::$cachingMock->shouldReceive('processRequest')->times(1);
        self::$templatingMock->shouldReceive('processRequest')->times(1);
        $method = self::$strutsReflectionInstance->getMethod("processRequest");
        $method->invoke(self::$strutsInstance, $testUrl);
    }
    
    /**
     * test should render the final request
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldRenderRequest() {
        self::$templatingMock->shouldReceive('completeRequest')->withNoArgs()->times(1);
        self::$cachingMock->shouldReceive('completeRequest')->withNoArgs()->times(1);
        
        $method = self::$strutsReflectionInstance->getMethod("renderRequest");
        $method->invoke(self::$strutsInstance);
    }

    /**
     * test should give users ability to add template tags
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldSetTemplateTags() {
        $testKey = 'cow_name';
        $testVal = 'Betsy';
        self::$templatingMock->shouldReceive('setATemplateTag')->with($testKey, $testVal)->times(1);
        
        $method = self::$strutsReflectionInstance->getMethod("setTemplateTag");
        $method->invoke(self::$strutsInstance, $testKey, $testVal);
    }
    
    /**
     * test should give users abillity to add an array of template tags
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldSetTemplateTagsFromArray() {
        $testArray = array('cow_name' => 'Betsy', 'dog_name' => 'Max');
        $expectedArray = array('cow_name' => 'Betsy', 'dog_name' => 'Max');
        self::$templatingMock->shouldReceive('setTemplateTagsWithArray')->with($expectedArray)->times(1);
        
        $method = self::$strutsReflectionInstance->getMethod("setTemplateTagsWithArray");
        $method->invoke(self::$strutsInstance, $testArray, '');
    }
    
    /**
     * User should be able to add a prefix when adding template tags in an array
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldAddPrefixWhenCallingSetTemplateTagsFromArray() {
        $prefix = 'moo_';
        $testArray = array('cow_name' => 'Betsy', 'dog_name' => 'Max');
        $expectedArray = array($prefix.'cow_name' => 'Betsy', $prefix.'dog_name' => 'Max');
        self::$templatingMock->shouldReceive('setTemplateTagsWithArray')->with($expectedArray)->times(1);
        
        $method = self::$strutsReflectionInstance->getMethod("setTemplateTagsWithArray");
        $method->invoke(self::$strutsInstance, $testArray, $prefix);
    }
    
    /**
     * STRUTS should be able to trace its progess
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldTraceLogDetails() {
    }
    
    /**
     * test should prove setLayoutVar is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecateSetLayoutVar() {
        try {
            $method = self::$strutsReflectionInstance->getMethod("setLayoutVar");
            $method->invoke(self::$strutsInstance, 'My Var', 'My Val');
        }catch (PHPUnit_Framework_Error_Warning $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * test should prove setPageVar is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecateSetPageVar() {
        try {
            $method = self::$strutsReflectionInstance->getMethod("setPageVar");
            $method->invoke(self::$strutsInstance, 'My Var', 'My Val');
        }catch (PHPUnit_Framework_Error_Warning $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * test should prove setLayoutVarFromArray is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecateSetLayoutVarFromArray() {
        try {
            $method = self::$strutsReflectionInstance->getMethod("setLayoutVarFromArray");
            $method->invoke(self::$strutsInstance, array('var_1' => 'val_1'), 'cow_');
        }catch (PHPUnit_Framework_Error_Warning $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * test should prove setPageVarFromArray is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecateSetPageVarFromArray() {
        try {
            $method = self::$strutsReflectionInstance->getMethod("setPageVarFromArray");
            $method->invoke(self::$strutsInstance, array('var_1' => 'val_1'), 'cow_');
        }catch (PHPUnit_Framework_Error_Warning $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * test should prove setLayoutJavascriptFromArray is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecateSetLayoutJavascriptFromArray() {
        try {
            $method = self::$strutsReflectionInstance->getMethod("setLayoutJavascriptFromArray");
            $method->invoke(self::$strutsInstance, array('script.js', 'script1.js'), null);
        }catch (PHPUnit_Framework_Error_Warning $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * test should prove setLayoutJSWithCompression is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecateSetLayoutJSWithCompression() {
        try {
            $method = self::$strutsReflectionInstance->getMethod("setLayoutJSWithCompression");
            $method->invoke(self::$strutsInstance, array('script.js', 'script1.js'), array('script.js', 'script1.js'), null, 'example');
        }catch (PHPUnit_Framework_Error_Warning $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * test should prove processJSFilesForCompression is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecateProcessJSFilesForCompression() {
        try {
            $method = self::$strutsReflectionInstance->getMethod("processJSFilesForCompression");
            $method->setAccessible(true);
            $method->invoke(self::$strutsInstance, array('script.css', 'script1.css'), 'sitwide', null);
        }catch (PHPUnit_Framework_Error_Warning $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * test should prove setLayoutCSSFromArray is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecateSetLayoutCSSFromArray() {
        try {
            $method = self::$strutsReflectionInstance->getMethod("setLayoutCSSFromArray");
            $method->setAccessible(true);
            $method->invoke(self::$strutsInstance, array('script.css', 'script1.css'), null);
        }catch (PHPUnit_Framework_Error_Warning $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * test should prove setLayoutCSSWithCompression is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecateSetLayoutCSSWithCompression() {
        try {
            $method = self::$strutsReflectionInstance->getMethod("setLayoutCSSWithCompression");
            $method->setAccessible(true);
            $method->invoke(self::$strutsInstance, array('script.css', 'script1.css'), array('script.css', 'script1.css'), null, '/example');
        }catch (PHPUnit_Framework_Error_Warning $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * test should prove processCSSFilesForCompression is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecateProcessCSSFilesForCompression() {
        try {
            $method = self::$strutsReflectionInstance->getMethod("processCSSFilesForCompression");
            $method->setAccessible(true);
            $method->invoke(self::$strutsInstance, array('script.css', 'script1.css'), 'sitewide', null);
        }catch (PHPUnit_Framework_Error_Warning $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * test should prove createCompressedFile is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecateCreateCompressedFile() {
        try {
            $method = self::$strutsReflectionInstance->getMethod("createCompressedFile");
            $method->setAccessible(true);
            $method->invoke(self::$strutsInstance, 'example', array('script.css', 'script1.css'), null, 'sitewide');
        }catch (PHPUnit_Framework_Error_Warning $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
      * test should prove getCompressedFileName is deprecated
      *
      * @return void
      * @access public
      * @author Johnathan Pulos
      */
    public function testShouldDeprecateGetCompressedFileName() {
         try {
             $method = self::$strutsReflectionInstance->getMethod("getCompressedFileName");
             $method->setAccessible(true);
             $method->invoke(self::$strutsInstance, 'example');
         }catch (PHPUnit_Framework_Error_Warning $expected) {
             return;
         }
         $this->fail('An expected exception has not been raised.');
     }
     
    /**
     * test should prove printlayoutVars is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecatePrintlayoutVars() {
          try {
              $method = self::$strutsReflectionInstance->getMethod("printlayoutVars");
              $method->invoke(self::$strutsInstance);
          }catch (PHPUnit_Framework_Error_Warning $expected) {
              return;
          }
          $this->fail('An expected exception has not been raised.');
      }
      
    /**
     * test should prove emptyLayoutVars is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecateEmptyLayoutVars() {
       try {
           $method = self::$strutsReflectionInstance->getMethod("emptyLayoutVars");
           $method->invoke(self::$strutsInstance);
       }catch (PHPUnit_Framework_Error_Warning $expected) {
           return;
       }
       $this->fail('An expected exception has not been raised.');
    }
       
    /**
     * test should prove printPageVars is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecatePrintPageVars() {
        try {
            $method = self::$strutsReflectionInstance->getMethod("printPageVars");
            $method->invoke(self::$strutsInstance);
        }catch (PHPUnit_Framework_Error_Warning $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * test should prove emptyPageVars is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecateEmptyPageVars() {
     try {
         $method = self::$strutsReflectionInstance->getMethod("emptyPageVars");
         $method->invoke(self::$strutsInstance);
     }catch (PHPUnit_Framework_Error_Warning $expected) {
         return;
     }
     $this->fail('An expected exception has not been raised.');
    }
         
    /**
     * test should prove setPageElement is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecateSetPageElement() {
      try {
          $method = self::$strutsReflectionInstance->getMethod("setPageElement");
          $method->invoke(self::$strutsInstance, '/design/pages/example.html');
      }catch (PHPUnit_Framework_Error_Warning $expected) {
          return;
      }
      $this->fail('An expected exception has not been raised.');
    }
          
    /**
     * test should prove setLayoutElement is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecateSetLayoutElement() {
        try {
           $method = self::$strutsReflectionInstance->getMethod("setLayoutElement");
           $method->invoke(self::$strutsInstance, '/design/pages/example.html');
        }catch (PHPUnit_Framework_Error_Warning $expected) {
           return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * test should prove renderLayout is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecateRenderLayout() {
        try {
            $method = self::$strutsReflectionInstance->getMethod("renderLayout");
            $method->invoke(self::$strutsInstance);
        }catch (PHPUnit_Framework_Error_Warning $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
            
    /**
     * test should prove prepareFile is deprecated
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testShouldDeprecatePrepareFile() {
         try {
             $method = self::$strutsReflectionInstance->getMethod("prepareFile");
             $method->setAccessible(true);
             $method->invoke(self::$strutsInstance, '/design/pages/example.html');
         }catch (PHPUnit_Framework_Error_Warning $expected) {
             return;
         }
         $this->fail('An expected exception has not been raised.');
    }
}
?>