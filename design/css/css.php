<?php
/**
 * This file will compress all CSS files supplied to a single file that has all returns removed.
 * 
 * @author 		Technoguru Aka. Johnathan Pulos
 * @version 	1
 * @copyright 	11 June, 2010
 * @package 	default
 */
header('Content-type: text/css');

/**
 * Compresses all files by removing all comments, and removing tabs, new lines, etc
 *
 * @var	string	$buffer	the file data to be compressed
 * 
 * @return string	compressed file data
 * @author Technoguru Aka. Johnathan Pulos
 **/
function compress($buffer) {
	$buffer = preg_replace( '#\s+#', ' ', $buffer );
	$buffer = preg_replace( '#/\*.*?\*/#s', '', $buffer );
	$buffer = str_replace( '; ', ';', $buffer );
	$buffer = str_replace( ': ', ':', $buffer );
	$buffer = str_replace( ' {', '{', $buffer );
	$buffer = str_replace( '{ ', '{', $buffer );
	$buffer = str_replace( ', ', ',', $buffer );
	$buffer = str_replace( '} ', '}', $buffer );
	$buffer = str_replace( ';}', '}', $buffer );
  return trim($buffer);
}

/**
 * Set used variables to default values
 * 
 * @var	array 	$files_to_compress	An array of files to compress into one
 */
$files_to_compress = explode(',', $_GET['files']);
/**
 * start the page compression/caching engine
 */
ob_start();

/**
 * Iterate over $files_to_compress and require_once each of them if they exist. (protects from duplicates)
 */
foreach($files_to_compress as $css){
	if(file_exists('./'.$css)){
		require_once('./'.$css);
	}
}
$fp = @fopen($_GET['final_file'], 'w');
@fwrite($fp, compress(ob_get_contents())); 
@fclose($fp);
ob_end_flush();
?>