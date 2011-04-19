<?php
/**
 * Handles all configuration for the STRUTS Engine.  It sets and gets global configuration variables for easier access.
 *
 * @package STRUTS
 * @author Technoguru Aka. Johnathan Pulos
 */
class Configure{
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
	 * Only allow one instance of this class.  To setup this class use Configure::scaffold()
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
            self::$configureInstance = new configure(); 
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
	    if($this->{$key}) {
	     return $this->{$key};   
	    }else {
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
	    if(($this->directories) && (array_key_exists($dir, $this->directories) && !empty($this->directories[$dir]))) {
	        return $this->directories[$dir];
	    }else {
	        return $this->defaultSettings['directories'][$dir];
	    }
	}
}
?>