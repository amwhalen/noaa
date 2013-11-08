<?php

require_once dirname(__FILE__) . '/../noaa/Forecaster.php';

class ConditionsTest extends PHPUnit_Framework_TestCase {

    public function testConditions() {

        try {
            // test a Forecast using NOAA's standard 7-day, 24-hour XML data
            $filename = 'forecast_conditions.xml';
            $forecast = new \noaa\weather\response\Forecast(file_get_contents(dirname(__FILE__) . '/xml/' . $filename));
        } catch (Exception $e) {
            $this->fail('An exception was thrown while attempting to instantiate a Forecast: ' . $e->getMessage());
        }

        // expected weather condition summaries
        $expectedConditionSummaries = array(
            'Chance Rain Showers',
            'Partly Sunny',
            'Chance Rain Showers',
            'Chance Rain Showers',
            'Slight Chance Rain Showers',
            'Slight Chance Rain Showers',
            'Slight Chance Rain Showers'
        );

        // expected weather condition strings
        $expectedConditionStrings = array(
            'Chance Rain Showers (chance light rain showers and patchy fog)',
            'Partly Sunny',
            'Chance Rain Showers (chance light rain showers)',
            'Chance Rain Showers (chance light rain showers)',
            'Slight Chance Rain Showers (slight chance light rain showers)',
            'Slight Chance Rain Showers (slight chance light rain showers)',
            'Slight Chance Rain Showers (slight chance light rain showers)'
        );

        // check all conditions separately for easier debugging
        $conditions = $forecast->getConditions();
        $i = 0;
        foreach ($conditions as $condition) {
            $this->assertEquals($expectedConditionSummaries[$i], $condition->getSummary());
            $this->assertEquals($expectedConditionStrings[$i], (string) $condition);
            $i++;
        }

    }

}