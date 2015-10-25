<?php

require_once dirname(__FILE__) . '/../noaa/Forecaster.php';

class CurrentWeatherTest extends PHPUnit_Framework_TestCase {

	public function testCurrentWeather() {

		try {
			// test using sample data from a NOAA weather observation station
			$filename = 'current_KBAF.xml';
			$current = new \noaa\weather\response\CurrentWeather(file_get_contents(dirname(__FILE__) . '/xml/' . $filename));
		} catch (Exception $e) {
			$this->fail('An exception was thrown while attempting to instantiate a CurrentWeather object: ' . $e->getMessage());
		}

		$this->assertEquals(62.0, $current->getTemperatureF());
		$this->assertEquals(16.7, $current->getTemperatureC());

		$this->assertEquals(50.0, $current->getDewpointF());
		$this->assertEquals(10.0, $current->getDewpointC());

		$this->assertEquals(1007.2, $current->getPressureMb());
		$this->assertEquals(29.74, $current->getPressureInHg());

		$this->assertEquals("Sun, 30 Sep 2012 14:53:00 -0400", $current->getObservationTime());

		$this->assertEquals(65, $current->getRelativeHumidity());

		$this->assertEquals(10.0, $current->getVisibilityMiles());

		$this->assertEquals(0.0, $current->getWindSpeedMPH());
		$this->assertEquals(0.0, $current->getWindSpeedKnots());
		$this->assertEquals(0.0, $current->getWindDegrees());
		$this->assertEquals("North", $current->getWindDirection());
		$this->assertEquals("Calm", $current->getWindString());

		$this->assertEquals("Overcast", $current->getWeather());

		$this->assertEquals("http://w1.weather.gov/images/fcicons/ovc.jpg", $current->getIcon());
		$this->assertEquals("ovc.jpg", $current->getIconName());

		$this->assertEquals("KBAF", $current->getStationId());

		$this->assertEquals("Westfield, Barnes Municipal Airport, MA", $current->getLocation());

		$this->assertEquals(42.16, $current->getLatitude());

		$this->assertEquals(-72.72, $current->getLongitude());

		$this->assertEquals("Sun, 30 Sep 2012 14:53:00 -0400", $current->getTime());

	}

}