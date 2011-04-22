<?php
/**
 * Declare all global modules here
 *
 * @author Technoguru Aka. Johnathan Pulos
 */
/**
 * Declare any modules to the Struts Templating Engine you need access to on the layout design page
 * Example:: this one creates a ##site_nav## that holds the site navigation in the variable
 */
$newStrut->setTemplateTag("example_nav", file_get_contents($module_directory . '/example_nav.html'));
?>