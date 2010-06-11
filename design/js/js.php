<?php
/**
 * This file will compress all javascript files supplied to a single file that has all returns removed.
 * All files are passed in the url using $_GET['files'] which is comma seperated.  .htaccess makes it a pretty url.
 * This file will also cache the file if $_GET['cache'] is set to true.  This file does not touch the root index.php file,
 * so all settings variables are passed through the url.
 * 
 * @author 		Technoguru Aka. Johnathan Pulos
 * @version 	1
 * @copyright 	11 June, 2010
 * @package 	default
 * @uses		class.JavaScriptPacker.php (../../code/classes/class.JavaScriptPacker.php)
 */
header('Content-type: text/js');
require_once '../../code/classes/class.JavaScriptPacker.php';
/**
 * Compresses all files by removing all comments, and removing tabs, new lines, etc
 *
 * @var	string	$buffer	the file data to be compressed
 * 
 * @return string	compressed file data
 * @author Technoguru Aka. Johnathan Pulos
 **/
function compress($buffer) {
	$packer = new JavaScriptPacker($buffer, 62, true, false);
	$buffer = $packer->pack();
	return $buffer;
}

/**
 * Set used variables to default values
 * 
 * @var	string	$cache_js			Should we cache the javascript after it is compressed.  Set in $_GET['cache'].
 * @var	int		$cache_time			Length to hold cache files
 * @var	string	$cache_ext			The extension for all cache files
 * @var	string	$cache_directory	Location to store all cache files
 * @var	array 	$files_to_compress	An array of files to compress into one
 */
$cache_js = $_GET['cache'];
$cache_time = 600;
$cache_ext = 'cache';
$cache_directory = '../../tmp/';
$files_to_compress = explode(',', $_GET['files']);

/**
 * If the $cache_js is true,  then start caching.
 */
if($cache_js){
	
	/**
	 * @var	string	$cachefile the cache file for the requested page (md5 encrypted)
	 */
	$cachefile =  $cache_directory . md5('js.php') . '.' . $cache_ext; 
	
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
}
/**
 * start the page compression/caching engine
 */
 ob_start("compress");

/**
 * Iterate over $files_to_compress and require_once each of them if they exist. (protects from duplicates)
 */
foreach($files_to_compress as $js){
	if(file_exists('./'.$js)){
		require_once('./'.$js);
	}
}

/**
 * If $cache_js is true then finish page caching, and dump the cache into the file
 */
if($cache_js){
	
	/**
	 * @var	file $fp holds the file that will be cached			
	 */
	$fp = @fopen($cachefile, 'w');
	@fwrite($fp, compress(ob_get_contents())); 
	@fclose($fp); 
}
ob_end_flush();
?>