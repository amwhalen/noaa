<?php

namespace noaa\weather\response;

use noaa\weather\Base;

class Response extends Base {

    protected $xml;
    protected $cachedValues = array();

    /**
     * Constructor
     * More Info: http://w1.weather.gov/xml/current_obs/
     *
     * @param string $xmlString The XML response from NOAA.
     */
    public function __construct($xmlString) {

        // suppress XML Warnings
        libxml_use_internal_errors(true);

        // parse the XML document
        try {
            $this->xml = new \SimpleXMLElement($xmlString);
        } catch (\Exception $e) {
            throw new \Exception('Failed to parse the weather data. ' . $e->getMessage());
        }

    }

}