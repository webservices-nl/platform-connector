# Webservices Platform Connector
## PHP platform connector for the Webservices.nl API's

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webservices-nl/platform-connector/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webservices-nl/platform-connector/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/webservices-nl/platform-connector/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/webservices-nl/platform-connector/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/webservices-nl/platform-connector/badges/build.png?b=master)](https://scrutinizer-ci.com/g/webservices-nl/platform-connector/build-status/master)

The goal of this project is to create a easy to implement connector to the Webservices platform. This connector currently supports SOAP to connect to Webservices.nl API's. 

Note: protocol support will be extended with (XML-RPC, REST) to connect to Webservices API. Current implementation 
features a proxy class, that independent of protocol, can be used for type hinted function calls.

### Prerequisites:
- PHP: 5.5+
- composer (https://getcomposer.org)

### Install
Please use composer to install this library.

```bash 
composer require webservices-nl/platform-connector
```

### Usage

```php
 
 // instantiate a connector factory, and build a connector
 $connector = ConnectorFactory::build(['username' => 'myusername', 'password' => 'secret']);
 $soapClient = $connector->create('soap', 'webservices');
 
 // call the different functions with ease...
 $response = $soapClient->getAccountEditV2();
              
 // create a other protocol connector to webservices (not implemented yet)
 $rpcClient  = $connector->create('rpc', 'webservices');
 $restClient = $connector->create('rest', 'webservices');

```

All services offered by Webservices.nl are available as methods in this class.  All parameters are expected to be in 
UTF-8 encoding, output is in UTF-8 as well. For documentation see: https://ws1.webservices.nl/documentation

#### Unit test
This client has been tested on PHP 5.5, 5.6 and 7.0. To run tests:

```bash
$ ./vendor/bin phpunit
```

Any questions, remarks, bugs? Please mail us.
- technical questions: <mailto:tech@webservices.nl>
- support questions: <mailto:support.webservices.nl>