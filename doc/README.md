# Webservices Platform Connector

The goal of this project is to create a easy to implement connector to the Webservices platform. 
This connector currently support only SOAP to connect to Webservices.nl, but in the future will support various 
protocols that Webservices.nl offers to connect over. Like REST, XML-RPC etc.

Current implementation features a proxy class, that independently of protocol, can be used for type hinted connector.

## Prerequisites:
- PHP: 5.4+
- composer (https://getcomposer.org)

## Install
Please composer to install this library.

```bash 
composer require webservices-nl/platform-connector
```

## Usage

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
This client has been tested on PHP 5.5, 5.6 and 7.0

```bash

composer install --dev 
phpunit

```

Any questions, remarks, bugs? Please mail us.
- technical questions: <mailto:tech@webservices.nl>
- support questions: <mailto:support.webservices.nl>