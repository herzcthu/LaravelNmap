<?php
namespace LaravelNmap;

use LaravelNmap\LaravelNmap as Nmap;
use PHPUnit_Framework_TestCase;

class getXMLTest extends PHPUnit_Framework_TestCase
{
	public function testgetXmlObject()
        {
            /*
             * Set true to get root permission if php user is in sudo group
             */
            $nmap = new Nmap(true);
            $xml = $nmap->detectOS()->setTarget('192.168.43.0/24')->setTimeout('300')->getXmlObject();
            echo "\n*** This function run with root permission \n*** if php user is in sudo group.\n*** If not, run in normal user ***\n";
            print_r($xml);
            echo "\n*** end xml output test***\n";
        }
}
