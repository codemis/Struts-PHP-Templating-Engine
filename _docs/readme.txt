/* This class provides a templating engine to easily share 1 template over multiple PHP files
*  In your page files put a ##variable name##,  and set the variable in the php file
*  This class will recursively replace all ##var## in a HTML file with the variable provided in PHP.
*  Author::    Technoguru Aka. Johnathan Pulos  (mailto:johnathan@jpulos.com)
*  Copyright:: Copyright (c) 2009 Johnathan Pulos
*  License::   N/A
*/

############Description############
Struts is a basic templating tool designed to remove PHP from the HTML,  so that designers
do not have to know PHP in order to use it.  It also provides a way to put PHP code in a single place, rather 
then scatter accross multiple files.  In the HTML files,  you just need to wrap a php variable name in ##,  and Struts
will replace it with the value of the variable.  So if you want to put a $UserName in the HTML file,  then just use ##UserName## .
Using struts will replace the ##UserName## with the value of the PHP variable $UserName that was set in the PHP Backbone file.

############File Structure############
Root (PHP Backbone files)
-Code (Holdes all PHP files)
-Engine (the core strut tool)
-Design (Holds all design elements including images, layouts, and html pages)
-Settings (Holds the settings YAML, and database include)
-Index.php (Controller to the site.)

Enjoy!!!
