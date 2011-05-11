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
	 * A Factory for global settings
	 *
	 * @var array
	 */
	private $factoryGlobalSettings = array(     'domain' => 'http://test.local',
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
                                                'enable_caching' => false
	                                        );
	/**
	 * Factory for page settings
	 *
	 * @var Array
	 */
	private $factoryPageSettings = array(   'title' => 'Test Page',
                                            'description' => 'Test Description',
                                            'keywords' => 'Test Keywords',
                                            'template' => '',
                                            'javascript' => '',
                                            'css' => '',
                                            'landing_page' => false,
                                            'cache' => true
	                                    );
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
     * Set a value for a class property
     *
     * @param string $propertyName the name of the property
     * @param mixed $value the value to set it at
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function changeClassProperty($propertyName, $value) {
        $property = self::$templatingReflectionInstance->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue(self::$templatingInstance, $value);
    }
    /**
     * Get the value of a property
     *
     * @param string $propertyName The name of the class property
     * @return mixed
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function getValueOfProperty($propertyName) {
        $property = self::$templatingReflectionInstance->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue(self::$templatingInstance);
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
    
    /**
     * An error should be triggered if they are trying to add a restricted tag to setATemplateTag()
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldTriggerErrorWhenAddingRestrictedTagOnSetATemplateTag() {
        $restrictedTag = 'my_special_test_tag';
        $this->changeClassProperty('restrictedTags', array($restrictedTag));
        
        try {
            $method = self::$templatingReflectionInstance->getMethod("setATemplateTag");
            $method->invoke(self::$templatingInstance, $restrictedTag, 'test_value');
        }catch (PHPUnit_Framework_Error $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * setATemplateTag() should create a new template tag
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldAddATemplateTagOnSetATemplateTag() {
        $expectedTag = 'MyNewTemplateTag';
        $expectedValue = 'It Rocks';
        $templateTags = $this->getValueOfProperty('templateTags');
        $this->assertArrayNotHasKey($expectedTag, $templateTags);
        
        $method = self::$templatingReflectionInstance->getMethod("setATemplateTag");
        $method->invoke(self::$templatingInstance, $expectedTag, $expectedValue);
        $alteredTemplateTags = $this->getValueOfProperty('templateTags');
        
        $this->assertArrayHasKey($expectedTag, $alteredTemplateTags);
        /**
         * cleanup
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
         $this->changeClassProperty('templateTags', array());
    }
    
    /**
     * mergeWithTemplateTags() should merge an array of new tags with the templateTags class var
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldMergeArrayWithTemplateTagsOnMergeWithTemplateTags() {
        $expectedArrayOfTags = array('tag1' => 'Bearded Cow', 'tag2' => 'Kung Foo Cow');
        $this->changeClassProperty('templateTags', array());
        
        $method = self::$templatingReflectionInstance->getMethod("mergeWithTemplateTags");
        $method->setAccessible(true);
        $method->invoke(self::$templatingInstance, $expectedArrayOfTags);
        $alteredTemplateTags = $this->getValueOfProperty('templateTags');

        $this->assertEquals($alteredTemplateTags, $expectedArrayOfTags);
        /**
         * cleanup
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
         $this->changeClassProperty('templateTags', array());
    }
    
    /**
     * An error should be triggered if you try to add arestricted tag through mergeWithTemplateTags()
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldTriggerErrorUsingRestrictedTagOnMergeWithTemplateTags() {
        $restrictedTag = 'my_special_test_tag';
        $testingArray = array('my_special_test_tag' => 'frooger', 'BrownCow' => 'And How');
        $this->changeClassProperty('restrictedTags', array($restrictedTag));
        
        try {
            $method = self::$templatingReflectionInstance->getMethod("mergeWithTemplateTags");
            $method->setAccessible(true);
            $method->invoke(self::$templatingInstance, $testingArray);
        }catch (PHPUnit_Framework_Error $expected) {
            /**
             * cleanup
             *
             * @author Technoguru Aka. Johnathan Pulos
             */
             $this->changeClassProperty('restrictedTags', array());
             return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * Unneccesary tags should be removed when merging in an array of tags
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldRemoveUnneccessaryTagsOnMergeWithTemplateTags() {
        $arrayOfTags = array('tag1' => 'Bearded Cow', 'tag2' => 'Kung Foo Cow');
        $expectedArrayOfTags = array('tag2' => 'Kung Foo Cow');
        $this->changeClassProperty('templateTags', array());
        $this->changeClassProperty('unnecessaryTags', array('tag1'));
        
        $method = self::$templatingReflectionInstance->getMethod("mergeWithTemplateTags");
        $method->setAccessible(true);
        $method->invoke(self::$templatingInstance, $arrayOfTags);
        $alteredTemplateTags = $this->getValueOfProperty('templateTags');

        $this->assertEquals($alteredTemplateTags, $expectedArrayOfTags);
        /**
         * cleanup
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
         $this->changeClassProperty('templateTags', array());
          $this->changeClassProperty('unnecessaryTags', array());
    }
    
    /**
     * addSettingsToTemplateTags() should add the global settings
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldAddGlobalSettingsToTemplateTagsOnAddSettingsToTemplateTags() {
        $expectedTemplateTags = $this->factoryGlobalSettings;
        $this->changeClassProperty('templateTags', array());
        
        /**
         * Remove all unnecessary tags so they do not get stripped
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
        $this->changeClassProperty('unnecessaryTags', array());
        
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('global_settings')->andReturn($expectedTemplateTags);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('page_settings')->andReturn(array());
        
        $method = self::$templatingReflectionInstance->getMethod("addSettingsToTemplateTags");
        $method->setAccessible(true);
        $method->invoke(self::$templatingInstance);
        
        $alteredTemplateTags = $this->getValueOfProperty('templateTags');
        $this->assertEquals($alteredTemplateTags, $expectedTemplateTags);
        /**
         * cleanup
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
         $this->changeClassProperty('templateTags', array());
    }
    
    /**
     * addSettingsToTemplateTags() should add the page settings
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldAddPageSettingsToTemplateTagsOnAddSettingsToTemplateTags() {
        $expectedTemplateTags = $this->factoryPageSettings;
        $this->changeClassProperty('templateTags', array());
        
        /**
         * Remove all unnecessary tags so they do not get stripped
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
        $this->changeClassProperty('unnecessaryTags', array());
        
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('global_settings')->andReturn(array());
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('page_settings')->andReturn($expectedTemplateTags);
        
        $method = self::$templatingReflectionInstance->getMethod("addSettingsToTemplateTags");
        $method->setAccessible(true);
        $method->invoke(self::$templatingInstance);
        
        $alteredTemplateTags = $this->getValueOfProperty('templateTags');
        $this->assertEquals($alteredTemplateTags, $expectedTemplateTags);
        /**
         * cleanup
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
         $this->changeClassProperty('templateTags', array());
    }
    
    /**
     * addSettingsToTemplateTags() should merge page settings and global settings allowing page settings to overwrite global settings on shared keys
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldAllowPageSettingsToOverwriteGlobalSettingsOnAddSettingsToTemplateTags() {
        $expectedPageTitle = 'My New Page Is Awesome';
        $expectedPageDescription = "A Really Awesome Page!";
        $pageSettings = $this->factoryPageSettings;
        $pageSettings['title'] = $expectedPageTitle;
        $pageSettings['description'] = $expectedPageDescription;
        $globalSettings = $this->factoryGlobalSettings;
        $globalSettings['title'] = "A stupid title.";
        $globalSettings['description'] = "A stupid description!";
        $this->changeClassProperty('templateTags', array());
        
        /**
         * Remove all unnecessary tags so they do not get stripped
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
        $this->changeClassProperty('unnecessaryTags', array());
        
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('global_settings')->andReturn($globalSettings);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('page_settings')->andReturn($pageSettings);
        
        $method = self::$templatingReflectionInstance->getMethod("addSettingsToTemplateTags");
        $method->setAccessible(true);
        $method->invoke(self::$templatingInstance);
        
        $alteredTemplateTags = $this->getValueOfProperty('templateTags');
        $this->assertEquals($alteredTemplateTags['title'], $expectedPageTitle);
        $this->assertEquals($alteredTemplateTags['description'], $expectedPageDescription);
        /**
         * cleanup
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
         $this->changeClassProperty('templateTags', array());
    }
    
    /**
     * on findRestricted() should trigger error if a restricted tag is found
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldTriggerErrorIfRestrictedTagFoundOnFindRestricted() {
        $restrictedTag = 'my_special_restricted_tag';
        $testingArray = array('my_special_restricted_tag' => 'frooger', 'BrownCow' => 'And How');
        $this->changeClassProperty('restrictedTags', array($restrictedTag));
        
        try {
            $method = self::$templatingReflectionInstance->getMethod("findRestricted");
            $method->setAccessible(true);
            $method->invoke(self::$templatingInstance, $testingArray);
        }catch (PHPUnit_Framework_Error $expected) {
            /**
             * cleanup
             *
             * @author Technoguru Aka. Johnathan Pulos
             */
             $this->changeClassProperty('restrictedTags', array());
             return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * removeUnnecessaryTags() should remove tags labled unnecessary
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldRemoveUnnecessaryTagsOnRemoveUnnecessaryTags() {
        $arrayOfTags = array('tag1' => 'Bearded Cow', 'tag2' => 'Kung Foo Cow');
        $expectedArrayOfTags = array('tag2' => 'Kung Foo Cow');
        $this->changeClassProperty('unnecessaryTags', array('tag1'));

        $method = self::$templatingReflectionInstance->getMethod("removeUnnecessaryTags");
        $method->setAccessible(true);
        $result = $method->invoke(self::$templatingInstance, $arrayOfTags);

        $this->assertEquals($result, $expectedArrayOfTags);
        /**
         * cleanup
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
         $this->changeClassProperty('unnecessaryTags', array());
    }
    
    /**
     * processJavascript() should create the correct JS tags
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldCreateJSTagsOnProcessJavascript() {
        $expected = "<script src=\"/js/test1.js\"></script>\r\n<script src=\"/js/test2.js\"></script>\r\n";
        $pageSettings = $this->factoryPageSettings;
        $pageSettings['javascript'] = 'test2.js';
        $globalSettings = $this->factoryGlobalSettings;
        $globalSettings['compress_js'] = false;
        $globalSettings['javascript'] = 'test1.js';
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('global_settings')->andReturn($globalSettings);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('page_settings')->andReturn($pageSettings);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('debug_level')->andReturn(3);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('js_tag_format')->andReturn("<script src=\"%s\"></script>\r\n");
        self::$configureMock->shouldReceive('getDirectory')->times(1)->with('js', false)->andReturn("js");
        
        $method = self::$templatingReflectionInstance->getMethod("processJavascript");
        $method->setAccessible(true);
        $method->invoke(self::$templatingInstance);
        $result = $this->getValueOfProperty('templateTags');
        $this->assertEquals($expected, $result['strutJavascript']);
        /**
         * cleanup
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
         $this->changeClassProperty('templateTags', array());
    }
    
    /**
     * processStylesheets() should create the correct CSS tags
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldCreateCSSTagsOnProcessStylesheets() {
        $expected = "<link rel=\"stylesheet\" href=\"/css/test1.css\">\r\n<link rel=\"stylesheet\" href=\"/css/test2.css\">\r\n";
        $pageSettings = $this->factoryPageSettings;
        $pageSettings['css'] = 'test2.css';
        $globalSettings = $this->factoryGlobalSettings;
        $globalSettings['compress_css'] = false;
        $globalSettings['css'] = 'test1.css';
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('global_settings')->andReturn($globalSettings);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('page_settings')->andReturn($pageSettings);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('debug_level')->andReturn(3);
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('css_tag_format')->andReturn("<link rel=\"stylesheet\" href=\"%s\">\r\n");
        self::$configureMock->shouldReceive('getDirectory')->times(1)->with('css', false)->andReturn("css");
    
        $method = self::$templatingReflectionInstance->getMethod("processStylesheets");
        $method->setAccessible(true);
        $method->invoke(self::$templatingInstance);
        $result = $this->getValueOfProperty('templateTags');
        $this->assertEquals($expected, $result['strutCSS']);
        /**
         * cleanup
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
         $this->changeClassProperty('templateTags', array());
    }
    
    /**
     * getResources() should create an array of resources based on page specific and global resources
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldCreateAppropriateArayOfResourcesOnGetResources() {
        $expectedArray = array('/my_js/test1.css', '/my_js/test2.css', '/my_js/test3.css');
        $pageSpecific = 'test2.css,test3.css';
        $globalSpecific = 'test1.css';
        $directory = 'my_js';
        
        $method = self::$templatingReflectionInstance->getMethod("getResources");
        $method->setAccessible(true);
        $result = $method->invoke(self::$templatingInstance, $globalSpecific, $pageSpecific, $directory); 
        $this->assertEquals($expectedArray, $result);
    }
    /**
     * Tests the core of the templating engine, making sure content is parsed, and set to the strutFinalLayout template tag
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldSetStrutFinalContent() {
        $expectedStrutContent = "This is my content COW";
        self::$configureMock->shouldReceive('getLayout')->times(1)->andReturn('test.html');
        self::$configureMock->shouldReceive('getDirectory')->times(1)->with('layouts')->andReturn('engine/tests/test_routes/pages');
        self::$configureMock->shouldReceive('getSetting')->times(1)->with('utf8_encode')->andReturn(false);
        
        $this->changeClassProperty('templateTags', array('TEST' => 'COW'));
        
        $method = self::$templatingReflectionInstance->getMethod("setStrutFinalLayout");
        $method->setAccessible(true);
        $method->invoke(self::$templatingInstance);
        
        $result = $this->getValueOfProperty('templateTags');
        $this->assertEquals($expectedStrutContent, $result['strutFinalLayout']);
        /**
         * cleanup
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
         $this->changeClassProperty('templateTags', array());
    }
    /**
     * prepareFile() should throw an error if the page does not exist
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldTriggerErrorIfPageNotFoundOnPrepareFile() {
        try {
            $method = self::$templatingReflectionInstance->getMethod("prepareFile");
            $method->setAccessible(true);
            $method->invoke(self::$templatingInstance, APP_PATH . 'engine' . DS . 'tests' . DS . 'goofytestfile.txt');
        }catch (PHPUnit_Framework_Error $expected) {
             return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * prepareFile() should return the contents of a file
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldGetContentsOfFileOnPrepareFile() {
        $expectedResult = "This is my content ##TEST##";
        $method = self::$templatingReflectionInstance->getMethod("prepareFile");
        $method->setAccessible(true);
        $result = $method->invoke(self::$templatingInstance, APP_PATH . 'engine' . DS . 'tests' . DS . 'test_routes' . DS . 'pages' . DS . 'test.html');
        $this->assertEquals($expectedResult, $result);
    }
    
    /**
     * Test should process the template tags and replace with correct values
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldReplaceTemplateTagsOnProcessFileContent() {
        $expectedResult = "My cow love pink leotards and puffy hair.";
        $processor = "My ##animal## love ##color## ##clothing## and puffy ##body_feature##.";
        $this->changeClassProperty('templateTags', array('animal' => 'cow', 'color' => 'pink', 'clothing' => 'leotards', 'body_feature' => 'hair'));
        $method = self::$templatingReflectionInstance->getMethod("processFileContent");
        $method->setAccessible(true);
        $result = $method->invoke(self::$templatingInstance, $processor);
        $this->assertEquals($expectedResult, $result);
        /**
         * cleanup
         *
         * @author Technoguru Aka. Johnathan Pulos
         */
         $this->changeClassProperty('templateTags', array());
    }
}
?>