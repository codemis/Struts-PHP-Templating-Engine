<?php
/**
 * Handles all configuration for the STRUTS Engine.  It sets and gets global configuration variables for easier access.
 *
 * @package STRUTS
 * @author Technoguru Aka. Johnathan Pulos
 */
require_once(APP_PATH . 'engine' . DS . 'vendors' . DS . 'spyc' . DS . 'spyc.php');
class Configure
{
    /**
     * An array of default settings, to set these use strutsEngine::setSetting()
     *
     * @var array
     * @access private
     */
    private $defaultSettings = array(   'default_layout' => 'main.html',
                                        'settings_file' => 'settings/site.yml',
                                        'database_file' => 'settings/database.inc.php',
                                        'cache_time' => 600,
                                        'cache_ext' => 'cache',
                                        'directories' => array( 'cache' => 'tmp/',
                                                                'pages' => 'design/pages',
                                                                'pages_code' => 'code/pages',
                                                                'modules' => 'design/modules',
                                                                'modules_code' => 'code/modules',
                                                                'layouts' => 'design/layouts',
                                                                'css' => 'design/css',
                                                                'js' => 'design/js',
                                                                'elements' => 'design/elements'                  
                                        )
    );
    /**
     * The current settings array parsed from the settings yaml file
     *
     * @var array
     * @access public
     */
    public $SPYCSettings = array();
    /**
	 * The singleton instance of the configure class
	 *
	 * @var Object
	 * @access private
	 */
	private static $configureInstance;
	/**
	 * The singleton instance of the logging class
	 *
	 * @var Object
	 * @access private
	 */
	public static $loggingInstance;
	
	/**
	 * Only allow one instance of this class.  To setup this class use Configure::init()
	 *
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function __construct() {
	}
	
	/**
     * prevent cloning of the object: issues an E_USER_ERROR if this is attempted
     *
 	 * @access public
     * @author Technoguru Aka. Johnathan Pulos
     */
    public function __clone()
    {
        trigger_error('Cloning the STRUT is not permitted.', E_USER_ERROR);
    }
	
	/**
	 * setup the Configure class
	 *
	 * @return object
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function init() { 
        if (!self::$configureInstance) { 
            self::$configureInstance = new Configure();
        }
        return self::$configureInstance;
	}
	
	/**
	 * Set the configuration settings
	 *
	 * @param string $key The configuration key
	 * @param mixed $value The configuration value
	 * @return void
 	 * @access public
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function setSetting($key, $value) {
	    $printed_value = (is_array($value)) ? var_export($value,true) : $value;
	    self::trace('Starting setSetting("'.$key.'", "'.$printed_value.'")', __LINE__);
	    $this->{$key} = $value;
	}
	
	/**
	 * Get the configuration settings
	 *
	 * @param string $key The configuration key
	 * @return mixed
 	 * @access public
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function getSetting($key) {
	    self::trace('Starting getSetting("'.$key.'")', __LINE__);
	    if($this->{$key}) {
	        $printed_value = (is_array($this->{$key})) ? var_export($this->{$key},true) : $this->{$key};
	        self::trace('<em>getSetting() Returning</em> - '.$printed_value, __LINE__);
	        return $this->{$key};   
	    }else {
	        self::trace('<em>getSetting() Returning</em> - null (variable not set)', __LINE__);
	        return null;
	    }
	}
	
	/**
	 * Get a specific diretory.  If the directory was not set, then return the default directory
	 *
	 * @param string $dir directory your looking for
	 * @param boolean $forRequire Is the directory for a require statement,  if so the directory seprator is replaced with correct seperator
	 * @return string
	 * @access public
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function getDirectory($dir, $forRequire = false) {
	    self::trace('Starting getDirectory("'.$dir.'")', __LINE__);
	    $directory = '';
	    if(($this->directories) && (array_key_exists($dir, $this->directories) && !empty($this->directories[$dir]))) {
	        $directory = $this->directories[$dir];
	        self::trace('<em>getDirectory() Returning</em> - '.$directory, __LINE__);
	    }else {
	        if(array_key_exists($dir, $this->defaultSettings['directories']) && !empty($this->defaultSettings['directories'][$dir])) {
	            $directory = $this->defaultSettings['directories'][$dir];
    	        self::trace('<em>getDirectory() Returning Default</em> - '.$directory, __LINE__);
	        }else {
    	        self::trace('<em>getDirectory() Returning Default</em> - empty string (directory not set)', __LINE__);
	        }
	    }
	    $directory = ($forRequire === true) ? str_replace('/', DS, $directory) : $directory;
	    return $directory;
	}
	
	/**
	 * Determine which layout file should be used.  If one is set in site.yml, then use it, otherwise use the default_layout.
	 *
	 * @return string
	 * @access public
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function getLayout() {
	    $defaultLayout = $this->getSetting('default_layout');
        if(isset($this->SPYCSettings[$this->current_page['page']]) && !empty($this->SPYCSettings[$this->current_page['page']]['template'])) {
            return $this->SPYCSettings[$this->current_page['page']]['template'];
        }else if(!empty($defaultLayout)) {
            return $defaultLayout;
        }else {
           trigger_error('default_layout and template setting for the page are both missing.', E_USER_ERROR); 
        }
	}

	/**
	 * Get/Set the global specific settings from the settings.yml
	 * 
	 * @return void
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function initGlobalSettings() {
	    self::trace('Starting initGlobalSettings()', __LINE__);
        $this->setSetting('global_settings', $this->SPYCSettings['global']);
    	self::trace('Ending initGlobalSettings()', __LINE__);
	}
		
	/**
	 * Get/Set the page specific settings from the settings.yml
	 * 
	 * @return void
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function initPageSettings() {
	    self::trace('Starting initPageSettings()', __LINE__);
	    if(!$this->current_page) {
	        trigger_error('You must first set Setting[current_page] before calling this method.', E_USER_ERROR);
	    }
	    $current_page = $this->current_page['page'];
	    /**
    	 * If they append a index,  see if it is valid, if not remove it
    	 * @todo goes into the configuration file
    	 * @author Johnathan Pulos
    	 */
    	if (strpos($current_page, 'index') != false) {
    	    if(!array_key_exists($current_page, $this->SPYCSettings)) {
    			$current_page = substr($current_page, 0, strrpos($current_page, '/index')); 
    		}
    	}
    	/**
    	 * If the page does not have a setting, then use the global settings
    	 *
    	 * @author Johnathan Pulos
    	 */
    	if(array_key_exists($this->current_page['page'], $this->SPYCSettings)){
    	    $page_settings = $this->SPYCSettings[$this->current_page['page']];
    	}else {
    	    $page_settings = $this->SPYCSettings['global'];
    	}
        $this->setSetting('page_settings', $page_settings);
    	self::trace('Ending initPageSettings()', __LINE__);
	}
	
	/**
	 * If SPYCSettings is empty then pars the YAML and set the class var
	 *
	 * @return void
	 * @access private
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function setSPYCSettings() {
	    self::trace('Starting setSPYCSettings()', __LINE__);
	    if(empty($this->SPYCSettings)) {
            $settingsFile = APP_PATH . str_replace('/', DS, $this->getSetting('settings_file'));
        	self::trace('Setting class var SPYCSettings from file: '.$settingsFile, __LINE__);
        	if(file_exists($settingsFile)) {
        	    $this->SPYCSettings = Spyc::YAMLLoad($settingsFile);   
        	}else {
        	    trigger_error('Unable to find the settings file at '.$settingsFile.'.', E_USER_ERROR);
        	}
        }
        self::trace('Ending setSPYCSettings()', __LINE__);
	}
	
	/**
	 * convienence method for logging traces
	 *
	 * @param string $message message to add to stack 
	 * @return void
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	private function trace($message, $line = '') {
	    $this->loggingInstance->logTrace('<strong>Configure (line# '.$line.')</strong>: '.$message);
	}

}
?>