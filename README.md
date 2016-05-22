# LaravelNmap
Nmap wrapper for laravel. 

Not all nmap arguments supported. All arguments method can use method chaining.
But try to use correct combination according to nmap usage.

**See below for supported arguments.**

***--help***
```php
$help = new LaravelNmap();
$help->NmapHelp();
```

***-v*** (Verbose)
```php
$nmap = new LaravelNmap();
$nmap->verbose();
```

***-O*** (Detect OS)
```php
$nmap->detectOS();
```

***-sV*** (Detect Services)
```php
$nmap->getServices();
```

***-sn*** (Disable port scan - same with -sP)
```php
$nmap->disablePortScan();
```

***-p [ports]*** (select port scan - see more for nmap help)
```php
$nmap->scanPorts('22,80,443');
```

*** target *** (This method is mandatory for all scan type)
```php
$nmap->setTarget('192.168.43.0/24');
```


### Output
There are 3 types of output. 
- Nmap raw output for stdout.
- SimpleXML object 
- Array

```php
$nmap->getRawOutput();
$nmap->getXmlObject();
$nmap->getArray();
```

### Security
This package allow to use root permission if php user is in sudo group. Highly discourage if you don't know security risks regarding nmap.
Below code will enable sudo permission -
```php
$nmap = new LaravelNmap(true);
```