# Webservices Platform Connector

### External checks
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webservices-nl/platform-connector/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webservices-nl/platform-connector/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/webservices-nl/platform-connector/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/webservices-nl/platform-connector/?branch=master)
[![Build Status](https://travis-ci.org/webservices-nl/platform-connector.svg?branch=master)](https://travis-ci.org/webservices-nl/platform-connector)

>This factory abstracts protocol specific's implementations for connecting to the Webservices.nl API. Providing an unified service layer independent of protocol.


The purpose of this library is to connect to any of the Webservices.nl API's (platforms) in a uniform way. 
Regardless of transport protocol it ships a proxy class for type hinted function calls to Webservices.nl function calls. 

##### Supported protocols
Webservices.nl support multiple protocols for connecting. Soap, XML-RPC, HTTP-RPC/REST. This library has support for multiple transfer protocols. 

##### Soap
This library ships a SoapClient that extends the native PHP `SoapClient` with a curl client for better timeout management. Also converts native PHP ```SoapFault``` into custom platform exceptions where possible.

##### XML-RPC
Scheduled to be released.

##### REST
Scheduled to be released.

### Pre-requisites
- PHP 5.6<=
- [composer](https://getcomposer.org)
- PSR-log LoggerInterface (optional) 

### Install
Please use composer to install this library. Or download the latest [zip](https://github.com/webservices-nl/platform-connector/archive/master.zip)

` composer require webservices-nl/platform-connector `

### Usage

```php
 
 // Instantiate a ConnectorFactory with your given Webservices.nl credentials.
 $factory = ConnectorFactory::build(
  [
    'username' => 'myusername',
    'password' => 'secret'
  ],
  LoggerInterface (optional)
 );
 
 // build a client
 $client = $factory->create('soap', 'webservices');
 
 // make type hinted function calls to any of the Webservices.nl API's
 $response = $client->accountEditV2();
```

All parameters are expected to be in UTF-8 encoding, output is in UTF-8 as well.

#### Unit test
This client is fully tested on PHP 5.+ and 7+ To run tests:

` ./vendor/bin phpunit `

### Further information
Consult the online [documentation](https://webview.webservices.nl/documentation). Any questions, remarks, bugs? Please mail us.
- technical questions: <mailto:tech@webservices.nl>
- support questions: <mailto:support.webservices.nl>