<?php
/**
 * Created by PhpStorm.
 * User: Ulugbek
 * Date: 02.02.2018
 * Time: 17:28
 */
namespace muxtor\pkk5component\tests;
use muxtor\pkk5component\Pkk5Component;
use PHPUnit\Exception;
use PHPUnit\Framework\TestCase;

class Pkk5ComponentTest extends TestCase
{
    public function testFailure()
    {
        $this->assertTrue(true);
    }

    /**
     * @param $kadastr
     */
    public function testGetInfo()
    {
        $kadastr = '69:27:0000022:1306,69:27:0000022:1307';
        $data = json_encode((new Pkk5Component)->getInfo($kadastr));
        $this->expectOutputString($data);
        print $data;
    }
}