<?php

namespace noaa;

use noaa\weather\Base,
	noaa\weather\Configuration,
	noaa\weather\response\CurrentWeather,
	noaa\weather\response\Forecast;

set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__)));
require_once 'weather/Base.php';
require_once 'weather/Configuration.php';
require_once 'weather/response/Response.php';
require_once 'weather/response/CurrentWeather.php';
require_once 'weather/response/Forecast.php';
require_once 'weather/response/ForecastDay.php';
require_once 'weather/response/Conditions.php';
require_once 'weather/cache/Cache.php';
require_once 'weather/cache/ArrayCache.php';
require_once 'weather/cache/FileCache.php';
require_once 'weather/cache/NoCache.php';

class Forecaster extends Base {

	protected $configuration;
	protected $cache;

	/**
	 * Constructor
	 */
	public function __construct($configuration=null) {
		
		if ($configuration !== null) {
			$this->setConfiguration($configuration);
		} else {
			$this->setConfiguration(new Configuration());
		}

		$this->cache = $this->getConfiguration()->getCache();

	}

	/**
	 * Returns the current weather for a specific station ID.
	 * For the station ID see: http://www.weather.gov/xml/current_obs/
	 * Or for an XML list of stations: http://www.weather.gov/xml/current_obs/index.xml
	 *
	 * @param string $stationId The NOAA weather observation station ID.
	 */
	public function getCurrentWeather($stationId) {
		
		// get the XML data
		$url = sprintf('http://www.weather.gov/xml/current_obs/%s.xml', $stationId);

		// fetch the xml if possible
		try {
			$xmlString = $this->fetchXml($url, 'current');
		} catch (\Exception $e) {
			throw $e;
		}

		// instantiate a CurrentWeather response object
		try {
			$currentWeatherResponse = new CurrentWeather($xmlString);	
		} catch (\Exception $e) {
			throw $e;
		}
		
		return $currentWeatherResponse;

	}

	/**
	 * Gets the forecast data from NOAA's NDFD REST service
	 * Docs: http://graphical.weather.gov/xml/rest.php
	 * NDFD Technical: http://www.nws.noaa.gov/ndfd/technical.htm
	 *
	 * @param string $lat The latitude
	 * @param string $lng The longitude
	 * @param string $startDate The day (local to the location) to start the forecast data at. Preferably YYYY-mm-dd format, no time included.
	 * @param int $days The number of days to retrieve the forecast for.
	 */
	public function getForecastByLatLng($lat, $lng, $startDate=null, $days=7) {

		// convert the dates to standardized UTC time
		$startDate = ($startDate === null) ? date('Y-m-d') : $startDate;
		$startDate = gmdate('Y-m-d', strtotime($startDate));

		// initialize the required parameters and set which data elements we want
		$parameters = array(
			// required
			'lat'       => $lat,
			'lon'       => $lng,
			'startDate' => $startDate,
			'numDays'   => $days,
			'format'    => '24 hourly',
			'Unit'      => 'e',
			// forecast elements: http://graphical.weather.gov/xml/docs/elementInputNames.php
			/*
			'temp'  => 'temp',
			'maxt'  => 'maxt',
			'mint'  => 'mint',
			'temp'  => 'temp',
			'snow'  => 'snow',
			'dew'   => 'dew',
			'wspd'  => 'wspd',
			'wdir'  => 'wdir',
			'sky'   => 'sky',
			'wx'    => 'wx',
			'icons' => 'icons',
			'rh'    => 'rh',
			'appt'  => 'appt',
			'pop12' => 'pop12',
			'qpf'   => 'qpf',
			'wwa'   => 'wwa',
			*/
		);

		// build the request URL query string
		$queryString = http_build_query($parameters);
		$url = sprintf("http://graphical.weather.gov/xml/sample_products/browser_interface/ndfdBrowserClientByDay.php?%s", $queryString);

		// fetch the xml if possible
		try {
			$xmlString = $this->fetchXml($url, 'forecast');
		} catch (\Exception $e) {
			throw $e;
		}

		// instanstiate a Forecast response object
		try {
			$forecast = new Forecast($xmlString);
		} catch (\Exception $e) {
			throw $e;
		}
		
		return $forecast;

	}

	/**
	 * Fetches the XML data from the given URL.
	 *
	 * @param string $url The URL to fetch.
	 */
	protected function fetchXml($url, $type) {

		// unique cache id for this station id
		$cacheId = $type . '_' . md5($url);

		// try to find it in the cache
		if ($this->cache->contains($cacheId)) {
			
			$xmlString = $this->cache->fetch($cacheId);

		} else {
			
			// not in cache, must fetch remotely
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // seconds
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$xmlString = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			// make sure we got a valid response
			if ($httpCode != 200) {
				throw new \Exception('Received invalid HTTP response code: ' . $httpCode);
			}

			// cache the string for an hour
			$this->cache->save($cacheId, $xmlString);

		}
		
		return $xmlString;

	}

}