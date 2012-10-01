<?php

// retrieve the station ID from the command line if supplied
if ($argc > 1) {
	$stationId = $argv[1];
} else {
	echo "Usage: php " . $argv[0] . " STATION_ID\n";
	echo "Example: php " . $argv[0] . " KBAF\n";
	echo "Find your closest station ID here: http://www.weather.gov/xml/current_obs/\n";
	exit(1);
}

// Instantiate a Forecaster object using a file cache
// XML data from the NOAA API will be cached for 1 HOUR in the specified file location
require_once dirname(__FILE__) . '/../noaa/Forecaster.php';
$config = new \noaa\weather\Configuration();
$config->setCache(new \noaa\weather\cache\FileCache(dirname(__FILE__) . '/cache'));
$forecaster = new \noaa\Forecaster($config);

// fetch a CurrentWeather instance for a specific station ID
// find station IDs here: http://www.weather.gov/xml/current_obs/
$current = $forecaster->getCurrentWeather($stationId);

// display
echo $current->getLocation() . "\n";
echo $current->getObservationTime() . "\n";
echo "Temperature: " . $current->getTemperatureF() . " °F\n";
echo "Dew Point: " . $current->getDewPointF() . " °F\n";
echo "Relative Humidity: " . $current->getRelativeHumidity() . " %\n";
echo "Wind: " . $current->getWindString() . "\n";
echo "Weather: " . $current->getWeather() . "\n";