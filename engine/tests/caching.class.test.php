<?php
/**
 * Testing for struts_engine.class.php
 *
 * @package STRUTS
 * @author Technoguru Aka. Johnathan Pulos
 */
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