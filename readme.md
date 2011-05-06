STRUTS PHP Template Engine
==========================

What is STRUTS PHP?
===================
  
  STRUTS PHP is a php templating tool designed to create static websites quickly with little to no designer interaction with code.  Designers will only need to interact with the settings and design directories, which have no PHP code in them at all. The goal was to find a medium between using a full blown PHP Framework, and just creating a bunch of static HTML pages.  I was tired of doing large **Find and Replace** fixes, so I created this tool out of desperation.  This tool is still in the beginning stages, but it has been implemented on several sites and is field tested. Here are some of the features:
  
  * Designer and Developer Code Seperation
  * Built in Caching Engine
  * Built in Javascript and CSS Compression
  * Dynamic sitemap xml builder that builds based on your settings YAML file (/sitemap.xml)
  * Single YAML page settings file
  * Pages can include or exclude file extensions without any configuration (page/page.html or page/page)
  * Template tags in layout files to easily display any custom variables set in YAML.  Add a setting in YAML titled **password**, and you can access in any layout or page template with ##password##
  * Well documented code using [PHPDocumentor](http://www.phpdoc.org/)
  
Directory Structure
===================

  * code - PHP files are stored in this folder for easy access by developer.
    * classes - custom PHP classes
    * functions - custom PHP functions
    * modules - STRUTS specific declarations for layout modules (See code/modules/modules.inc.php for more info)
    * pages - location of specific php files for each page.  **File structure should mimic design/pages directory**
  * design - location of all template files
    * css - location of all public CSS files
      * min - compressed CSS will be stored here **Must have 777 permissions**
    * images - location of all public image files
    * js - location of all public Javascript files
      * min - compressed Javascript will be stored here **Must have 777 permissions**
    * layouts - location of all layout templates that a page can use
    * modules - location of all modules that a page can use
    * pages - location of all page templates
  * engine - location for the STRUTS core PHP files
  * settings - location for the settings YAML and database include files

Getting Started
===============

  1. Clone this git repo 
  
    `git clone git@github.com:codemis/Struts-PHP-Templating-Engine.git`

  2. There are several example pages so once you setup your vhost,  point your browser to http://localhost/example.html or http://localhost/example-directory/example.html.  You can even exclude the file extensions on these paths.  You should now see a styled example page.

Requires
========

  PHP 5.3.2+ and cURL

How do you configure it?
========================

  To configure the STRUTS Engine settings, you can edit lines 49-64 in the main **index.php**.  If you want to configure the caching or compression, just edit the variables in **settings/site.yml**.
  
How it works!
=============

  The code has several example files that show the code in action, and it should be straight forward.  Here is a breakdown of what is going on:
  
  1. The settings are pulled and parsed from the YAML file using the [PHP SPYC Class](http://code.google.com/p/spyc/) located in the engine directory.
  2. Once parsed,  the page and settings is determined by the url.  So if you visited http://localhost/example-directory/example.html, the engine will:
    * Set page settings from **'example-directory/example'** in the site.yml.
    * Include the PHP file in code/pages/example-directory/example.php or code/pages/example-directory/example/index.php if the the page setting **landing_page** is TRUE.
    * Include the page content from the file in design/pages/example-directory/example.html, or design/pages/example-directory/example/index.html if the the page setting **landing_page** is TRUE.
  3. If there are no settings,  it will default to the global settings.
  4. If caching is enabled, and the page has been loaded before, then it will feed the user that page, else it will continue.
  5. It checks if Javascript/CSS compression is on, and if it is, it creates a compressed version of the js/css set in the site.yaml, or it skips this step if it has already been created.
  6. It includes the page specific PHP file if it exists.
  7. It reads the page specific HTML file.  Then it finds all ## variables in the html file, and replaces them with any set php variables, and page settings.  So if you have a setting for the page called **title**, then ##title## will be replaced with the setting value.
  8. If the file exists,  it includes the code/modules/modules.php file where you can define layout wide modules.
  9. The layout file is set based on the **template** setting in site.yml, if it exists either in the global settings, or the page specific settings, or it defaults to the **$layout_template** in the index.php file
  10. Now the function `renderLayout()` is called, which again parses any ## variables and sets them with any variables set in the PHP or the site.yml.
  11. It prints out the result.
  
Template Tags
=============

Page Specific
-------------

  There are two ways to set layout specific tags.  The first is setting it in the settings/site.yml file.  Under the specific page, just add the variable and its value.  For example,  to set a variable named **my_title** on the page at **http://localhost/example-directory/example.html**,  got to the site.yml file and find or create a setting for the page that looks like this:
  
    'example-directory/example':
        title: 'Page Title'
        description: 'Page Description'
        keywords: 'Page keywords'
        template: ''
        javascript: ''
        css: ''
        landing_page: false
        cache: true
        changefreq: 'daily'
        priority: '2'
  
  Now add your new variable:
  
    my_title: 'I Love STRUTS'
  
  Now open the page specific HTML file at design/pages/example-directory/example.html or design/pages/example-directory/example/index.html if it is a landing page, and add your template tag ##my_title##.
  
  Another way to set a page variable, and to do some PHP processing on the variable is to create a page specific PHP file.  Using the same page as the previous example, create a PHP file at code/pages/example-directory/example.php or code/pages/example-directory/example/index.php if it is a landing page.  Open the file, and set the variable by using the following STRUTS method:
  
  `$newStrut->setPageVar('my_title', 'Another Example Page');`

  Now open the page specific HTML file at design/pages/example-directory/example.html or design/pages/example-directory/example/index.html if it is a landing page, and add your template tag ##my_title##. 

Layout Specific
---------------

  All global settings in site.yml, and the page specific settings in site.yml are accessible in the layout with the ##variable## syntax.  To add custom variables for the layout,  you should set them up in the sites.yml file explained above in the Page Specific tagging.  Only modules should be setup in the PHP code.
  
  There are also designated STRUTS' tags that are provided for the layout that can be helpful.  Here is the list of them:

  * `##strutCSS##` - The CSS tags will be added dynamically.  If the CSS is being compressed,  the tag will only be linked to the compressed CSS files for the sitewide CSS and the page specific CSS file, otherwise all the CSS files will be added by the STRUTS Engine.  All CSS should be set in the settings/site.yml file.  You can set both global CSS files which will be included on all pages, or page specific CSS that will only be included on that page. Do not compress files not located in the CSS directory.
  * `##strutJavascript##` - The Javascript tags will be added dynamically.  If the Javascript is being compressed,  the tag will only be linked to the compressed Javascript file for the sitewide Javascript and the page specific Javascript file, otherwise all the Javascript files will be added by the STRUTS Engine.  All Javascript should be set in the settings/site.yml file.  You can set both global Javascript files which will be included on all pages, or page specific Javascript that will only be included on that page.  Do not compress files that are not located in the JS directory.
  * `##strutContent##` - The page specific content pulled from the page specific template file.

  
Adding modules to the layout is a great way to share common HTML elements over several layouts.  To create a module, first save your HTML snippet in the design/modules directory.  Then open the code/modules/modules.inc.php file and add the following code with your specific information:
  
  `$newStrut->setLayoutVar("module_variable_name", file_get_contents($module_directory . '/module_file_name.html'));`
  
  
Now in the layout you can use the ##module_variable_name## to display the current module.

To The Future
=============

  It is my hope to start consolidating much of the functionality in the index.php into PHP classes in the engine directory.  I hope to create a routing class, caching class, etc. to make the code easier to upgrade, and more manageable.  I am definitely open to suggestions, or comments.  Just let me know.  Latez.
  
Codemis AKA Johnathan Pulos