<?php

namespace noaa\weather\response;

use noaa\weather\Base;

class TimeLayout extends Base {

    protected $key;
    protected $timeCoordinate;
    protected $summary;
    protected $validTimes = array();

    /**
     * Constructor
     */
    public function __construct($key, $timeCoordinate, $summary, $validTimes) {
        $this->setKey($key);
        $this->setTimeCoordinate($timeCoordinate);
        $this->setSummary($summary);
        $this->setValidTimes($validTimes);
    }

}