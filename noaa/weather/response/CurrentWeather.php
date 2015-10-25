<?php

namespace noaa\weather\response;

use noaa\weather\response\Response;

class CurrentWeather extends Response {

    /**
     * Returns the current temperature in Fahrenheit.
     */
    public function getTemperatureF() {
        return (float)$this->value('temp_f');
    }

    /**
     * Returns the current temperature in Celsius.
     */
    public function getTemperatureC() {
        return (float)$this->value('temp_c');
    }

    /**
     * Returns the current dew point
     */
    public function getDewPointF() {
        return (float)$this->value('dewpoint_f');
    }

    /**
     * Returns the current dew point
     */
    public function getDewPointC() {
        return (float)$this->value('dewpoint_c');
    }

    /**
     * Returns the current pressure in millibars
     */
    public function getPressureMB() {
        return (float)$this->value('pressure_mb');
    }

    /**
     * Returns the current pressure in Inches of Mercury
     */
    public function getPressureInHg() {
        return (float)$this->value('pressure_in');
    }

    /**
     * Returns the RFC822 time of the weather observation.
     */
    public function getObservationTime() {
        return $this->value('observation_time_rfc822');
    }

    /**
     * Returns the current relative humidity.
     */
    public function getRelativeHumidity() {
        return (int)$this->value('relative_humidity');
    }

    /**
     * Returns the location name of the observation.
     */
    public function getLocation() {
        return $this->value('location');
    }

    /**
     * Returns the visibility in Miles.
     */
    public function getVisibilityMiles() {
        return (float)$this->value('visibility_mi');
    }

    /**
     * Returns the current weather string.
     * All possible values: http://w1.weather.gov/xml/current_obs/weather.php
     */
    public function getWeather() {
        return (string)$this->value('weather');
    }

    /**
     * Returns the direction of the wind in Degrees.
     */
    public function getWindDegrees() {
        return (int)$this->value('wind_degrees');
    }

    /**
     * Returns the direction of the wind as a string.
     */
    public function getWindDirection() {
        return (string)$this->value('wind_dir');
    }

    /**
     * Returns the string of wind condition information.
     */
    public function getWindString() {
        return (string)$this->value('wind_string');
    }

    /**
     * Returns the wind speed in MPH.
     */
    public function getWindSpeedMPH() {
        return (float)$this->value('wind_mph');
    }

    /**
     * Returns the wind sped in Knots.
     */
    public function getWindSpeedKnots() {
        return (float)$this->value('wind_kt');
    }

    /**
     * Returns the icon URL for the current weather conditions
     */
    public function getIcon() {
        $urlBase  = (string)$this->value('icon_url_base');
        $filename = (string)$this->value('icon_url_name');
        return $urlBase . $filename;
    }

    /**
     * Returns the icon filename for the current weather conditions
     */
    public function getIconName() {
        return (string)$this->value('icon_url_name');
    }

    /**
     * Returns the station ID that made this observation.
     */
    public function getStationId() {
        return (string)$this->value('station_id');
    }

    /**
     * Returns the latitude at which this observation was made.
     */
    public function getLatitude() {
        return (float)$this->value('latitude');
    }

    /**
     * Returns the longitude at which this observation was made.
     */
    public function getLongitude() {
        return (float)$this->value('longitude');
    }

    /**
     * Returns the date and time at which this observation was made.
     */
    public function getTime() {
        return (string)$this->value('observation_time_rfc822');
    }

    /**
     * Returns a specific value from the response data
     */
    protected function value($key) {

        // if it's not set yet, get and cache the value from the SimpleXML object
        if (!isset($this->cachedValues[$key])) {
            $path = sprintf("/current_observation/%s[1]", $key);
            $node = $this->xml->xpath($path);
            $val = (string)$node[0];
            $this->cachedValues[$key] = $val;
        }

        return $this->cachedValues[$key];

    }

}