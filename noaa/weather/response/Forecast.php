<?php

namespace noaa\weather\response;

use noaa\weather\response\Response,
    noaa\weather\response\ForecastDay,
    noaa\weather\response\Conditions,
    noaa\weather\response\TimeLayout,
    noaa\weather\response\Hazard;

class Forecast extends Response {

    protected $length;
    protected $days;
    protected $precipitationProbabilityTonight;

    /**
     * Constructor
     *
     * @param string $xmlString The XML forecast data.
     */
    public function __construct($xmlString) {

        parent::__construct($xmlString);

        // calculate the number of days in the forecast
        $this->length = count($this->getStartTimes());

        // array cache for days
        $this->days = array();
    }

    /**
     * Returns a ForecastDay object for the given forecast index
     *
     * @param int $index The zero-based index of the day to return the forecast for.
     * @return null|ForecastDay Returns a ForecastDay object if one is found at the index, or NULL if the index is or out of bounds.
     */
    public function getDay($index) {

        if ($index < 0 || $index >= $this->length) {
            return null;
        }

        // try to find the cached day
        if (!isset($this->days[$index])) {

            $highs      = $this->getHighTemperatures();
            $lows       = $this->getLowTemperatures();
            $starts     = $this->getStartTimes();
            $ends       = $this->getEndTimes();
            $conditions = $this->getConditions();
            $icons      = $this->getIcons();
            $precips    = $this->getPrecipitationProbabilities();

            // instantiate, cache, and return new ForecastDay
            $day = new ForecastDay();
            $day->setHighTemperature($highs[$index]);
            $day->setLowTemperature($lows[$index]);
            $day->setStartTime($starts[$index]);
            $day->setEndTime($ends[$index]);
            $day->setConditions($conditions[$index]);
            $day->setIcon($icons[$index]);
            $day->setPrecipitationProbabilityDay($precips[$index*2]);
            $day->setPrecipitationProbabilityNight($precips[$index*2+1]);

            $this->days[$index] = $day;

        }

        return $this->days[$index];

    }

    /**
     * Returns the date and time this Forecast was created.
     * @return string The forecast as a date and time like "2013-11-08T15:01:34Z"
     */
    public function getCreationDate() {

        $nodes = $this->xml->xpath("/dwml/head[1]/product[1]/creation-date");
        return (string)$nodes[0];

    }

    /**
     * Returns any Hazards (Watches, Warnings, and Advisories) for the forecast time period.
     *
     * SINGLE XML:
     * <hazards time-layout="k-p6h-n1-3">
     *   <name>Watches, Warnings, and Advisories</name>
     *   <hazard-conditions>
     *     <hazard hazardCode="LW.Y" phenomena="Lake Wind" significance="Advisory" hazardType="long duration">
     *       <hazardTextURL>http://forecast.weather.gov/wwamap/wwatxtget.php?cwa=usa&amp;wwa=Lake%20Wind%20Advisory</hazardTextURL>
     *     </hazard>
     *   </hazard-conditions>
     * </hazards>
     *
     * EMPTY XML:
     * <hazards time-layout="k-p7d-n1-3">
     *   <name>Watches, Warnings, and Advisories</name>
     *   <hazard-conditions xsi:nil="true"/>
     * </hazards>
     *
     */
    public function getHazards() {

        $hazards = array();

        $allHazards = $this->xml->xpath("/dwml/data[1]/parameters[1]/hazards");
        foreach ($allHazards as $hazardsNode) {
            $nodes = $hazardsNode->xpath("hazard-conditions[1]/hazard");
            if (count($nodes) > 0) {
                foreach ($nodes as $node) {
                    $tlk = (string) $hazardsNode["time-layout"];
                    $h = new Hazard((string)$node["hazardCode"], (string)$node["phenomena"], (string)$node["significance"], (string)$node["hazardType"], $tlk);
                    $hazards[] = $h;
                }
            }
        }

        return $hazards;

    }

    /**
     * Returns an array of the daily maximum temperatures
     */
    public function getHighTemperatures() {

        $nodes = $this->xml->xpath("/dwml/data[1]/parameters[1]/temperature[@type='maximum'][1]/value");
        $temps = array();
        foreach ($nodes as $node) {
            // test for the xsi:nil="true" attribute, which denotes that this node has no value
            $nil = (boolean)$node->attributes('xsi', true)->nil;
            if ($nil) {
                $temps[] = null;
            } else {
                $temps[] = (int)$node[0];
            }
        }
        return $temps;

    }

    /**
     * Returns an array of the daily minimum temperatures
     */
    public function getLowTemperatures() {

        $nodes = $this->xml->xpath("/dwml/data[1]/parameters[1]/temperature[@type='minimum'][1]/value");
        $temps = array();
        if (count($nodes) > 0) {
            foreach ($nodes as $node) {
                // test for the xsi:nil="true" attribute, which denotes that this node has no value
                $nil = (boolean)$node->attributes('xsi', true)->nil;
                if ($nil) {
                    $temps[] = null;
                } else {
                    $temps[] = (int)$node[0];
                }
            }
        }
        return $temps;

    }

    /**
     * Returns the weather condition icons for all days.
     */
    public function getIcons() {
        $nodes = $this->xml->xpath("/dwml/data[1]/parameters[1]/conditions-icon[1]/icon-link");
        $icons = array();
        foreach ($nodes as $node) {
            $icons[] = (string)$node[0];
        }
        return $icons;
    }

    /**
     * Returns the start dates and times of each forecast day
     */
    public function getPrecipitationProbabilities() {
        $nodes = $this->xml->xpath("/dwml/data[1]/parameters[1]/probability-of-precipitation[1]/value");
        $probs = array();
        if (count($nodes) > 0) {
            // if this forecast starts at night time, it contains tonight's precip probability
            // but no other piece of information about tonight's forecast, so remove the first item
            // since it shouldn't go in a ForecastDay object
            if ($this->doesStartAtNight()) {
                // remove the first item in the array
                $tonight = array_shift($nodes);
                // add a null to the end
                array_push($nodes, end($nodes));
                // set tonight's precip
                $this->precipitationProbabilityTonight = (int)$tonight[0];
            } else {
                $this->precipitationProbabilityTonight = null;
            }
            foreach ($nodes as $node) {
                // test for the xsi:nil="true" attribute, which denotes that this node has no value
                $nil = (boolean)$node->attributes('xsi', true)->nil;
                if ($nil) {
                    $probs[] = null;
                } else {
                    $probs[] = (int)$node[0];
                }
            }
        }
        return $probs;
    }

    /**
     * Returns tonight's chance of precipitation
     */
    public function getPrecipitationProbabilityTonight() {
        if (!isset($this->precipitationProbabilityTonight)) {
            // this call sets the variable
            $this->getPrecipitationProbabilities();
        }
        return $this->precipitationProbabilityTonight;
    }

    /**
     * Returns the end dates and times of each forecast day
     */
    public function getEndTimes() {
        $nodes = $this->xml->xpath("/dwml/data[1]/time-layout[@summarization='24hourly'][1]/end-valid-time");
        $times = array();
        foreach ($nodes as $node) {
            $times[] = (string)$node[0];
        }
        return $times;
    }

    /**
     * Returns the 12-hour end dates and times
     */
    public function getEndTimes12Hour() {
        $nodes = $this->xml->xpath("/dwml/data[1]/time-layout[@summarization='12hourly'][1]/end-valid-time");
        $times = array();
        foreach ($nodes as $node) {
            $times[] = (string)$node[0];
        }
        return $times;
    }

    /**
     * Returns the start dates and times of each forecast day
     */
    public function getStartTimes() {
        $nodes = $this->xml->xpath("/dwml/data[1]/time-layout[@summarization='24hourly'][1]/start-valid-time");
        $times = array();
        foreach ($nodes as $node) {
            $times[] = (string)$node[0];
        }
        return $times;
    }

    /**
     * Returns the 12-hour start dates and times
     */
    public function getStartTimes12Hour() {
        $nodes = $this->xml->xpath("/dwml/data[1]/time-layout[@summarization='12hourly'][1]/start-valid-time");
        $times = array();
        foreach ($nodes as $node) {
            $times[] = (string)$node[0];
        }
        return $times;
    }

    /**
     * Returns time layouts used for different forecast data points.
     *
     * <time-layout time-coordinate="local" summarization="24hourly">
     * <layout-key>k-p24h-n7-1</layout-key>
     * <start-valid-time>2012-10-01T06:00:00-04:00</start-valid-time>
     * <end-valid-time>2012-10-02T06:00:00-04:00</end-valid-time>
     * <start-valid-time>2012-10-02T06:00:00-04:00</start-valid-time>
     * <end-valid-time>2012-10-03T06:00:00-04:00</end-valid-time>
     * <start-valid-time>2012-10-03T06:00:00-04:00</start-valid-time>
     * <end-valid-time>2012-10-04T06:00:00-04:00</end-valid-time>
     * <start-valid-time>2012-10-04T06:00:00-04:00</start-valid-time>
     * <end-valid-time>2012-10-05T06:00:00-04:00</end-valid-time>
     * <start-valid-time>2012-10-05T06:00:00-04:00</start-valid-time>
     * <end-valid-time>2012-10-06T06:00:00-04:00</end-valid-time>
     * <start-valid-time>2012-10-06T06:00:00-04:00</start-valid-time>
     * <end-valid-time>2012-10-07T06:00:00-04:00</end-valid-time>
     * <start-valid-time>2012-10-07T06:00:00-04:00</start-valid-time>
     * <end-valid-time>2012-10-08T06:00:00-04:00</end-valid-time>
     * </time-layout>
     *
     * @return array An array of TimeLayout objects.
     */
    public function getTimeLayouts() {

        $nodes = $this->xml->xpath("/dwml/data[1]/time-layout");
        $layouts = array();
        if (count($nodes) > 0) {
            foreach ($nodes as $node) {
                $key = (string)$node->{'layout-key'};
                $startTimes = $node->{'start-valid-time'};
                $endTimes = $node->{'end-valid-time'};
                $validTimes = array();
                $i = 0;
                foreach ($startTimes as $st) {
                    $validTimes[] = array((string)$st, (string)$endTimes[$i]);
                    $i++;
                }
                $tl = new TimeLayout($key, (string)$node['time-coordinate'], (string)$node['summarization'], $validTimes);
                $layouts[$key] = $tl;
            }
        }
        return $layouts;

    }

    /**
     * Get a TimeLayout object by key.
     *
     * @param  string $key The key, such as 'k-p24h-n7-1'.
     * @return TimeLayout TimeLayout object.
     */
    public function getTimeLayoutByKey($key) {
        $tls = $this->getTimeLayouts();
        if (array_key_exists($key, $tls)) {
            return $tls[$key];
        } else {
            return false;
        }
    }

    /**
     * Returns the weather conditions for all days.
     */
    public function getConditions() {
        $nodes = $this->xml->xpath("/dwml/data[1]/parameters[1]/weather[1]/weather-conditions");
        $conditions = array();
        foreach ($nodes as $node) {
            $summary = (string)$node['weather-summary'];
            $values = array();
            foreach ($node->value as $value) {
                $arr = array(
                    'coverage' => (string)$value['coverage'],
                    'intensity' => (string)$value['intensity'],
                    'weather-type' => (string)$value['weather-type'],
                    'qualifier' => (string)$value['qualifier']
                );
                if (isset($value['additive'])) {
                    $arr['additive'] = (string)$value['additive'][0];
                }
                $values[] = $arr;
            }
            $condition = new Conditions($summary, $values);
            $conditions[] = $condition;
        }
        return $conditions;
    }

    /**
     * Returns TRUE if this forecast starts at night (the end of a day) instead of in the morning.
     *
     * If a forecast starts at night, it will contain one item of information that's still relevant to "today",
     * which is the precipitation probability for "tonight", which spans from 6pm today to 6am tomorrow morning.
     */
    public function doesStartAtNight() {
        $tmpStartdate = $this->getStartTimes12Hour();
        $startdate = $tmpStartdate[0];
        preg_match('/.*T([0-9]{2}):.*/i', $startdate, $matches);
        $starthour = $matches[1];
        return ($starthour == '18');
    }

}
