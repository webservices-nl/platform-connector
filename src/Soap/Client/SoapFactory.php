<?php

namespace Webservicesnl\Soap\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

use Webservicesnl\Common\Client\ClientFactoryInterface;
use Webservicesnl\Common\Endpoint\Manager as EndpointManager;
use Webservicesnl\Common\Exception\Client\Input\InvalidException;
use Webservicesnl\Common\Exception\Client\InputException;
use Webservicesnl\Common\Exception\Server\NoServerAvailableException;
use Webservicesnl\Soap\Client\Config\ConfigFactory;

/**
 * Class SoapFactory.
 *
 * Create a SoapClient for a given platform (mainly webservices)
 */
class SoapFactory implements ClientFactoryInterface
{
    use LoggerAwareTrait;

    /**
     * Default settings.
     *
     * @var array
     */
    protected static $defaultSettings = [
        'endpointTimeout' => 60,
        'password'        => null,
        'platform'        => null,
        'protocol'        => 'soap',
        'soapHeaders'     => [],
        'username'        => null,
        'useHttpClient'   => true,
    ];

    /**
     * @var string
     */
    protected $platform;

    /**
     * SoapBuilder constructor.
     *
     * @param string               $platform
     * @param LoggerInterface|null $logger
     */
    public function __construct($platform, LoggerInterface $logger = null)
    {
        $this->platform = $platform;
        if ($logger) {
            $this->setLogger($logger);
        }
    }

    /**
     * @param string               $platform
     * @param LoggerInterface|null $logger
     *
     * @return static
     */
    public static function build($platform, LoggerInterface $logger = null)
    {
        return new static($platform, $logger);
    }

    /**
     * Build SoapClient.
     *
     * @param array $settings
     *
     * @throws InputException
     *
     * @throws NoServerAvailableException
     * @throws InvalidException
     * @return SoapClient
     */
    public function create(array $settings = [])
    {
        $settings = $settings + self::$defaultSettings;
        $soapSettings = SoapSettings::loadFromArray($settings);
        $config = ConfigFactory::config($this->platform, $soapSettings);

        // add endpoint manager
        $manager = new EndpointManager($settings);
        foreach ($config['endPoints'] as $endPoint) {
            $manager->createEndpoint($endPoint);
        }

        $soapClient = new SoapClient($soapSettings, $manager, $this->getLogger());
        $soapHeaders = array_merge($config['soapHeaders'], $settings['soapHeaders']);
        $soapClient->__setSoapHeaders($soapHeaders);

        // add a Curl client
        if ($settings['useHttpClient'] === true) {
            $client = $this->createCurlClient($manager->getActiveEndpoint()->getUrl());
            $soapClient->setClient($client);
        }

        if ($this->hasLogger() === true) {
            $this->getLogger()->info("Creating a SoapClient for platform '$this->platform'", $settings);
            $this->getLogger()->debug('Created EndpointManager', ['endpoint' => print_r($manager, true)]);
            $this->getLogger()->debug('Created SoapClient', ['soapclient' => print_r($soapClient, true)]);
        }

        return $soapClient;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return bool
     */
    public function hasLogger()
    {
        return $this->getLogger() instanceof LoggerInterface;
    }

    /**
     * @param string $wsdl
     *
     * @return Client
     */
    private function createCurlClient($wsdl)
    {
        $client = new Client(['base_url' => $wsdl]);
        if ($this->getLogger() instanceof LoggerInterface) {
            $client->getEmitter()->attach(new LogSubscriber($this->getLogger()));
        }

        return $client;
    }
}
