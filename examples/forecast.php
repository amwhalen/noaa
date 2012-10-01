<?php

// Instantiate a Forecaster object using a file cache
// XML data from the NOAA API will be cached for 1 HOUR in the specified file location
require_once dirname(__FILE__) . '/../noaa/Forecaster.php';
$config = new \noaa\weather\Configuration();
$config->setCache(new \noaa\weather\cache\FileCache(dirname(__FILE__) . '/cache'));
$forecaster = new \noaa\Forecaster($config);

// set your own latitude and longitude here to test
$lat = '42.16';
$lng = '-72.72';
$startTime = date('c', time());
$numDays = 7;

// fetch a Forecast object
try {
	$forecast = $forecaster->getForecastByLatLng($lat, $lng, $startTime, 7);
} catch (Exception $e) {
	echo "Error: " . $e->getMessage() . "\n";
	exit(1);
}

// display
$hazards = $forecast->getHazards();
if (count($hazards) > 0) {
	echo "HAZARDS: " . implode(', ', $hazards) . "\n";
}

for ($i = 0; $i < $forecast->getLength(); $i++) {
	echo "---\n";
	$day = $forecast->getDay($i);
	echo date('l, F j', strtotime($day->getStartTime())) . "\n";

	$high = ($day->getHighTemperature() === null) ? 'NULL' : $day->getHighTemperature();
	echo "High: " . $high . "\n";

	$low = ($day->getLowTemperature() === null) ? 'NULL' : $day->getLowTemperature();
	echo "Low: " . $low . "\n";

	$precipDay = ($day->getPrecipitationProbabilityDay() === null) ? 'NULL' : $day->getPrecipitationProbabilityDay();
	echo "Precipitation Chance Day: " . $precipDay . "%\n";

	$precipNight = ($day->getPrecipitationProbabilityNight() === null) ? 'NULL' : $day->getPrecipitationProbabilityNight();
	echo "Precipitation Chance Night: " . $precipNight . "%\n";

	echo "Conditions: " . $day->getConditions() . "\n";
}