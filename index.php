<?php 
	/**
	 * This is the root file that controls the templating engine.  This file is called everytime a page is loaded.  It interfaces with the Struts Templating Engine, and provides
	 * you with ways to edit some of the root settings.
	 *
	 * @author 		Technoguru Aka. Johnathan Pulos
	 * @version 	2
	 * @copyright 	11 June, 2010
	 * @package 	STRUTS
	 **/
	/**
	 * Start all the sessions
	 *
	 * @author Johnathan Pulos
	 */
    session_start(); 
    /**
     * Define global vars for the engine
     */
    /**
     * Define the DOMAIN for the current site
     */
    define("DOMAIN", "http://struts.local/");
    /**
     * Define the directory seperator type for the current file system
     */
    define("DS", '/');
    /**
     * Defines the root directory for this application
     */
    define("APP_PATH", dirname(__FILE__) .DS);

    /**
     * Include the Struts Templating Engine
     *
     * @author Technoguru Aka. Johnathan Pulos
     */
	include("engine/struts_engine.class.php");
    /**
     * Setup the strutsEngine object using the singleton method provided.
     *
     * @author Technoguru Aka. Johnathan Pulos
     */
	$newStrut = strutsEngine::init();
	/**
	 * SETTINGS
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	/**
	 * Define the default layout for the whole application,  if a page needs a specific layout, you can set the 'template' setting for that page in the site.yml.
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$newStrut->setSetting('default_layout', 'example.html');
	/**
	 * Define where the settings file (site.yml) is located.
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$newStrut->setSetting('settings_file', 'settings/site.yml');
	/**
	 * Define where your database file is located.  Pleas note no database functionality is installed in this application.  Would love to get PHP Active Record working with this.
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$newStrut->setSetting('database_file', 'settings/database.inc.php');
	/**
	 * Define the length of time to refrence a cache file.  In seconds.
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$newStrut->setSetting('cache_time', 600);
	/**
	 * Define the extension to use for the cache files.
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$newStrut->setSetting('cache_ext', 'cache');
	/**
	 * Define how long to hold a log file before overwriting it.  In seconds.
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$newStrut->setSetting('retain_logs', 600);
	/**
	 * should the page be utf8 encoded
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$newStrut->setSetting('utf8_encode', false);
	/**
	 * Set the level of debugging.
	 *
	 * 4 - Display full stack trace when an error occurs, but do not write the trace to the log files (development), Caching disabled, Compression disabled 
	 * 3 - Display full stack trace when an error occurs, and write the trace to the log files (development), Caching disabled, Compression disabled
 	 * 2 - Write stack trace into log file (development), Caching Disabled, Compression disabled
	 * 1 - Write stack trace into log file, Caching Enabled, Compression enabled
	 * 0 - do nothing, Caching Enabled, Compression enabled
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$newStrut->setSetting('debug_level', 3);
	/**
	 * Define the html for the javascript tags that can implemented in the ##strutJavascript##.
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$newStrut->setSetting('js_tag_format', "<script src=\"%s\"></script>\r\n");
	/**
	 * Define the html for the css tags that get implemented on the ##strutCSS##.
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$newStrut->setSetting('css_tag_format', "<link rel=\"stylesheet\" href=\"%s\">\r\n");
	/**
	 * Define the name to use for the site wide compressed file.
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$newStrut->setSetting('sitewide_compressed_filename', "sitewide.min");
	/**
	 * Directory settings.  To change these,  just uncommet the directory you would like to change.
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$strutDirectories = array();
	/**
	 * Location for cache files
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
    //$strutDirectories['cache'] = 'tmp/';
    /**
     * Location of the page templates.
     *
     * @author Technoguru Aka. Johnathan Pulos
     */
    //$strutDirectories['pages'] = 'design/pages';
    /**
     * Location of the PHP code for each page
     *
     * @author Technoguru Aka. Johnathan Pulos
     */
    //$strutDirectories['pages_code'] = 'code/pages';
    /**
     * Location for the modules for the template files
     *
     * @author Technoguru Aka. Johnathan Pulos
     */
    //$strutDirectories['modules'] = 'design/modules';
    /**
     * Location for code to be used for setting modules
     *
     * @author Technoguru Aka. Johnathan Pulos
     */
    //$strutDirectories['modules_code'] = 'code/modules';
    /**
     * Lcation of the layout files.
     *
     * @author Technoguru Aka. Johnathan Pulos
     */
    //$strutDirectories['layouts'] = 'design/layouts';
    /**
     * Location of the CSS files
     *
     * @author Technoguru Aka. Johnathan Pulos
     */
    //$strutDirectories['css'] = 'design/css';
    /**
     * Location of the javascript files
     *
     * @author Technoguru Aka. Johnathan Pulos
     */
    //$strutDirectories['js'] = 'design/js';
    /**
     * Set the final directories
     *
     * @author Technoguru Aka. Johnathan Pulos
     */
	//$newStrut->setSetting('directories', $strutDirectories);
	/**
	 * The requested page is sent in the url using a GET param url.  Pass it to the processRequest method.
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$page_url = (isset($_GET['url'])) ? trim($_GET['url']) : 'index.html';
	$newStrut->processRequest($page_url);
	/**
	 * This is the perfect time to start adding your php includes and your module includes.  To add a module,  use the strutEngine::setTemplateTag().  You can access the current_page attribute
	 * using the strutEngine::getSetting() method.
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$currentPage = $newStrut->getSetting('current_page');
    /**
     * Include the database file if it exists
     *
     * @author Technoguru Aka. Johnathan Pulos
     */
    $databaseFile = APP_PATH . str_replace("/", DS, $newStrut->getSetting('database_file'));
    if(file_exists($databaseFile)){
	    require_once($databaseFile);
	 }
	/**
     * If the page requested has a PHP functionality file, then include and run it.
     *
     * @author Johnathan Pulos
     */
	if(!empty($currentPage)) {
	    $phpFile = APP_PATH . $currentPage['php_file'];
    	if(file_exists($phpFile)){
    	    include(str_replace('/', DS, $phpFile));
    	}
	}
	 /**
	  * Declare any modules/page vars to the Struts Templating Engine you need access to on the layout design page 
	  *
	  * @author Johnathan Pulos
	  */
    $moduleDirectory = APP_PATH . $newStrut->getDirectory('modules_code', true);
    if(file_exists($moduleDirectory . DS . 'modules.inc.php')){
        $module_directory = APP_PATH . $newStrut->getDirectory('modules', true);
	    require_once($moduleDirectory . DS . 'modules.inc.php');
	 }
	/**
	 * Now render the final request.  This should be the last thing called.  Nothing after that.
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$newStrut->renderRequest();
?>