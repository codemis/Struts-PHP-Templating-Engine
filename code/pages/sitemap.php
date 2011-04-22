<?php
/**
 * Dynamically creates a sitemap for this site.  It grabs the settings file and iterates over each page
 * then spits out the required xml structure.  Requires the following variables to be set in the index.php
 * $settings
 * $newStrut
 * 
 * @author 		Technoguru Aka. Johnathan Pulos
 * @version 	1
 * @copyright 	11 June, 2010
 * @package 	default
 */
header ("content-type: text/xml");
/**
 * Set used variables to an empty string
 * @var	string	$sitemap_content	The content for this sitemap page in XML format
 * @var string	$changefreq			The change frequency of the page from the settings YAML
 * @var	string	$priority			The page priority of the page from the settings YAML
 * @var	string	$file				The page file location
 * @var	string	$last_update		The last time the file was updated.  PHP checks the file settings, if they are available, else it sets it to today.	
 */
$sitemap_content = $changefreq = $priority = $file = $last_update = '';
/**
 * iterate over each page in the settings YAML array
 */
foreach($settings as $key => $site_page){
	/**
	 * Do not include the global, 404, or sitemap in the sitmap items
	 */
	if(($key != 'global') && ($key != 'sitemap') && ($key != '404')){
		$changefreq = (isset($site_page['changefreq'])) ? $site_page['changefreq'] : $settings['global']['changefreq'];
		$priority = (isset($site_page['priority'])) ? $site_page['priority'] : $settings['global']['priority'];
		$file = $pages_directory . '/' . $key . '/' . $site_page['filename'] . '.html';
		$last_update = (file_exists($file)) ? date ('Y-m-d', filemtime($file)) : date('Y-m-d');
		$sitemap_content .= sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.2f</priority></url>', 
										$settings['global']['domain'] .'/'.$key . '.html', 
										$last_update, 
										$changefreq, 
										$priority);
	}
}
/**
 * Set the ##strutContent## for the layout file using the Struts templating engine.
 */
$newStrut->setTemplateTag("strutContent", $sitemap_content);
?>