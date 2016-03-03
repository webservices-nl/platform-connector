<?php

namespace Webservicesnl\Soap\Client;

use GuzzleHttp\Client as CurlClient;
use Psr\Log\LoggerInterface;
use Webservicesnl\Endpoint\Manager;

/**
 * Class AbstractSoapClient.
 */
class SoapClient extends \SoapClient
{
    const NO_XML_FAULTSTRING = 'looks like we got no XML document';
    const RETRY_MINUTES = 60;

    /**
     * Guzzle Client for the SOAP calls.
     *
     * @var CurlClient
     */
    private $curlClient;

    /**
     * LoggerInterface (optional).
     *
     * @var LoggerInterface
     */
    protected $logger;

//    /**
//     * @var array
//     */
//    private $cookies = [];

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
     */
    public function __construct(SoapSettings $settings, Manager $manager, LoggerInterface $logger = null)
    {
        $this->settings = $settings;
        $this->manager = $manager;
        $this->logger = $logger;

        // do some initialization
        $this->init();
    }

    /**
     *  Setup the native soapClient bit.
     */
    private function init()
    {
        // throws an Exception when no endpoint is met
        $active = $this->manager->getActiveEndpoint();

        // initiate the PHP SoapClient
        parent::__construct($active->getUrl(), $this->settings->toArray());
    }

    /**
     * @param CurlClient $client
     */
    public function setClient(CurlClient $client)
    {
        $this->curlClient = $client;
    }

    /**
     * @param string $function_name
     * @param array  $arguments
     * @param array  $options
     * @param array  $input_headers
     * @param null   $output_headers
     *
     * @return mixed
     * @throws WebservicesNlServerException|WebservicesNlServerUnavailableException|WebservicesNlServerUnavailableInternalErrorException|WebservicesNlServerUnavailableTemporaryException
     */
    public function soapCall($function_name, $arguments = [], $options = [], $input_headers = [], &$output_headers = null)
    {
//        try {
//            // try all servers until we have a result
//            while (1) {
//                try {
//                    $response = $this->wsCallServer($function_name, $arguments, $options, $input_headers, $output_headers);
//                    $this->ws_setServerAvailable($this->current_server);
//
//                    return $response;
//                } catch (SoapFault $s) {
//                    $this->debug_log .= "\t" . $s->getMessage() . "\n";
//
//                    if ($this->ws_isServerTimeoutFault($s)) {
//                        // a timeout occurred, try the next server
//                        $this->ws_setServerUnavailable($this->current_server);
//                        $this->ws_switchServer();
//                    } else {
//                        throw($s);
//                    }
//                }
//            }
//        } catch (Exception $e) {
//            throw($this->ws_convertException($e));
//        }
    }

    /**
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int    $version
     * @param bool   $oneWay
     *
     * @return string|void
     */
    public function __doRequest($request, $location, $action, $version = SOAP_1_1, $oneWay = false)
    {
        // else get next location
        if ($this->hasClient()) {
            return $this->doHttpRequest($request, $location, $action);
        } else {
            return parent::__doRequest($request, $location, $action, $this->settings->getSoapVersion(), $oneWay);
        }
    }

    /**
     * @return bool
     */
    public function hasClient()
    {
        return ($this->curlClient instanceof CurlClient);
    }

    private function doHttpRequest($request, $location, $action)
    {
        var_dump($request, $location, $action);
        die;
//        // HTTP headers
//        $soapVersion = $soapRequest->getVersion();
//        $soapAction = $soapRequest->getAction();
//        if (SOAP_1_1 == $soapVersion) {
//            $headers = array(
//                'Content-Type:' . $soapRequest->getContentType(),
//                'SOAPAction: "' . $soapAction . '"',
//            );
//        } else {
//            $headers = array(
//                'Content-Type:' . $soapRequest->getContentType() . '; action="' . $soapAction . '"',
//            );
//        }
//
//        $location = $soapRequest->getLocation();
//        $content = $soapRequest->getContent();
//        $headers = $this->filterRequestHeaders($soapRequest, $headers);
//        $options = $this->filterRequestOptions($soapRequest);
//        // execute HTTP request with cURL
//        $responseSuccessfull = $this->curl->exec(
//            $location,
//            $content,
//            $headers,
//            $options
//        );
//        // tracing enabled: store last request header and body
//        if ($this->tracingEnabled === true) {
//            $this->lastRequestHeaders = $this->curl->getRequestHeaders();
//            $this->lastRequest = $soapRequest->getContent();
//        }
//
//        // in case of an error while making the http request throw a soapFault
//        if ($responseSuccessfull === false) {
//            // get error message from curl
//            $faultstring = $this->curl->getErrorMessage();
//            throw new \SoapFault('HTTP', $faultstring);
//        }
//
//        // tracing enabled: store last response header and body
//        if ($this->tracingEnabled === true) {
//            $this->lastResponseHeaders = $this->curl->getResponseHeaders();
//            $this->lastResponse = $this->curl->getResponseBody();
//        }
//        // wrap response data in SoapResponse object
//        $soapResponse = SoapResponse::create(
//            $this->curl->getResponseBody(),
//            $soapRequest->getLocation(),
//            $soapRequest->getAction(),
//            $soapRequest->getVersion(),
//            $this->client->getResponseContentType()
//        );
//
//        return $soapResponse;
    }
}
