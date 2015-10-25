<?php

require_once dirname(__FILE__) . '/../noaa/Forecaster.php';

class ForecastTest extends PHPUnit_Framework_TestCase {

	public function testForecast() {

		try {
			// test a Forecast using NOAA's standard 7-day, 24-hour XML data
			$filename = 'forecast_7day.xml';
			$forecast = new \noaa\weather\response\Forecast(file_get_contents(dirname(__FILE__) . '/xml/' . $filename));
		} catch (Exception $e) {
			$this->fail('An exception was thrown while attempting to instantiate a Forecast: ' . $e->getMessage());
		}

		// xml creation date
		$date = $forecast->getCreationDate();
		$this->assertEquals($date, '2008-12-05T19:38:31Z');

		// high temps
		$expectedHighs = array(38, 29, 33, 20, 37, 18, 14);
		$this->assertEquals(
			$expectedHighs,
			$forecast->getHighTemperatures()
		);

		// low temps
		$expectedLows = array(17, 19, 20, 14, 14, 2, null);
		$this->assertEquals(
			$expectedLows,
			$forecast->getLowTemperatures()
		);

		// hazards
		$expectedHazards = array("Long Duration Lake Wind Advisory");
		$this->assertEquals(
			$expectedHazards,
			$forecast->getHazards()
		);

		// precipitation probabilities
		$expectedPrecipitation = array(27, 19, 56, 33, 30, 27, 20, 7, 10, 16, 26, 12, 11, null);
		$this->assertEquals(
			$expectedPrecipitation,
			$forecast->getPrecipitationProbabilities()
		);

		// icons
		$expectedIcons = array(
			'http://www.nws.noaa.gov/weather/images/fcicons/sn30.jpg',
			'http://www.nws.noaa.gov/weather/images/fcicons/sn60.jpg',
			'http://www.nws.noaa.gov/weather/images/fcicons/sn30.jpg',
			'http://www.nws.noaa.gov/weather/images/fcicons/sn20.jpg',
			'http://www.nws.noaa.gov/weather/images/fcicons/bkn.jpg',
			'http://www.nws.noaa.gov/weather/images/fcicons/sn30.jpg',
			'http://www.nws.noaa.gov/weather/images/fcicons/bkn.jpg'
		);
		$this->assertEquals(
			$expectedIcons,
			$forecast->getIcons()
		);

		// start times
		$expectedStartTimes = array(
			'2008-12-05T06:00:00-07:00',
			'2008-12-06T06:00:00-07:00',
			'2008-12-07T06:00:00-07:00',
			'2008-12-08T06:00:00-07:00',
			'2008-12-09T06:00:00-07:00',
			'2008-12-10T06:00:00-07:00',
			'2008-12-11T06:00:00-07:00'
		);
		$this->assertEquals(
			$expectedStartTimes,
			$forecast->getStartTimes()
		);

		// end times
		$expectedEndTimes = array(
			'2008-12-06T06:00:00-07:00',
			'2008-12-07T06:00:00-07:00',
			'2008-12-08T06:00:00-07:00',
			'2008-12-09T06:00:00-07:00',
			'2008-12-10T06:00:00-07:00',
			'2008-12-11T06:00:00-07:00',
			'2008-12-12T06:00:00-07:00'
		);
		$this->assertEquals(
			$expectedEndTimes,
			$forecast->getEndTimes()
		);

		// expected weather condition summaries
		$expectedConditionSummaries = array(
			'Snow Likely',
			'Snow Likely',
			'Chance Snow',
			'Slight Chance Snow',
			'Mostly Cloudy',
			'Chance Snow',
			'Mostly Cloudy'
		);

		// expected weather condition strings
		$expectedConditionStrings = array(
			'Snow Likely (likely light snow)',
			'Snow Likely (likely light snow)',
			'Chance Snow (chance light snow)',
			'Slight Chance Snow (slight chance light snow)',
			'Mostly Cloudy',
			'Chance Snow (chance light snow)',
			'Mostly Cloudy'
		);

		// check all conditions separately for easier debugging
		$conditions = $forecast->getConditions();
		$i = 0;
		foreach ($conditions as $condition) {
			$this->assertEquals($expectedConditionSummaries[$i], $condition->getSummary());
			$this->assertEquals($expectedConditionStrings[$i], (string) $condition);
			$i++;
		}

		// length
		$this->assertEquals(7, $forecast->getLength());

		// index within bounds
		$this->assertEquals(null, $forecast->getDay(-1));
		$this->assertEquals(null, $forecast->getDay($forecast->getLength()+1));

		// ForecastDay tests
		for ($i = 0; $i < 7; $i++) {
			$day = $forecast->getDay($i);
			$this->assertNotNull($day);
			$this->assertEquals($expectedHighs[$i], $day->getHighTemperature());
			$this->assertEquals($expectedLows[$i], $day->getLowTemperature());
			$this->assertEquals($expectedIcons[$i], $day->getIcon());
			$this->assertEquals($expectedStartTimes[$i], $day->getStartTime());
			$this->assertEquals($expectedEndTimes[$i], $day->getEndTime());
			$this->assertEquals($expectedConditionSummaries[$i], $day->getConditions()->getSummary());
			$this->assertEquals($expectedConditionStrings[$i], (string) $day->getConditions());
			$this->assertEquals($expectedPrecipitation[$i*2], $day->getPrecipitationProbabilityDay());
			$this->assertEquals($expectedPrecipitation[$i*2+1], $day->getPrecipitationProbabilityNight());
		}

	}

	public function testNightForecasts() {

		try {
			// test a Forecast using NOAA's standard 7-day, 24-hour XML data
			$filename = 'forecast_precip.xml';
			$forecast = new \noaa\weather\response\Forecast(file_get_contents(dirname(__FILE__) . '/xml/' . $filename));
		} catch (Exception $e) {
			$this->fail('An exception was thrown while attempting to instantiate a Forecast: ' . $e->getMessage());
		}

		$day = $forecast->getDay(0);

		// this forecast starts at night
		$this->assertTrue($forecast->doesStartAtNight());
		$this->assertEquals(4, $forecast->getPrecipitationProbabilityTonight());

		// zero day should be 1/31 (not 2/1)
		$this->assertEquals(13, $day->getPrecipitationProbabilityDay());
		$this->assertEquals(28, $day->getPrecipitationProbabilityNight());

		// last day should have day/night precip of null
		$lastDay = $forecast->getDay(6);
		$this->assertEquals(null, $lastDay->getPrecipitationProbabilityDay());
		$this->assertEquals(null, $lastDay->getPrecipitationProbabilityNight());

	}

}