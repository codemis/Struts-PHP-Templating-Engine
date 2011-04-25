<?php 
	/**
	 * This is the root file that controls the templating engine.  This file is called everytime a page is loaded.  It interfaces with the Struts Templating Engine
	 * and gets all settings from the settings.settings.yml file.  Please do not edit this file.  it may cause the site to go down.
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
    define("DOMAIN", "http://struts.local");
    define("DS", '/');
    define("APP_PATH", dirname(__FILE__) .DS);

    /**
     * Include the Struts Templating Engine, and the spyc class that reads the yaml settings
     *
     * @author Technoguru Aka. Johnathan Pulos
     */
	include("engine/struts_engine.class.php");
    /**
     * Setup the strutsEngine object
     *
     * @author Technoguru Aka. Johnathan Pulos
     */
	$newStrut = strutsEngine::init();
	/**
	 * SETTINGS
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$newStrut->setSetting('default_layout', 'example_test.html');
	$newStrut->setSetting('settings_file', 'settings/site.yml');
	$newStrut->setSetting('database_file', 'settings/database.inc.php');
	$newStrut->setSetting('cache_time', 600); //Seconds
	$newStrut->setSetting('cache_ext', 'cache');
	$newStrut->setSetting('retain_logs', 600); //Seconds
	/**
	 * Set the level of debugging.
	 *
	 * 4 - Display full stack trace when an error occurs, but do not write the trace to the log files (development), Caching disabled 
	 * 3 - Display full stack trace when an error occurs, and write the trace to the log files (development), Caching disabled
 	 * 2 - Write stack trace into log file (development), Caching Disabled
	 * 1 - Write stack trace into log file, Caching Enabled
	 * 0 - do nothing, Caching Enabled
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$newStrut->setSetting('debug_level', 3);
	$newStrut->setSetting('js_tag_format', "<script src=\"%s\"></script>\r\n");
	$newStrut->setSetting('css_tag_format', "<link rel=\"stylesheet\" href=\"%s\">\r\n");
	$strutDirectories = array();
    $strutDirectories['cache'] = 'tmp/';
    $strutDirectories['pages'] = 'design/pages';
    $strutDirectories['pages_code'] = 'code/pages';
    $strutDirectories['modules'] = 'design/modules';
    $strutDirectories['modules_code'] = 'code/modules';
    $strutDirectories['layouts'] = 'design/layouts';
    $strutDirectories['css'] = '/design/css';
    $strutDirectories['js'] = '/design/js';
    $strutDirectories['elements'] = 'design/elements';
	$newStrut->setSetting('directories', $strutDirectories);
	$page_url = (isset($_GET['url'])) ? trim($_GET['url']) : 'index.html';
	$newStrut->processRequest($page_url);
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
	$newStrut->renderRequest();
	trigger_error('Cloning the STRUT is not permitted.', E_USER_ERROR);
	
	/**
	 * @var	string	$js_files a string of all the global and page specific javascript files from the settings YAML
	 */
	$js_files = '';
	
	/**
	 * This if block determines what settings have been supplied for the specific page and global javascript files,  and populates the
	 * $js_files variable with a comma seperated string
	 */
	if((array_key_exists('javascript', $page_specific_settings)) && (!empty($page_specific_settings['javascript']))){
		if((array_key_exists('javascript', $settings['global'])) && (!empty($settings['global']['javascript']))){
			$sitewide_js_files = explode(',', $settings['global']['javascript']);
			$page_js_files = explode(',', $page_specific_settings['javascript']);
		}else{
			$sitewide_js_files = explode(',', $page_specific_settings['javascript']);
			$page_js_files = array();
		}
		
		/**
		 * IMPORTANT:: unset the $page_specific_settings['javascript'] so it will not become a variable on the layout
		 */
		unset($page_specific_settings['javascript']);
	}else{
		if((array_key_exists('javascript', $settings['global'])) && (!empty($settings['global']['javascript']))){
			$sitewide_js_files = explode(',', $settings['global']['javascript']);
			$page_js_files = array();
		}
	}
	
	/**
	 * Tell Struts to create the ##strutJavascript## based on the $js_files and whether compression setting is set or not 
	 */
	if($settings['global']['compress_js'] === true){
		$newStrut->setLayoutJSWithCompression($sitewide_js_files, $page_js_files, $settings['global']['js_compress_directory'], $page_url);
	}else{
		$js_files = array_merge($sitewide_js_files, $page_js_files);
		$newStrut->setLayoutJavascriptFromArray($js_files, $js_directory);
	}
	
	/**
	 * @var	string	$css_files a string of all the global and page specific css files from the settings YAML
	 */
	$css_files = '';
	
	/**
	 * This if block determines what settings have been supplied for the specific page and global css files,  and populates the
	 * $css_files variable with a comma seperated string
	 */
	if((array_key_exists('css', $page_specific_settings)) && (!empty($page_specific_settings['css']))){
		if((array_key_exists('css', $settings['global'])) && (!empty($settings['global']['css']))){
			$sitewide_css_files = explode(',', $settings['global']['css']);
			$page_css_files = explode(',', $page_specific_settings['css']);
		}else{
			$sitewide_css_files = explode(',', $page_specific_settings['css']);
			$page_css_files = array();
		}
		/**
		 * IMPORTANT:: unset the $page_specific_settings['css'] so it will not become a variable on the layout
		 */
		unset($page_specific_settings['css']);
	}else{
		if((array_key_exists('css', $settings['global'])) && (!empty($settings['global']['css']))){
			$sitewide_css_files = explode(',', $settings['global']['css']);
			$page_css_files = array();
		}
	}
	/**
	 * Tell Struts to create the ##strutCSS## based on the $css_files and whether compression setting is set or not 
	 */
	if($settings['global']['compress_css'] === true){
		$newStrut->setLayoutCSSWithCompression($sitewide_css_files, $page_css_files, $settings['global']['css_compress_directory'], $page_url);
	}else{
		$css_files = array_merge($sitewide_css_files, $page_css_files);
		$newStrut->setLayoutCSSFromArray($css_files, $css_directory);
	}
	
	/**
	 * Check $page_specific_settings['template'], if it is set then $layout_template = $page_specific_settings['template']
	 * else it remains the default layout set above
	 */
	if((array_key_exists('template', $page_specific_settings)) && (!empty($page_specific_settings['template']))){
		$layout_template = $page_specific_settings['template'];
		
		/**
		 * IMPORTANT:: unset the $page_specific_settings['template'] so it will not become a variable on the layout
		 */
		unset($page_specific_settings['template']);
	}else{
		$layout_template = $settings['global']['template'];
	}

	/**
	 * If the page requesed has a design layout, then include it.
	 */
	if(file_exists($page_path . '.html')){
		$newStrut->setPageElement($page_path . '.html');
	}
	
	/**
	 * Tell the Struts Templating Engine the layout file to use.
	 */
	if(file_exists($layout_directory . '/' . $layout_template)){
	  $newStrut->setLayoutElement($layout_directory . '/' . $layout_template);
	}
	
	/**
	 * Tell Struts Templating Engine to render the layout.
	 */
	print $newStrut->renderLayout();
?>