<?php
namespace LaravelNmap;

use LaravelNmap\LaravelNmap as Nmap;
use PHPUnit_Framework_TestCase;

class getXMLTest extends PHPUnit_Framework_TestCase
{
	public function testgetXmlObject()
        {
            $nmap = new Nmap();
            $xml = $nmap->target('nm.laradock.app')->getArray();
            print_r($xml);
        }
}
