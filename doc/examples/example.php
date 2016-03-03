<?php

use Webservicesnl\Connector\ConnectorFactory;

$client = ConnectorFactory::create('webservices', 'soap', ['username' => 'johndoe', 'password' => 'topsecret']);

try {

    $address = $client->addressReeksPostcodeSearch('2012es30');
    print_r($address);

    // get the state of the client and store it
    $state = $client->__getServerState();
} catch (WebservicesNlClientAuthenticationException $e) {
    print('Your Webservices.nl username or password is invalid.');
} catch (WebservicesNlClientAuthorizationException $e) {
    print('Your Webservices.nl user does not have sufficient rights to access the requested service.');
} catch (WebservicesNlClientPaymentException $e) {
    print('Your Webservices.nl account does not have sufficient balance.');
} catch (WebservicesNlClientInputException $e) {
    print('An error has occurred due to a problem with the input parameters.');
} catch (WebservicesNlServerDataNotFoundException $e) {
    print('The requested data isn\'t available (for example, the requested address does not exist).');
} catch (WebservicesNlServerUnavailableException $e) {
    print('A server side error occurred that causes the service to be unavailable.');
} catch (WebservicesNlNoServerAvailableException $e) {
    print('Service timed out on all servers.');
}
