Overview
========

This library provides a PHP client for NOAA's forecast and current weather services.

Current weather data is provided by [NOAA's XML Feeds of Current Weather Conditions](http://w1.weather.gov/xml/current_obs/).
Forecast data is provided by the [NOAA's National Digital Forecast Database (NDFD) REST Web Service](http://graphical.weather.gov/xml/rest.php).
The National Oceanic and Atmospheric Administration (NOAA) is a United States federal agency, so the data they provide is only available for US locations.

Requirements
============

* PHP 5.3.0+
* PHP cURL
* PHP SimpleXML

Current Weather Conditions
==========================

To find your current weather conditions you'll first need to know a local NOAA Weather Observation Station ID.
You can search for your local station ID here: http://www.weather.gov/xml/current_obs/.
There is also a list of stations in XML if you'd like to do something with the station data: http://www.weather.gov/xml/current_obs/index.xml.
Here is some sample code for getting the current weather conditions:

    require_once 'noaa/Forecaster.php';
    $config = new \noaa\weather\Configuration();
    $myWritableCacheDirectory = dirname(__FILE__) . '/cache';
    $config->setCache(new \noaa\weather\cache\FileCache($myWritableCacheDirectory));
    $forecaster = new \noaa\Forecaster($config);
    $stationId = 'KBAF';
    try {
        // returns a CurrentWeather object or throws an exception on API error
        $current = $forecaster->getCurrentWeather($stationId);
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "Temperature: " . $current->getTemperatureF() . " °F\n";

You can find more sample code in the example/current.php file.

Forecast
========

A latitude and longitude are required to retrieve forecast data.
There are many APIs that will convert addresses and zip codes into latitude and longitude, but those conversions are outside the scope of this library.
The data provided by the API is 24-hour summarized data from 6am-6pm (local time for the supplied location).
The precipitation probabilities provided are 12-hour summaries for day and night, running from 6am-6pm and 6pm-6am respectively.
Here is some sample code for getting the 7-day forecast starting from the current time:

    require_once 'noaa/Forecaster.php';
    $config = new \noaa\weather\Configuration();
    $myWritableCacheDirectory = dirname(__FILE__) . '/cache';
    $config->setCache(new \noaa\weather\cache\FileCache($myWritableCacheDirectory));
    $forecaster = new \noaa\Forecaster($config);
    $lat = '42.16';
    $lng = '-72.72';
    $startTime = date('Y-m-d', time());
    $numDays = 7;
    try {
        // returns a Forecast object or throws an exception on API error
        $forecast = $forecaster->getForecastByLatLng($lat, $lng, $startTime, $numDays);
    } catch (\Exception $e) {
        echo "There was an error fetching the forecast: " . $e->getMessage() . "\n";
    }
    // get ForecastDay object for the first day of the forecast
    $day = $forecast->getDay(0);
    echo "High Temperature: " . $day->getHighTemperature() . "\n";

You can find more sample code in the example/forecast.php file.

Nightly Forecasts
-----------------

For forecasts made at night (after 6pm local time), the first day in the forecast result will be for "tomorrow."
The doesStartAtNight() method will tell you if a forecast is a "night time" forecast:

    $forecast->doesStartAtNight();

There will also be one item of information that's still of value for "today," and that is the precipitation probability from 6pm-6am.
That probability can be retrieved like so:

    if ($forecast->doesStartAtNight()) {
        $tonightsPrecipitation = $forecast->getPrecipitationProbabilityTonight();
    }


Caching
========

NOAA suggests that consumers of their API ask for new data only once per hour.
This library offers a simple to use FileCache class that only requires a writable directory in which to store the cached data.
It will handle cache invalidation on its own based on file modification time.
The directory you set must exist and be writable.

If you prefer to use a different cache other than the supplied FileCache it's easy to create your own.
Just create a class that implements the \noaa\weather\cache\Cache interface.
Use the setCache() Configuration method to set your own custom caching class.
See the noaa/weather/cache/ArrayCache.php file for an example.

To instantiate a Forecaster object without a caching mechanism (not recommended!):

    require_once 'noaa/Forecaster.php';
    $forecaster = new \noaa\Forecaster();
