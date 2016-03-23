<?php

namespace WebservicesNl\Soap\Client;

use GuzzleHttp\Client as httpClient;
use GuzzleHttp\Psr7\Request;
use Http\Client\Exception as ClientException;
use Http\Message\MessageFactory;
use Psr\Log\LoggerInterface;
use WebservicesNl\Common\Endpoint\Endpoint;
use WebservicesNl\Common\Endpoint\Manager;
use WebservicesNl\Common\Exception\Client\InputException;
use WebservicesNl\Common\Exception\Server\NoServerAvailableException;
use WebservicesNl\Soap\Exception\Converter;
use WebservicesNl\Soap\Exception\SoapFault;

/**
 * Class AbstractSoapClient.
 */
class SoapClient extends \SoapClient
{
    const NO_XML_FAULT_STRING = 'looks like we got no XML document';

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
     * Guzzle Client for the SOAP calls. (optional).
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * LoggerInterface (optional).
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Manager
     */
    private $manager;

    /**
     * Configurable Soap settings.
     *
     * @var SoapSettings;
     */
    private $settings;

    /**
     * SoapClient constructor.
     *
     * @param SoapSettings    $settings
     * @param Manager         $manager
     * @param LoggerInterface $logger
     *
     * @throws NoServerAvailableException
     */
    public function __construct(SoapSettings $settings, Manager $manager, LoggerInterface $logger = null)
    {
        $this->settings = $settings;
        $this->manager = $manager;
        $this->logger = $logger;

        // throws an Exception when no endpoint is met
        $active = $this->manager->getActiveEndpoint();

        // initiate the native PHP SoapClient for fetching all the WSDL stuff
        parent::__construct((string) $active->getUri(), $this->settings->toArray());
    }

    /**
     * Set a HTTP Client.
     *
     * @param HttpClient $client
     */
    public function setClient(HttpClient $client)
    {
        $this->httpClient = $client;
    }

    /**
     * Prepares the actual soapCall.
     *
     * @param string     $function_name
     * @param array      $arguments
     * @param array      $options
     * @param array      $input_headers
     * @param array|null $output_headers
     *
     * @return mixed
     *
     * @throws NoServerAvailableException
     */
    public function soapCall(
        $function_name,
        array $arguments = [],
        array $options = [],
        array $input_headers = [],
        &$output_headers = null
    ) {
        $continue = true;
        $response = null;
        do {
            try {
                $response = parent::__soapCall($function_name, $arguments, $options, $input_headers, $output_headers);
                // if a valid response has been received, stop looping
                $continue = false;
            } catch (\Exception $exception) {
                //@todo handle exception (check timeout, change endpoint, reset client, etc)
                $endpoint = $this->manager->getActiveEndpoint();
                $endpoint->setStatus(Endpoint::STATUS_ERROR);
            }
        } while ($continue);

        return $response;
    }

    /**
     * Triggers the SOAP request.
     * When http client is present, sent request by cURL instead of native SOAP request.
     *
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int    $version
     * @param bool   $oneWay
     *
     * @return string|void
     *
     * @throws \Exception
     */
    public function __doRequest($request, $location, $action, $version = SOAP_1_1, $oneWay = false)
    {
        try {
            if ($this->hasClient() === true) {
                $response = $this->doHttpRequest(
                    $request,
                    $location,
                    $action,
                    $this->settings->getSoapVersion()
                );
            } else {
                $response = parent::__doRequest(
                    $request,
                    $location,
                    $action,
                    $this->settings->getSoapVersion(),
                    $oneWay
                );
                $this->__last_request = $request;
            }

            return $response;
        } catch (SoapFault $fault) {
            return Converter::build()->convertToException($fault);
        }
    }

    /**
     * checks if a HTTP client is present.
     *
     * @return bool
     */
    public function hasClient()
    {
        return $this->httpClient instanceof HttpClient;
    }

    /**
     * Http version of doRequest.
     *
     * @param mixed  $requestBody
     * @param string $location
     * @param string $action
     * @param string $version
     *
     * @return string
     *
     * @throws InputException
     * @throws \SoapFault
     */
    private function doHttpRequest($requestBody, $location, $action, $version)
    {
        $method = self::determineMethod($requestBody);
        $contentType = self::getContentTypeForVersion($version);
        $headers = [sprintf('Content-Type: %s; action="%s"', $contentType, $action)];

        try {
            // build the request
            $requestObj = new Request($method, $location, $headers, $requestBody);
            $response = $this->httpClient->send($requestObj);
        } catch (ClientException $exception) {
            throw new \SoapFault('Sender', $exception->getMessage());
        } catch (\Exception $exception) {
            throw new \SoapFault('Server', $exception->getMessage());
        }

        return (string) $response->getBody();
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
     * Get content type for given SOAP version.
     *
     * @param string $version SOAP version constant SOAP_1_1|SOAP_1_2
     *
     * @throws InputException
     *
     * @return string
     */
    private static function getContentTypeForVersion($version)
    {
        if (!in_array($version, [SOAP_1_1, SOAP_1_2], true)) {
            throw new InputException("The 'version' argument has to be either 'SOAP_1_1' or 'SOAP_1_2'!");
        }

        return self::$versionToContentTypeMap[$version];
    }

    /**
     * We don't allow cookies.
     *
     * @param string $name
     * @param null   $value
     *
     * @throws \BadMethodCallException
     */
    public function __setCookie($name, $value = null)
    {
        throw new \BadMethodCallException('No more cookies for you!');
    }
}
