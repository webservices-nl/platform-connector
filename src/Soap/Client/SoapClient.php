<?php

namespace Webservicesnl\Soap\Client;

use Http\Client\Exception as ClientException;
use Http\Client\HttpClient;
use Http\Client\Plugin\CookiePlugin;
use Http\Client\Plugin\PluginClient;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\NotFoundException;
use Http\Message\Cookie;
use Http\Message\CookieJar;
use Http\Message\MessageFactory;
use Psr\Log\LoggerInterface;
use Webservicesnl\Endpoint\Manager;
use Webservicesnl\Exception\Server\NoServerAvailableException;
use Webservicesnl\Soap\Exception\SoapFault;

/**
 * Class AbstractSoapClient.
 */
class SoapClient extends \SoapClient
{
    const NO_XML_FAULT_STRING = 'looks like we got no XML document';
    const RETRY_MINUTES = 60;

    // @todo determine content type correctly
    const CONTENT_TYPE = 'application/soap+xml';

    /**
     * @var array|Cookie[]
     */
    private $cookies = [];
    /**
     * @var bool
     */
    private $customConfigValidated = false;
    /**
     * Guzzle Client for the SOAP calls.
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
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var PluginClient
     */
    private $pluginClient;

    /**
     * Configurable Soap settings.
     *
     * @var SoapSettings;
     */
    private $settings;

    /**
     * SoapClient constructor.
     *
     * @param SoapSettings $settings
     * @param Manager $manager
     * @param LoggerInterface $logger
     */
    public function __construct(
        SoapSettings $settings,
        Manager $manager,
        LoggerInterface $logger = null
    ) {
        $this->settings = $settings;
        $this->manager = $manager;
        $this->logger = $logger;

        // throws an Exception when no endpoint is met
        $active = $this->manager->getActiveEndpoint();

        // initiate the PHP SoapClient
        parent::__construct($active->getUrl(), $this->settings->toArray());

    }

    /**
     * @param HttpClient $client
     */
    public function setClient(HttpClient $client)
    {
        $this->httpClient = $client;
    }

    /**
     * @param string $function_name
     * @param array $arguments
     * @param array $options
     * @param array $input_headers
     * @param null $output_headers
     *
     * @return mixed
     *
     * @throws NoServerAvailableException
     */
    public function soapCall(
        $function_name,
        $arguments = [],
        $options = [],
        $input_headers = [],
        &$output_headers
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
            }
        } while ($continue);

        return $response;
    }

    /**
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int $version
     * @param bool $oneWay
     *
     * @return string|void
     *
     * @throws \Exception
     */
    public function __doRequest($request, $location, $action, $version = SOAP_1_1, $oneWay = false)
    {
        try {
            if ($this->hasClient() && $this->validateCustomClient()) {
                return $this->doHttpRequest($request, $location, $action, $version);
            }

            return parent::__doRequest($request, $location, $action, $this->settings->getSoapVersion(), $oneWay);
        } catch (SoapFault $fault) {
            // @todo convert to specific exception
            throw new \Exception('please change this.');
        }
    }

    /**
     * @return bool
     */
    public function hasClient()
    {
        return $this->httpClient instanceof HttpClient;
    }

    /**
     * @return bool
     *
     * @throws \RuntimeException
     */
    private function validateCustomClient()
    {
        if ($this->customConfigValidated === true || $this->hasClient() === false) {
            $this->customConfigValidated = true;

            return true;
        }

        try {
            // look for a default MessageFactory if none has been provided
            if ($this->getMessageFactory() instanceof MessageFactory === false) {
                $this->messageFactory = MessageFactoryDiscovery::find();
            }
            if ($this->getMessageFactory() === null) {
                throw new \RuntimeException('Please provide a valid MessageFactory.');
            }
        } catch (NotFoundException $exception) {
            throw new \RuntimeException('Please provide a valid MessageFactory.');
        }

        $this->customConfigValidated = true;

        return true;
    }

    /**
     * @param mixed $request
     * @param string $location
     * @param string $action
     * @param string $version
     *
     * @return string
     *
     * @throws SoapFault
     */
    private function doHttpRequest($request, $location, $action, $version)
    {
        $method = self::determineMethod($request);

        $headers = [sprintf('Content-Type: %s; action="%s"', self::CONTENT_TYPE, $action)];
        if (SOAP_1_1 === $version) {
            $headers = [
                'Content-Type:' . self::CONTENT_TYPE,
                sprintf('SOAPAction: "%s"', $action)
            ];
        }

        // build the request
        $requestObj = $this->getMessageFactory()->createRequest(
            $method,
            $location,
            $headers,
            $request
        );

        try {
            $response = $this->pluginClient->sendRequest($requestObj);
        } catch (ClientException $exception) {
            throw new SoapFault($exception->getMessage());
        } catch (\Exception $exception) {
            throw new SoapFault($exception->getMessage());
        }

        return (string)$response->getBody();
    }

    /**
     * @return MessageFactory
     */
    public function getMessageFactory()
    {
        return $this->messageFactory;
    }

    /**
     * @param MessageFactory $messageFactory
     */
    public function setMessageFactory(MessageFactory $messageFactory)
    {
        $this->messageFactory = $messageFactory;
    }

    /**
     * @param mixed $request
     * @return string
     */
    private static function determineMethod($request)
    {
        if ($request === null || (is_string($request) && trim($request) === '')) {
            return 'GET';
        }
        return 'POST';
    }

    /**
     * @throws \RuntimeException
     */
    private function initClient()
    {
        // initialize the necessary plugins

        // set the cookie plugin
        $cookieJar = new CookieJar();
        $cookieJar->setCookies($this->cookies);
        $cookiePlugin = new CookiePlugin($cookieJar);


        $this->pluginClient = new PluginClient($this->httpClient, [$cookiePlugin]);
    }
}

