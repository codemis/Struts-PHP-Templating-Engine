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
     * Sets up the testing yaml file for page and global settings
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function setupTestYamlFileSettings() {
        $method = self::$configureReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$configureInstance, 'settings_file', 'engine/tests/test_settings/settings.yml');
        $method = self::$configureReflectionInstance->getMethod("setSPYCSettings");
        $result = $method->invoke(self::$configureInstance);
        
        $method = self::$configureReflectionInstance->getMethod("initGlobalSettings");
        $method->invoke(self::$configureInstance);
        $method = self::$configureReflectionInstance->getMethod("initPageSettings");
        $method->invoke(self::$configureInstance);
    }
    
    /**
     * Tear down the testing yaml file for page and global settings
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function tearDownTestYamlFileSettings() {
        $method = self::$configureReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$configureInstance, 'settings_file', 'settings/site.yml');
        $spyc_settings = self::$configureReflectionInstance->getProperty("SPYCSettings");
        $spyc_settings->setValue(self::$configureInstance, array());
        
        $method->invoke(self::$configureInstance, 'global_settings', array());
        $method->invoke(self::$configureInstance, 'page_settings', array());
        $method->invoke(self::$configureInstance, 'current_page', array());
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
        self::$configureReflectionInstance = '';
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
    
    /**
     * getDirectory() should hand user over a default directory if they did not set one
     * NOTE: This test must remain above testShouldReturnAUserSetDirectoryOnGetDirectory() test
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldReturnADefaultDirectoryOnGetDirectory() {
        $expectedKey = "directories";
        $expectedValue = 'tmp/';
       
        $method = self::$configureReflectionInstance->getMethod("getDirectory");
        $result = $method->invoke(self::$configureInstance, 'cache', false);
        $this->assertEquals($result, $expectedValue);
    }
    
    /**
     * getDirectory() should hand user over their specified directory if they set one
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldReturnAUserSetDirectoryOnGetDirectory() {
        $expectedKey = "directories";
        $expectedValue = array('cache' => 'my_new_cache_dir/');
        $method = self::$configureReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$configureInstance, $expectedKey, $expectedValue);
        
        $method = self::$configureReflectionInstance->getMethod("getDirectory");
        $result = $method->invoke(self::$configureInstance, 'cache', false);
        $this->assertEquals($expectedValue['cache'], $result);
    }

    /**
     * getLayout() should return a default layout if the YML settings are not set
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldReturnDefaultLayoutOnGetLayout() {
        $expectedLayout = "main.html";
        
        $method = self::$configureReflectionInstance->getMethod("getLayout");
        $result = $method->invoke(self::$configureInstance);
        $this->assertEquals($expectedLayout, $result);
    }
    
    /**
     * If a page template is set,  getLayout() should bypass the global layout and hand over the page specific template
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldReturnPageLayoutIfIssetOnGetLayout() {
        $expectedLayout = "my_new_test_layout.html";
        $current_page = $this->expectedCurrentPage;
        $current_page['page'] = "test_with_layout";
        $method = self::$configureReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$configureInstance, 'current_page', $current_page);
        $this->setupTestYamlFileSettings();
         
        $method = self::$configureReflectionInstance->getMethod("getLayout");
        $result = $method->invoke(self::$configureInstance);
        $this->assertEquals($expectedLayout, $result);
        
        $this->tearDownTestYamlFileSettings();
    }
    
    /**
     * If the page does not have a specific template, then it should use the global setting unsless not specified
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldReturnGlobalLayoutIfIssetOnGetLayout() {
        $expectedLayout = "test_layout.html";
        $current_page = $this->expectedCurrentPage;
        $current_page['page'] = "test";
        $method = self::$configureReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$configureInstance, 'current_page', $current_page);
        $this->setupTestYamlFileSettings();
         
        $method = self::$configureReflectionInstance->getMethod("getLayout");
        $result = $method->invoke(self::$configureInstance);
        $this->assertEquals($expectedLayout, $result);
        
        $this->tearDownTestYamlFileSettings();
    }
    
    /**
     * initGlobalSettings should setup the class var global_settings
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldSetGlobalSettingsOnInitGlobalSettings() {
        $global_settings_prop = self::$configureReflectionInstance->getProperty("global_settings");
        $global_settings_prop->setAccessible(true);
        $global_settings = $global_settings_prop->getValue(self::$configureInstance);
        $this->assertTrue(empty($global_settings));
        
        $method = self::$configureReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$configureInstance, 'settings_file', 'engine/tests/test_settings/settings.yml');
        $method = self::$configureReflectionInstance->getMethod("setSPYCSettings");
        $result = $method->invoke(self::$configureInstance);
        
        $method = self::$configureReflectionInstance->getMethod("initGlobalSettings");
        $method->invoke(self::$configureInstance);
        
        $global_settings_prop = self::$configureReflectionInstance->getProperty("global_settings");
        $global_settings_prop->setAccessible(true);
        $global_settings = $global_settings_prop->getValue(self::$configureInstance);
        $this->assertFalse(empty($global_settings));
        
        $this->tearDownTestYamlFileSettings();
    }
    
    /**
     * If current_page is not set initPageSettings() should throw an error
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldThrowErrorIfCurrentPageNotSetOnInitPageSettings() {
        $current_page_prop = self::$configureReflectionInstance->getProperty("current_page");
        $current_page_prop->setAccessible(true);
        $current_page = $current_page_prop->getValue(self::$configureInstance);
        $this->assertTrue(empty($current_page));
        
        try {
            $method = self::$configureReflectionInstance->getMethod("initPageSettings");
            $method->invoke(self::$configureInstance);
        }catch (PHPUnit_Framework_Error $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * If index is in the current page,  it should be removed before setting the page_settings var
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldRemoveIndexBeforeSettingOnInitPageSettings() {
        $expectedPageTitle = 'Test Page Data';
        $current_page = $this->expectedCurrentPage;
        $current_page['page'] = "test/index";
        $method = self::$configureReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$configureInstance, 'current_page', $current_page);
        $this->setupTestYamlFileSettings();
        
        $page_settings_prop = self::$configureReflectionInstance->getProperty("page_settings");
        $page_settings_prop->setAccessible(true);
        $page_settings = $page_settings_prop->getValue(self::$configureInstance);
        $this->assertEquals($expectedPageTitle, $page_settings['title']);
        
        $this->tearDownTestYamlFileSettings();
    }
    
    /**
     * initPageSettings should set the page_settings var
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldSetPageSettingsOnInitPageSettings() {
        $page_settings_prop = self::$configureReflectionInstance->getProperty("page_settings");
        $page_settings_prop->setAccessible(true);
        $page_settings = $page_settings_prop->getValue(self::$configureInstance);
        $this->assertTrue(empty($page_settings));
        
        $expectedPageTitle = 'Test Page Data';
        $current_page = $this->expectedCurrentPage;
        $current_page['page'] = "test";
        $method = self::$configureReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$configureInstance, 'current_page', $current_page);
        $method->invoke(self::$configureInstance, 'settings_file', 'engine/tests/test_settings/settings.yml');
        
        $method = self::$configureReflectionInstance->getMethod("setSPYCSettings");
        $result = $method->invoke(self::$configureInstance);
        
        $method = self::$configureReflectionInstance->getMethod("initPageSettings");
        $method->invoke(self::$configureInstance);
        
        $page_settings_prop = self::$configureReflectionInstance->getProperty("page_settings");
        $page_settings_prop->setAccessible(true);
        $page_settings = $page_settings_prop->getValue(self::$configureInstance);
        $this->assertFalse(empty($page_settings));
        
        $this->tearDownTestYamlFileSettings();
    }
    
    /**
     * setSPYCSettings() should get the settings YML and parse the data
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldSetSpycSettingsOnSetSPYCSettings() {
        $spyc_settings_prop = self::$configureReflectionInstance->getProperty("SPYCSettings");
        $spyc_settings_prop->setAccessible(true);
        $spyc_settings = $spyc_settings_prop->getValue(self::$configureInstance);
        $this->assertTrue(empty($spyc_settings));
        $method = self::$configureReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$configureInstance, 'settings_file', 'engine/tests/test_settings/settings.yml');
        
        $method = self::$configureReflectionInstance->getMethod("setSPYCSettings");
        $method->invoke(self::$configureInstance);
        
        $spyc_settings_prop = self::$configureReflectionInstance->getProperty("SPYCSettings");
        $spyc_settings_prop->setAccessible(true);
        $spyc_settings = $spyc_settings_prop->getValue(self::$configureInstance);
        $this->assertFalse(empty($spyc_settings));
        
        $this->tearDownTestYamlFileSettings();
    }
    
    /**
     * If it is not able to find the settings file setSPYCSettings() should throw an error
     *
     * @return void
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function testShouldThrowErrorIfSettingsFileDoesNotExistOnSetSPYCSettings() {
        $method = self::$configureReflectionInstance->getMethod("setSetting");
        $method->invoke(self::$configureInstance, 'settings_file', 'engine/tests/settings.yml');
        try {
            $method = self::$configureReflectionInstance->getMethod("setSPYCSettings");
            $method->invoke(self::$configureInstance);
        }catch (PHPUnit_Framework_Error $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

}
?>