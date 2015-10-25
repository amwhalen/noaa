<?php

require_once dirname(__FILE__) . '/../noaa/Forecaster.php';

class BaseTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException Exception
     */
    public function testBaseException() {
        $badXML = '<bad><xml';
        $b = new \noaa\weather\Base();
        $b->thisDoesNotNorWillItEverExist();
    }

    public function testVersion() {
        $b = new \noaa\weather\Base();
        $this->assertInternalType('string', $b->version());
    }

}