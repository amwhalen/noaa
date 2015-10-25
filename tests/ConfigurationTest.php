<?php

require_once dirname(__FILE__) . '/../noaa/weather/Configuration.php';

class ConfigurationTest extends PHPUnit_Framework_TestCase {

    public function testConfiguration() {

        $c = new \noaa\weather\Configuration();

        $this->assertEquals('F', $c->getTemperatureScale());
        $c->setTemperatureScale('C');
        $this->assertEquals('C', $c->getTemperatureScale());

        $this->assertEquals('miles', $c->getDistanceUnit());
        $c->setDistanceUnit('kilometers');
        $this->assertEquals('kilometers', $c->getDistanceUnit());

        $this->assertEquals(new \noaa\weather\cache\NoCache(), $c->getCache());
        $c->setCache(new \noaa\weather\cache\ArrayCache());
        $this->assertEquals(new \noaa\weather\cache\ArrayCache(), $c->getCache());

    }

}