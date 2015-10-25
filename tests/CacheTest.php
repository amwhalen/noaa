<?php

require_once dirname(__FILE__) . '/../noaa/weather/cache/Cache.php';
require_once dirname(__FILE__) . '/../noaa/weather/cache/ArrayCache.php';
require_once dirname(__FILE__) . '/../noaa/weather/cache/NoCache.php';

class CacheTest extends PHPUnit_Framework_TestCase {

    public function testArrayCache() {

        $c = new \noaa\weather\cache\ArrayCache();

        $saved = $c->save('test', 'testdata');
        $this->assertTrue($saved);
        $this->assertTrue($c->contains('test'));
        $this->assertEquals('testdata', $c->fetch('test'));
        $c->delete('test');
        $this->assertFalse($c->contains('test'));

        $c->save('test', 'testdata');
        $c->save('test1', 'testdata');
        $c->save('test2', 'testdata');
        $this->assertTrue($c->contains('test'));
        $this->assertTrue($c->contains('test1'));
        $this->assertTrue($c->contains('test2'));
        $c->flush();
        $this->assertFalse($c->contains('test'));
        $this->assertFalse($c->contains('test1'));
        $this->assertFalse($c->contains('test2'));

    }

    public function testNoCache() {

        $c = new \noaa\weather\cache\NoCache();

        $this->assertFalse($c->save('test', 'testdata'));
        $this->assertFalse($c->fetch('false'));
        $this->assertFalse($c->contains('false'));
        $this->assertTrue($c->delete('false'));

    }

}