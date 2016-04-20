<?php

namespace WebservicesNl\Soap\Client;

use GuzzleHttp\Client as httpClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use WebservicesNl\Common\Client\ClientInterface;
use WebservicesNl\Common\Endpoint\Manager;
use WebservicesNl\Common\Exception\Server\NoServerAvailableException;
use WebservicesNl\Soap\Exception\ConverterInterface;

/**
 * Class SoapClient.
 *
 * Extends the native PHP SoapClient. Adds PSR7 Client (Guzzle) for making the calls for better timeout management.
 * Also optional loggerInterface (middleware client) helps with tracing and debugging calls.
 */
class SoapClient extends \SoapClient implements ClientInterface
{
    use LoggerAwareTrait;

    const PROTOCOL = 'soap';

    /**
     * Content types for SOAP versions.
     *
     * @var array(string=>string)
     */
    protected static $versionToContentTypeMap = [
        SOAP_1_1 => 'text/xml; charset=utf-8',
        SOAP_1_2 => 'application/soap+xml; charset=utf-8',
    ];

    /**
     * Guzzle Client for the SOAP calls.
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var Manager
     */
    private $manager;

    /**
     * Soap settings.
     *
     * @var SoapSettings;
     */
    private $settings;

    /**
     * @var ConverterInterface
     */
    private $converter;

    /**
     * SoapClient constructor.
     *
     * @param SoapSettings $settings
     * @param Manager      $manager
     * @param              $client
     *
     * @throws NoServerAvailableException
     * @throws \InvalidArgumentException
     */
    public function __construct(SoapSettings $settings, Manager $manager, $client)
    {
        $this->settings = $settings;
        $this->manager = $manager;
        $this->httpClient = $client;

        // throws an Exception when no endpoint is met
        $active = $this->manager->getActiveEndpoint();
        $this->log('Initial endpoint is ' . (string)$active->getUri(), LogLevel::INFO);

        // initiate the native PHP SoapClient for fetching all the WSDL stuff
        parent::__construct((string)$active->getUri()->withQuery('wsdl'), $this->settings->toArray());
    }

    /**
     * Prepares the soapCall.
     *
     * @param string     $function_name
     * @param array      $arguments
     * @param array      $options
     * @param array      $input_headers
     * @param array|null $output_headers
     *
     * @return mixed
     * @throws \Exception|\SoapFault
     */
    public function soapCall(
        $function_name,
        array $arguments = [],
        array $options = [],
        array $input_headers = [],
        &$output_headers = null
    ) {
        $this->log('Called:' . $function_name, LogLevel::INFO, ['arguments' => $arguments]);

        try {
            return parent::__soapCall($function_name, $arguments, $options, $input_headers, $output_headers);
        } catch (\SoapFault $fault) {
            if ($this->getConverter() !== null) {
                throw $this->getConverter()->convertToException($fault);
            }
            throw $fault;
        }
    }

    /**
     * Triggers the SOAP request over HTTP.
     * Sent request by cURL instead of native SOAP request.
     *
     * @param string $request
     * @param string $location
     * @param string $action
     *
     * @return string The XML SOAP response.
     * @throws NoServerAvailableException
     * @throws \SoapFault
     * @throws \Exception
     */
    public function __doRequest($request, $location, $action)
    {
        $active = $this->manager->getActiveEndpoint();
        try {
            $response = $this->doHttpRequest($request, (string)$active->getUri(), $action);
            $this->manager->updateLastConnected();

            return $response;
        } catch (ConnectException $exception) {
            $active->setStatus('error');
            $this->log('Endpoint is not responding', 'error', ['endpoint' => $active]);

            return $this->__doRequest($request, $location, $action);
        }
    }

    /**
     * Http version of doRequest.
     *
     * @param mixed  $requestBody
     * @param string $location
     * @param string $action
     *
     * @return string
     * @throws \SoapFault
     */
    private function doHttpRequest($requestBody, $location, $action)
    {
        // get soap details for request
        $headers = $this->createHeaders($action);
        $method = self::determineMethod($requestBody);

        // build the request
        try {
            $requestObj = new Request($method, $location, $headers, $requestBody);
            $response = $this->httpClient->send($requestObj);

            if ($response->getStatusCode() > 399 && simplexml_load_string((string)$response->getBody()) === false) {
                throw new \SoapFault('Server', 'Invalid SoapResponse', 'Server', '');
            }

            return (string)$response->getBody();
        } catch (\InvalidArgumentException $e) {
            throw new \SoapFault('Client', 'Invalid SoapRequest', 'Client.Input', '');
        }
    }

    /**
     * Determine the SOAPHeaders for given version.
     *
     * @param string $action
     *
     * @return array
     */
    private function createHeaders($action)
    {
        $headers = ['Content-Type' => self::$versionToContentTypeMap[$this->settings->getSoapVersion()]];
        if ($this->settings->getSoapVersion() === SOAP_1_1) {
            $headers['SOAPAction'] = $action;
        }

        return $headers;
    }

    /**
     * Determines methods.
     * For Soap it's either GET or POST.
     *
     * @param mixed $request
     *
     * @return string
     */
    private static function determineMethod($request)
    {
        return ($request === null || (is_string($request) && trim($request) === '')) ? 'GET' : 'POST';
    }

    /**
     * Log message.
     *
     * @param string $message
     * @param int    $level
     * @param array  $context
     */
    public function log($message, $level, array $context = [])
    {
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * Return this connector over which a connection is established.
     *
     * @return string
     */
    public function getProtocolName()
    {
        return static::PROTOCOL;
    }

    /**
     * Proxy function to SoapCall
     *
     * @return mixed
     */
    public function call()
    {
        //
    }

    /**
     * @return ConverterInterface
     */
    public function getConverter()
    {
        return $this->converter;
    }

    /**
     * @param ConverterInterface $converter
     */
    public function setConverter($converter)
    {
        $this->converter = $converter;
    }

    /**
     * @return httpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }
}
