<?php
/**
 * Testing for struts_engine.class.php
 *
 * @package STRUTS
 * @author Technoguru Aka. Johnathan Pulos
 */
class strutsEngineTest extends PHPUnit_Framework_TestCase
{
    public function testArrayMethod() {
        $stack = array();
        $this->assertEquals(0, count($stack));
    }
}
?>