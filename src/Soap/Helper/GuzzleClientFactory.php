<?php

namespace WebservicesNl\Soap\Helper;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use WebservicesNl\Common\Client\ClientFactoryInterface;
use WebservicesNl\Connector\Platform\PlatformConfigInterface;

/**
 * HttpClientFactory.
 *
 * Helper class the Webservices connector generator with instantiating a PSR-7 curl client.
 */
class GuzzleClientFactory implements ClientFactoryInterface
{
    use LoggerAwareTrait;

    /**
     * @var PlatformConfigInterface
     */
    private $platform;

    /**
     * HttpClientFactory constructor.
     *
     * @param PlatformConfigInterface $platform
     * @param LoggerInterface|null    $logger
     */
    public function __construct(PlatformConfigInterface $platform, LoggerInterface $logger = null)
    {
        $this->platform = $platform;
        $this->logger = $logger;
    }

    /**
     * Create a static instance (LSB) of HttpClientFactory.
     *
     * @param PlatformConfigInterface $platform
     * @param LoggerInterface         $logger
     *
     * @return GuzzleClientFactory
     */
    public static function build(PlatformConfigInterface $platform, LoggerInterface $logger = null)
    {
        return new static($platform, $logger);
    }

    /**
     * Create and configure a http curl client.
     *
     * @param array $settings additional settings
     *
     * @return Client
     */
    public function create(array $settings = [])
    {
        $stack = null;
        $settings = $this->platform->toArray() + $settings;

        if ($this->getLogger() instanceof LoggerInterface) {
            $stack = HandlerStack::create();
            $stack->push(Middleware::log($this->getLogger(), new MessageFormatter('{request} - {response}')));
        }

        return new Client(
            [
                'base_url'           => (string)$settings['url'],
                'handler'            => $stack,
                'exceptions'         => (bool)$settings['exceptions'],
                'timeout'            => (float)$settings['responseTimeout'],
                'connection_timeout' => (float)$settings['connectionTimeout'],
            ]
        );
    }

    /**
     * Returns this LoggerInterface
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
