<?php
namespace LaravelNmap;

use LaravelNmap\LaravelNmap as Nmap;
use PHPUnit_Framework_TestCase;

class LaravelNmapTest extends PHPUnit_Framework_TestCase
{
	public function testNmapHelp()
	{
            $nmap = new Nmap();
            $help = $nmap->NmapHelp();
            //echo "*** This help is run without root permission ***\n";
            //echo $help;
            //echo "\n*** End help ***\n\n\n";
	}
}
