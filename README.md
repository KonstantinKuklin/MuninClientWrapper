[![Build Status](https://travis-ci.org/KonstantinKuklin/MuninClientWrapper.svg?branch=master)](https://travis-ci.org/KonstantinKuklin/MuninClientWrapper)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/KonstantinKuklin/MuninClientWrapper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/KonstantinKuklin/MuninClientWrapper/?branch=master)

README
======

What is MuninClientWrapper?
-----------------

MuninClientWrapper is a PHP wrapper for munin node. It is fully written on PHP.
It allows developers to connect to munin-node and get munin version, list of installed plugins,
plugins values, plugins configs.

Requirements
------------

MuninClientWrapper is only supported on `PHP 5.3` and up.

Installation
------------

The best way to install MuninClientWrapper is via Composer:
   ```
   php composer.phar require konstantin-kuklin/munin-client-wrapper:dev-master  
   ```

Documentation
------------

### How get munin version:

   ```
   $muninClient = new \Munin\Client($host,$port = 4949);
   $muninClient->getVersion();
   ```

will return string with value like: `2.0.16`

$port not required, by default 4949


### How get plugins list:
   ```
   $muninClient = new \Munin\Client($host,$port = 4949);
   $muninClient->getPluginsList();
   ```

will return array vector like: array([0] => 'cpu', [1] => 'df' .... [29] => 'uptime')

### How get plugin values:
   ```
   $muninClient = new \Munin\Client($host);
   $muninClient->getPluginValue('cpu');
   ```

will return array map like: array([user] => 234234, [nice] => 3573 [system] => 8644 ....)

### How to change timeout for reading:

   ```
   $muninClient = new \Munin\Client($host);
   $muninClient->setReadTimeOut($seconds, $microseconds = 0)
   ```
   
by default is `2 seconds` for each read operation from munin socket.

