<?php

namespace noaa\weather\response;

use noaa\weather\Base;

class Hazard extends Base {

    protected $code;
    protected $phenomenaCode;
    protected $significanceCode;
    protected $phenomena;
    protected $significance;
    protected $type;
    protected $timeLayoutKey;

    /**
     * Constructor
     */
    public function __construct($code, $phenomena, $significance, $type, $timeLayoutKey) {
        $this->setCode($code);
        $this->setPhenomena($phenomena);
        $this->setSignificance($significance);
        $this->setType($type);
        $this->setTimeLayoutKey($timeLayoutKey);
    }

    public function setCode($code) {
        list($p, $s) = explode('.', $code);
        $this->code = $code;
        $this->setPhenomenaCode($p);
        $this->setSignificanceCode($s);
    }

    public function __toString() {
        return ucwords(sprintf("%s %s %s", $this->getType(), $this->getPhenomena(), $this->getSignificance()));
    }

}