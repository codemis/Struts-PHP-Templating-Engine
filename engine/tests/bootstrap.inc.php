<?php
    require_once 'Mockery/Loader.php';
    require_once 'Hamcrest/hamcrest.php';
    $loader = new \Mockery\Loader;
    $loader->register();
    /**
    * Define the DOMAIN for the current site
    */
    if(!defined("DOMAIN")) {
        define("DOMAIN", "http://struts.local/");   
    }
    /**
    * Define the directory seperator type for the current file system
    */
    if(!defined("DS")) {
        define("DS", '/');
    }
    /**
    * Defines the root directory for this application, force it to the directory root
    */
    if(!defined("APP_PATH")) {
        define("APP_PATH", "../../");
    }
?>