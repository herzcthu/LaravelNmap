<?php
namespace LaravelNmap;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class LaravelNmap
{
        private $process;
        private $arguments = [];
        private $options = [];
        private $input;
        private $timeout = 300;
        public $result;
        
        public function __construct($sudo = false) {           
            
            if($sudo === true) {
                /*
                 * Test user is in sudo group
                 */
                $group = new Process('groups | grep " sudo "');
                $group->run();
                $sudogroup = $group->getOutput();
                if(!empty($sudogroup)) {
                    $prefix = 'sudo';
                    $this->arguments[] = 'nmap';
                } else {
                    $prefix = 'nmap';   
                }
            } else {
                $prefix = 'nmap';
            }
            $this->process = new ProcessBuilder();
            $this->process->setPrefix($prefix);
	}
        
        
        public function NmapHelp() {
            /*
             * set argument
             */
            $arguments = ['--help'];
            /*
             * get new Process instance 
             */
            $process = $this->process->setArguments($arguments)
                        ->getProcess();
            /*
             * run the process
             */
            $process->run();
            /*
             * get process output for nmap help
             */
            return $process->getOutput();
	}
        
        public function verbose() {
            /*
             * set argument for verbosity
             */
            $this->arguments[] = '-v';
            return $this;
        }
        
        public function detectOS($fastscan = true) {
            if($fastscan === true) {
                $this->arguments[] = '-F';
            }
            /*
             * set argument for OS detection
             */
            $this->arguments[] = '-O';
            return $this;
        }
        
        public function getServices() {
            /*
             * set argument to scan running services
             * if you use this method, use scanPorts() method also to reduce process run time
             */
            $this->arguments[] = '-sV';
            return $this;
        }
        
        public function disablePortScan() {
            /*
             * set argument to disable port scanning
             * Warning: this method should not be used if using '-p' switch somewhere
             */
            $this->arguments[] = '-sn';
            return $this;
        }
        
        /*
         * set ports to scan
         */
        public function scanPorts($ports) {
            $this->arguments[] = '-p'.$ports;
            return $this;
        }
        
        /*
         * set target host or networks seperated by space
         */
        public function setTarget($target) {
            $this->arguments[] = $target;
            return $this;
        }
        
        /*
         * set environment variables
         */
        public function setEnv($name, $value) {
            $this->process->setEnv($name, $value);
            return $this;
        }
        
        /*
         * set process timeout
         */
        public function setTimeout($timeout) {
            $this->timeout = $timeout;
            $this->process->setTimeout($this->timeout);
            return $this;
        }
        
        /*
         * set current working directory
         */
        public function cwd($cwd) {
            $this->process->setWorkingDirectory($cwd);
            return $this;
        }
        
        /**
         * 
         * @return SimpleXMLElement Object
         */
        public function getXmlObject() {
            $this->arguments[] = '-oX';
            // this argument is needed to get xml output to stdout
            $this->arguments[] = '-';
            
            $process = $this->process->setArguments($this->arguments)->getProcess();
            
            $process->run();
            
            $xmldata = $process->getOutput();
            return $xmldata;             
        }
        
        public function getArray() {
            $xmldata = $this->getXmlObject();
            $xml = simplexml_load_string($xmldata);
            $array = [];
            foreach($xml->host as $host) {
                $hostaddr = (string) $host->address->attributes()->addr;
                foreach($host->children() as $type => $info) {
                    switch ($type) {
                        case 'address':
                            $type = (string) $info->attributes()->addrtype;
                            break;
                        case 'ports':
                            $info = $this->getPorts($info);
                            break;
                        case 'os':
                            $os['name'] = isset($info->osmatch[0])?(string) $info->osmatch[0]->attributes()->name:'';
                            $os['vendor'] = isset($info->osmatch[0]->osclass)?(string) $info->osmatch[0]->osclass->attributes()->vendor:'';
                            $os['osfamily'] = isset($info->osmatch[0]->osclass)?(string) $info->osmatch[0]->osclass->attributes()->osfamily:'';
                            $os['osgen'] = isset($info->osmatch[0]->osclass)?(string) $info->osmatch[0]->osclass->attributes()->osgen:'';
                            $info = $os;
                            break;
                        case 'status';
                            $info = (string) $info->attributes()->state;
                            break;
                        case 'uptime';
                            $uptime['seconds'] = (string) $info->attributes()->seconds;
                            $uptime['lastboot'] = (string) $info->attributes()->lastboot;
                            $info = $uptime;
                            break;
                        default:
                            $info = array_values((array) $info);
                            break;
                    }
                        $array[$hostaddr][$type] = $info;                    
                }
                /*
                $addr = (string) $host->address->attributes()->addr;
                $addrtype = (string) $host->address->attributes()->addrtype;
                $array[$addr][$addrtype]['addr'] = $addr;
                $array[$addr][$addrtype]['type'] = $addrtype;
                if(isset($host->status)) {
                    $array[$addr]['state'] = (string) $host->status->attributes()->state;
                }
                if(isset($host->uptime)) {
                    $array[$addr]['uptime'] = (string) $host->uptime->attributes()->seconds;
                    $array[$addr]['lastboot'] = (string) $host->uptime->attributes()->lastboot;
                }
                if(isset($host->hostnames)) {
                    $array[$addr]['hostname'] = isset($host->hostnames->hostname)?call_user_func_array('array_merge',(array)$host->hostnames->hostname):[];
                }
                if(isset($host->ports)) {
                    $array[$addr]['ports'] = isset($host->ports->port)?$this->getPorts($host->ports->port):[];
                }
                 * 
                 */
            }
            return $array;
        }

        public function getRawOutput() {
            $process = $this->process->setArguments($this->arguments)->getProcess();
            
            $process->run();
            
            return $process->getOutput();
        }
        
        private function getPorts(\SimpleXMLElement $xmlPorts) {
            $ports = [];
            //return $xmlPorts;
            foreach($xmlPorts as $type => $port) {
                $portid = (string) $port->attributes()->portid;
                if(!empty($portid)) {
                $ports[$portid]['protocol'] = (string) $port->attributes()->protocol;
                $ports[$portid]['state'] = (string) $port->state->attributes()->state;
                $ports[$portid]['service'] = (string) $port->service->attributes()->name;
                }
            }
            return $ports;
        }
}
