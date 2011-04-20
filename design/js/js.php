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
 * @uses		class.JSMin.php (../../code/classes/class.JSMin.php)
 */
header('Content-type: text/js');
require_once '../../engine/vendors/JSMin/class.JSMin.php';
/**
 * Compresses all files by removing all comments, and removing tabs, new lines, etc
 *
 * @var	string	$buffer	the file data to be compressed
 * 
 * @return string	compressed file data
 * @author Technoguru Aka. Johnathan Pulos
 **/
function compress($buffer) {
	$buffer = JSMin::minify($buffer);
  return $buffer;
}

/**
 * Set used variables to default values
 * 
 * @var	array $files_to_compress	An array of files to compress into one
 */
$files_to_compress = explode(',', $_GET['files']);

/**
 * start the page compression/caching engine
 */
ob_start();

/**
 * Iterate over $files_to_compress and require_once each of them if they exist. (protects from duplicates)
 */
foreach($files_to_compress as $js){
	if(file_exists('./'.$js)){
		require_once('./'.$js);
	}
}
$directory = $_GET['directory'];
$directoryArray = explode('/js/', $directory);
$fp = @fopen($directoryArray[1].'/'.$_GET['filename'], 'w');
@fwrite($fp, compress(ob_get_contents())); 
@fclose($fp);
ob_end_flush();
?>