<?php
/**
 * Handles all configuration for the STRUTS Engine.  It sets and gets global configuration variables for easier access.
 *
 * @package STRUTS
 * @author Technoguru Aka. Johnathan Pulos
 */
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
                                                                'layouts' => 'design/layouts',
                                                                'css' => '/design/css',
                                                                'js' => '/design/js',
                                                                'elements' => 'design/elements'                  
                                        )
    );
    /**
	 * The singleton instance of the configure class
	 *
	 * @var Object
	 */
	private static $configureInstance;
	/**
	 * The singleton instance of the logging class
	 *
	 * @var Object
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
	 * @return string
	 * @access public
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	public function getDirectory($dir) {
	    self::trace('Starting getDirectory("'.$dir.'")', __LINE__);
	    if(($this->directories) && (array_key_exists($dir, $this->directories) && !empty($this->directories[$dir]))) {
	        $directory = $this->directories[$dir];
	        self::trace('<em>getDirectory() Returning</em> - '.$directory, __LINE__);
	        return $directory;
	    }else {
	        if(array_key_exists($dir, $this->defaultSettings['directories']) && !empty($this->defaultSettings['directories'][$dir])) {
	            $directory = $this->defaultSettings['directories'][$dir];
    	        self::trace('<em>getDirectory() Returning Default</em> - '.$directory, __LINE__);
    	        return $directory;
	        }else {
    	        self::trace('<em>getDirectory() Returning Default</em> - empty string (directory not set)', __LINE__);
    	        return '';
	        }
	    }
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