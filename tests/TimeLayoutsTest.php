<?php

require_once dirname(__FILE__) . '/../noaa/Forecaster.php';

class TimeLayoutsTest extends PHPUnit_Framework_TestCase {

    public function testConditions() {

        try {
            // test a Forecast using NOAA's standard 7-day, 24-hour XML data
            $filename = 'forecast_conditions.xml';
            $forecast = new \noaa\weather\response\Forecast(file_get_contents(dirname(__FILE__) . '/xml/' . $filename));
        } catch (Exception $e) {
            $this->fail('An exception was thrown while attempting to instantiate a Forecast: ' . $e->getMessage());
        }

        $this->assertFalse($forecast->getTimeLayoutByKey('doesNotExist'));

        $tl = $forecast->getTimeLayoutByKey('k-p24h-n7-1');
        $this->assertEquals('24hourly', $tl->getSummary());
        $this->assertEquals('local', $tl->getTimeCoordinate());
        $this->assertEquals('k-p24h-n7-1', $tl->getKey());
        $this->assertEquals(7, count($tl->getValidTimes()));

    }

}