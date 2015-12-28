<?php

require_once dirname(__FILE__) . '/../noaa/Forecaster.php';

class HazardTest extends PHPUnit_Framework_TestCase {

    public function testConditions() {

        try {
            // test a Forecast using NOAA's standard 7-day, 24-hour XML data
            $filename = 'forecast_7day.xml';
            $forecast = new \noaa\weather\response\Forecast(file_get_contents(dirname(__FILE__) . '/xml/' . $filename));
        } catch (Exception $e) {
            $this->fail('An exception was thrown while attempting to instantiate a Forecast: ' . $e->getMessage());
        }

        $hazards = $forecast->getHazards();

        $this->assertEquals(1, count($hazards));

        $h = $hazards[0];
        $this->assertEquals("LW.Y", $h->getCode());
        $this->assertEquals("Lake Wind", $h->getPhenomena());
        $this->assertEquals("Advisory", $h->getSignificance());
        $this->assertEquals("LW", $h->getPhenomenaCode());
        $this->assertEquals("Y", $h->getSignificanceCode());
        $this->assertEquals("long duration", $h->getType());
        $this->assertEquals("k-p6h-n1-3", $h->getTimeLayoutKey());
        $this->assertEquals("Long Duration Lake Wind Advisory", (string) $h);

    }

}