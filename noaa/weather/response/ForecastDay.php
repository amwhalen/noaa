<?php

namespace noaa\weather\response;

use noaa\weather\Base;

class ForecastDay extends Base {

    protected $startTime;
    protected $endTime;
    protected $highTemperature;
    protected $lowTemperature;
    protected $conditions;
    protected $icon;
    protected $precipitationProbabilityDay;
    protected $precipitationProbabilityNight;

}