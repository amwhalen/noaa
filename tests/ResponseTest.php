<?php

require_once dirname(__FILE__) . '/../noaa/Forecaster.php';

class ResponseTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException Exception
     */
    public function testResponseException() {
        $badXML = '<bad><xml';
        $response = new \noaa\weather\response\Response($badXML);
    }

}