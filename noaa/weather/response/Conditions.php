<?php

namespace noaa\weather\response;

use noaa\weather\Base;

class Conditions extends Base {

	protected $summary;
	protected $values = array();
	
	/**
	 * Constructor
	 */
	public function __construct($summary, $values) {
		$this->setSummary($summary);
		$this->setValues($values);
	}

	/**
	 * String representation
	 * Returns a string like "Chance Rain Showers (chance light rain showers and patchy fog)"
	 */
	public function __toString() {
		
		// start with the summary, usually something like "Chance Rain Showers"
		$str = $this->summary;

		if (count($this->values) > 0) {
			$str .= ' (';
			$vals = array();
			foreach ($this->values as $v) {
				
				$words = array();

				// does this day have multiple conditions? the additive will be set to something like "and"
				if (isset($v['additive'])) {
					$words[] = $v['additive'];
				}

				// "chance", "patchy", etc.
				$words[] = $v['coverage'];

				// ignore "none" for intensity. We don't want to say "patchy none fog", but rather "patchy fog" instead.
				// if set, this is something like "light", 
				if (isset($v['intensity']) && $v['intensity'] !== 'none') {
					$words[] = $v['intensity'];
				}

				// "rain showers", "fog", "snow", etc.
				$words[] = $v['weather-type'];

				// convert all the attributes into a string like "slight chance light rain showers"
				$vals[] = implode(' ', $words);

			}
			$str .= implode(' ', $vals);
			$str .= ')';
		}

		return $str;
		
	}

}