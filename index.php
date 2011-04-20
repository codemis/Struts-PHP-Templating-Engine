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
     * Include the Struts Templating Engine, and the spyc class that reads the yaml settings
     *
     * @author Technoguru Aka. Johnathan Pulos
     */
	include("engine/struts.php");
	include("engine/spyc/spyc.php");
	
	/**
	 * Set the various variables for use later in the script.
	 * @var 	string 	$page_content Holds the current page content for the selected page
	 * @var 	string 	$newStrut templating object
	 * @var 	array 	$settings an array of settings converted from YAML to a PHP Array thanks to Spyc
	 * @var 	array 	$page_specific_settings an array of page specific settings converted from YAML to a PHP Array thanks to Spyc
	 */
	$page_content = $newStrut = $settings = $page_specific_settings = '';
    define("APP_PATH", dirname(__FILE__) ."/");
    /**
     * Setup the strutsEngine object
     *
     * @author Technoguru Aka. Johnathan Pulos
     */
	$newStrut = strutsEngine::init();
	$newStrut->setSetting('default_layout', 'example_test.html');
	$newStrut->setSetting('settings_file', 'settings/site.yml');
	$newStrut->setSetting('database_file', 'settings/database.inc.php');
	$newStrut->setSetting('cache_time', 600);
	$newStrut->setSetting('cache_ext', 'cache');
	/**
	 * Set the level of debugging.
	 *
	 * 3 - Display full stack trace when an error occurs, but do not write the trace to the log files (development) 
	 * 2 - Display full stack trace when an error occurs, and write the trace to the log files (development)
	 * 1 - Write stack trace into log file, but display error page
	 * 0 - do nothing
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$newStrut->setSetting('debug_level', 3);
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
	$newStrut->handleRequest($_GET['url']);
	$newStrut->readSetting('cache_time');
	trigger_error('Cloning the STRUT is not permitted.', E_USER_ERROR);
	$newStrut->jsFormat = "<script src=\"%s\"></script>\r\n";
	$newStrut->cssFormat ="<link rel=\"stylesheet\" href=\"%s\">\r\n";
	$newStrut->siteUrl = ($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'http://www.example.com';//Do not add final slash, NOTE:: change to correct url
	
	/**
	 * Set all default variables and directories
	 * @var string 	$layout_template 		the main layout file for all pages found in the pages directory
	 * @var int		$cache_time 			length of time to cache files for
	 * @var string	$cache_directory		directory for the cahce files
	 * @var	string	$pages_directory		directory for all the page code
	 * @var	string	$pages_code_directory	directory for all the page php functionality code
	 * @var	string	$module_directory		directory for all the modules code (nav, side modules, etc.)
	 * @var	string	$layout_directory		directory for all global layout code
	 * @var	string	$css_directory			directory for all the css files
	 * @var	string	$js_directory			directory for all the javascript files
	 * @var	string	$settings_file			location of the settings YAML
	 * @var	string	$database_file			location of the database settings file
	 */
	$layout_template = 'example.html';// NOTE:: Set to your default layout
	$cache_time = 600;
	$cache_ext = 'cache';
	$cache_directory = 'tmp/';
	$pages_directory = 'design/pages';
	$pages_code_directory = 'code/pages';
	$module_directory = 'design/modules';
	$module_code_directory = 'code/modules';
	$layout_directory = 'design/layouts';
	$css_directory = '/design/css';
	$newStrut->CSSDir = $css_directory;
	$js_directory = '/design/js';
	$newStrut->JSDir = $js_directory;
	$elements_directory = 'design/elements';
	$settings_file = 'settings/site.yml';
	$database_file = 'settings/database.inc.php';	
	
	/**
	 * Spyc load the settings YAML into a PHP array
	 */
	$settings = Spyc::YAMLLoad($settings_file);
	/**
	 * @var	string	$page_url	the url for the page requested.
	 */
	$page_url = (isset($_GET['url'])) ? trim($_GET['url']) : 'index.html';		

	/**
	 * If they append a index.html,  see if it is valid, if not remove it
	 * @todo goes into the configuration file
	 * @author Johnathan Pulos
	 */
	if (strpos($page_url, 'index.html') != false) {
	    if(!array_key_exists($page_url, $settings)) {
			$page_url = substr($page_url, 0, strrpos($page_url, '/index.html')); 
		}
	}
	
	/**
	 * If an extension exists on $page_url then remove it
	 */
	if (strpos($page_url, '.') != false) {
	    $page_url = substr($page_url, 0, strrpos($page_url, '.')); 
	}
	
	/**
	 * If they are requesting to delete all the cache
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	if($page_url == 'clear_cache') {
		require_once('code/functions/recursive_directory_scan.php');
		/**
		 * remove CSS temp files
		 *
		 * @author Technoguru Aka. Johnathan Pulos
		 */
		$directoryResults = scan_directory_recursively($settings['global']['css_compress_directory']);
		foreach($directoryResults as $cssFile) {
			if($cssFile['extension'] == 'css'){
				unlink($cssFile['path']);
			}
		}
		/**
		 * remove Javascript temp files
		 *
		 * @author Technoguru Aka. Johnathan Pulos
		 */
		$directoryResults = scan_directory_recursively($settings['global']['js_compress_directory']);
		foreach($directoryResults as $jsFile) {
			if($jsFile['extension'] == 'js'){
				unlink($jsFile['path']);
			}
		}
		/**
		 * Remove all temp files
		 *
		 * @author Technoguru Aka. Johnathan Pulos
		 */
		$directoryResults = scan_directory_recursively('tmp');
		foreach($directoryResults as $tempFile) {
			if($tempFile['extension'] == 'cache'){
				unlink($tempFile['path']);
			}
		}
		echo 'All cache has been cleared';
		exit;
	}
	
	/* 404 page */
	$page_url = (isset($_GET['url']) && !array_key_exists($page_url, $settings)) ? '404' : $page_url;

	/**
	 * Set path for the page design and code files.
	 * 
	 * All file will exist based on the url.  For example,  if the page requested is /meetings/rooms.html then
	 * -  the page code can be found in $pages_code_directory/meetings/rooms.php
	 * -  the page design/layout file can be found in $pages_directory/meetings/rooms.html
	 * Another example.  If the page requested is /meetings.html
	 * -  the page code can be found in $pages_code_directory/meetings.php
	 * -  the page design/layout file can be found in $pages_directory/meetings.html
	 * 
	 * @var	string $page_path 			the path to the design/layout file for the requested page
	 * @var	string	$pages_code_path	the path to the php functionality file for the requested page		
	 */
	if(!empty($page_url)){
			$page_path = $pages_directory . '/' . $page_url;
			$pages_code_path = $pages_code_directory . '/' . $page_url;
	}else{
			$page_path = $pages_directory;
			$pages_code_path = $pages_code_directory;
	}
	
	/**
	 * Set $page_specific_settings to the specific page settings.  If they do not exist then set them to the global settings.
	 * @todo 	must verify if the global setting is really the bes approach
	 */
	$page_specific_settings = ((!empty($page_url)) && array_key_exists($page_url, $settings)) ? $settings[$page_url] : $settings['global'];	
	if(empty($page_url) || $page_specific_settings['landing_page'] === true){
		$page_path = $page_path . '/index';		
		$pages_code_path = $pages_code_path . '/index';
	}
	
	/**
	 * If $page_specific_settings['cache'] is true and $settings['global']['enable_caching'] is true, then start page caching.
	 */
	if(($page_specific_settings['cache'] === true) && ($settings['global']['enable_caching'] === true)){
		
		/**
		 * @var	string	$cachefile the cache file for the requested page (md5 encrypted)
		 */
		$cachefile = $cache_directory . md5($page_path . '/index.html') . '.' . $cache_ext;
		
		/**
		 * @var	$cachefile_created	The date the file was cached
		 */ 
		$cachefile_created = (@file_exists($cachefile)) ? @filemtime($cachefile) : 0; 
		@clearstatcache(); 
		
		/**
		 * If the $cachefile_created is less then the set $cache_time then load the file directly and exit() the code
		 */
		if (time() - $cache_time < $cachefile_created) {  
			@readfile($cachefile);  
			exit(); 
		} 
		
		/**
		 * start the page caching engine
		 */
		ob_start();
	}
	
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
	 * Set the seo vars based on what is provided
	 *
	 * @author Technoguru Aka. Johnathan Pulos
	 */
	$page_specific_settings['title'] = ((isset($page_specific_settings['title'])) && (!empty($page_specific_settings['title']))) ? $page_specific_settings['title'] : $settings['global']['title'];
	$page_specific_settings['keywords'] = ((isset($page_specific_settings['keywords'])) && (!empty($page_specific_settings['keywords']))) ? $page_specific_settings['keywords'] : $settings['global']['keywords'];
	$page_specific_settings['description'] = ((isset($page_specific_settings['description'])) && (!empty($page_specific_settings['description']))) ? $page_specific_settings['description'] : $settings['global']['description'];
	unset($settings['global']['title']);
	unset($settings['global']['keywords']);
	unset($settings['global']['description']);
	/**
	 * Send all the rest of the $page_specific_settings to Struts Engine to become layout/page variables.
	 * Now all variables set in the settings YAML is acessible in the layout using ##variable##
	 */
	$newStrut->setLayoutVarFromArray($page_specific_settings, '');
	$newStrut->setPageVarFromArray($page_specific_settings, '');

	/**
	 * If the $database_file exists, then include it
	 * IMPORTANT:: this must remain before including the PHP functionality file for the specific page.
	 */
	if(file_exists($database_file)){
		include($database_file);
	}
	
	/**
	 * If the page requested has a PHP functionality file, then include and run it.
	 */
	if((!empty($page_url)) && (file_exists($pages_code_path . '.php'))){
		include($pages_code_path . '.php');
	}

	/**
	 * If the page requesed has a design layout, then include it.
	 */
	if(file_exists($page_path . '.html')){
		$newStrut->setPageElement($page_path . '.html');
	}
		
	/**
	 * Declare any modules to the Struts Templating Engine you need access to on the layout design page
	 * Example:: this one creates a ##site_nav## that holds the site navigation in the variable
	 */
  if(file_exists($module_code_directory . '/modules.inc.php')){
	  require_once($module_code_directory . '/modules.inc.php');
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
	/**
	 * If $page_specific_settings['cache'] is true, and $settings['global']['enable_caching'] is true
	 * then finish page caching, and dump the cache into the file
	 */
	if(($page_specific_settings['cache'] === true) && ($settings['global']['enable_caching'] === true)){
		
		/**
		 * @var	file $fp holds the file that will be cached			
		 */
		$fp = @fopen($cachefile, 'w');
		@fwrite($fp, ob_get_contents()); 
		@fclose($fp); 
		ob_end_flush(); 
	}
?>