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
            $xml = $nmap->disablePortScan()->setTarget('192.168.7.226,192.168.20.32')->setTimeout('1200')->getArray();
            echo "\n*** This function run with root permission \n*** if php user is in sudo group.\n*** If not, run in normal user ***\n";
            var_dump($xml);
            echo "\n*** end xml output test***\n";
        }
}
