[![Latest Stable Version](https://poser.pugx.org/pmvc-app/lucency/v/stable)](https://packagist.org/packages/pmvc-app/lucency) 
[![Latest Unstable Version](https://poser.pugx.org/pmvc-app/lucency/v/unstable)](https://packagist.org/packages/pmvc-app/lucency) 
[![Build Status](https://travis-ci.org/pmvc-app/lucency.svg?branch=master)](https://travis-ci.org/pmvc-app/lucency)
[![License](https://poser.pugx.org/pmvc-app/lucency/license)](https://packagist.org/packages/pmvc-app/lucency)
[![Total Downloads](https://poser.pugx.org/pmvc-app/lucency/downloads)](https://packagist.org/packages/pmvc-app/lucency) 

PMVC lucency growth hacking app 
===============

## disableIframe
   * What is it?
      * When lucency's top frame not in same domain, we need prevent lucency to extra top frame data because corssdomain issue.
   * default: false
   * pass with GET ?if=1 -> disableIframe equal true 

## How to log startup time
1. Log start up time in very begin.
```
<head>
<script>var startUpTime=new Date().getTime()</script>
<!-- ... your code -->
</head>
```
2. Beacon will auto fire with view beacon
https://github.com/react-atomic/react-atomic-organism/blob/7130aa7402c09ae25da281b39b41b102dddaaff4/packages/organism-react-i13n/ui/organisms/I13nElement.jsx#L134-L136

## How to split tag template variable
Each tag have own namespace under data->lucency->*tagname*
```
$view->get('data')['lucency'][*tagname*]
```
## disalbe canonical for facebook pixel debug
?--no-canonical=1


## Install with Composer
### 1. Download composer
   * mkdir test_folder
   * curl -sS https://getcomposer.org/installer | php

### 2. Install Use composer.json or use command-line directly
#### 2.1 Install Use composer.json
   * vim composer.json
```
{
    "require": {
        "pmvc-app/lucency": "dev-master"
    }
}
```
   * php composer.phar install

#### 2.2 Or use composer command-line
   * php composer.phar require pmvc-app/lucency

## Lucency family
### Lucency Template for call remote app (GA, FB Pixel, Heap)
   * https://github.com/pmvc-theme/lucency_html
### Lucency interaction package (fire beacon from client side)
   * https://github.com/react-atomic/react-atomic-organism/tree/master/packages/organism-react-i13n
### Lucency plugin (for assign pvid)
   * https://github.com/pmvc-plugin/lucency

