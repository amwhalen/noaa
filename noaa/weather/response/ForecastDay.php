<?php

namespace noaa\weather\response;

use noaa\weather\Base;

class ForecastDay extends Base {

	// start
	// end
	// high
	// low
	// weather conditions
	// conditions icon
	// daytime (6am-6pm) probability of precipitation (12 hour)
	// nighttime (6pm-6am) probability of precipitation (12 hour)

	protected $startTime;
	protected $endTime;
	protected $highTemperature;
	protected $lowTemperature;
	protected $conditions;
	protected $icon;
	protected $precipitationProbabilityDay;
	protected $precipitationProbabilityNight;

}