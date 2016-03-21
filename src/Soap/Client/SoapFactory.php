<?php

namespace WebservicesNl\Soap\Client;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

use WebservicesNl\Common\Client\ClientFactoryInterface;
use WebservicesNl\Common\Endpoint\Manager as EndpointManager;
use WebservicesNl\Common\Exception\Client\Input\InvalidException;
use WebservicesNl\Common\Exception\Client\InputException;
use WebservicesNl\Common\Exception\Server\NoServerAvailableException;
use WebservicesNl\Soap\Client\Config\ConfigFactory;

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
     *
     * @throws InputException
     */
    public function __construct($platform, LoggerInterface $logger = null)
    {
        if (!ConfigFactory::hasConfig($platform)) {
            throw new InputException($platform . ' is not a valid platform');
        }
        $this->platform = ucfirst($platform);
        $this->logger = $logger;
    }

    /**
     * @param string               $platform
     * @param LoggerInterface|null $logger
     *
     * @throws InputException
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
     * @throws \InvalidArgumentException
     * @return SoapClient
     */
    public function create(array $settings = [])
    {
        $soapSettings = SoapSettings::loadFromArray($settings += self::$defaultSettings);
        $platformConfig = ConfigFactory::config($this->platform, $soapSettings);

        // add endpoint manager
        $manager = new EndpointManager($settings);
        foreach ($platformConfig['endPoints'] as $endPoint) {
            $manager->createEndpoint($endPoint);
        }

        $soapClient = new SoapClient($soapSettings, $manager, $this->getLogger());
        $soapHeaders = array_merge($platformConfig['soapHeaders'], $settings['soapHeaders']);
        $soapClient->__setSoapHeaders($soapHeaders);

        // add a curl client
        if ($settings['useHttpClient'] === true) {
            $client = $this->createCurlClient((string)$manager->getActiveEndpoint()->getUri());
            $soapClient->setClient($client);
        }

        if ($this->hasLogger() === true) {
            $this->getLogger()->info("Creating a SoapClient for platform $this->platform");
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
        $stack = null;
        if ($this->getLogger() instanceof LoggerInterface) {
            $stack = HandlerStack::create();
            $stack->push(
                Middleware::log(
                    $this->getLogger(),
                    new MessageFormatter('{req_body} - {res_body}')
                )
            );
        }

        return new Client(['base_url' => $wsdl, 'handler' => $stack]);
    }
}
