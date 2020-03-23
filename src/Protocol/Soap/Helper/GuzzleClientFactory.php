<?php

namespace WebservicesNl\Protocol\Soap\Helper;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use WebservicesNl\Platform\PlatformConfigInterface;

/**
 * HttpClientFactory.
 *
 * Helper class the Webservices connector generator with instantiating a PSR-7 curl client.
 */
class GuzzleClientFactory
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
     */
    public function __construct(PlatformConfigInterface $platform)
    {
        $this->platform = $platform;
    }

    /**
     * Create a static instance (LSB) of HttpClientFactory.
     *
     * @param PlatformConfigInterface $platform
     *
     * @return GuzzleClientFactory
     */
    public static function build(PlatformConfigInterface $platform)
    {
        return new static($platform);
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
        $settings += $this->platform->toArray();

        if ($this->getLogger() instanceof LoggerInterface) {
            $stack = HandlerStack::create();
            $stack->push(Middleware::log($this->getLogger(), new MessageFormatter('{request} - {response}')));
        }

        return new Client(
            [
                'base_url' => (string) $settings['url'],
                'handler' => $stack,
                'exceptions' => false,
                'timeout' => (float) $settings['responseTimeout'],
                'connection_timeout' => (float) $settings['connectionTimeout'],
                'headers' => [
                    'User-Agent' => $settings['userAgent'],
                ],
            ]
        );
    }

    /**
     * Returns this LoggerInterface.
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
