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
	 */
	public function __toString() {
		
		$str = $this->summary;

		if (count($this->values) > 0) {
			$str .= ' (';
			$vals = array();
			foreach ($this->values as $v) {
				// handle "none" for intensity, ex: "patchy none fog"
				if (isset($v['additive'])) {
					$vals[] = sprintf('%s %s %s %s', $v['additive'], $v['coverage'], $v['intensity'], $v['weather-type']);
				} else {
					$vals[] = sprintf('%s %s %s', $v['coverage'], $v['intensity'], $v['weather-type']);
				}
			}
			$str .= implode(' ', $vals);
			$str .= ')';
		}

		return $str;
		
	}

}