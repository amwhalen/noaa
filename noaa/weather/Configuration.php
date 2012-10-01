<?php

namespace noaa\weather;

use noaa\weather\Base,
	noaa\weather\cache\Cache,
	noaa\weather\cache\ArrayCache;

class Configuration extends Base {

	protected $cache;
	protected $temperatureScale;
	protected $distanceUnit;

	public function __construct() {

		// defaults
		$this->setCache(new ArrayCache());
		$this->setTemperatureScale('F');
		$this->setDistanceUnit('miles');

	}

}