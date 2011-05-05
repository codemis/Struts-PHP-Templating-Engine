<?php
/**
 * Testing for struts_engine.class.php
 *
 * @package STRUTS
 * @author Technoguru Aka. Johnathan Pulos
 */
/**
* Define the DOMAIN for the current site
*/
define("DOMAIN", "http://struts.local/");
/**
* Define the directory seperator type for the current file system
*/
define("DS", '/');
/**
* Defines the root directory for this application, force it to the directory root
*/
define("APP_PATH", "../../");
require_once('../caching.class.php');
class CachingTest extends PHPUnit_Framework_TestCase
{
    /**
	 * The singleton instance of the class
	 *
	 * @var Object
	 */
	private static $cachingInstance;
	
	/**
	 * Setup the testing case
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
    public function setUp() {
        self::$cachingInstance = Caching::init();
    }
    
    /**
     * Complete the testing case
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function tearDown() {
        self::$cachingInstance = '';
    }
}
?>