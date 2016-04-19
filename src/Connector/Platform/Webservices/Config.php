<?php

namespace WebservicesNl\Connector\Platform\Webservices;

use WebservicesNl\Common\Exception\Client\InputException;
use WebservicesNl\Connector\Platform\AbstractConfig;

/**
 * Class WebservicesConfig
 * Webservice config file for connecting to the Webservices Platform
 */
class Config extends AbstractConfig
{
    const DEFAULT_RESPONSE_TIMEOUT = 20;
    const PLATFORM_NAME = 'Webservices';
    const RETRY_MINUTES = 60;

    /**
     * Connection timeout in seconds.
     *
     * @var int
     */
    private $connectionTimeout = 5;

    /**
     * Webservices provided password (mandatory).
     *
     * @var string
     */
    private $password;

    /**
     * Response timeout in seconds. (Webservices specific).
     *
     * @var int
     */
    private $responseTimeout = self::DEFAULT_RESPONSE_TIMEOUT;

    /**
     * Threshold when to try server again after failure (in minutes).
     *
     * @var int
     */
    private $retryMinutes = self::RETRY_MINUTES;

    /**
     * Webservices provided username (mandatory).
     *
     * @var string
     */
    private $userName;

    /**
     * WebservicesConfig constructor.
     *
     * @param array $settings
     *
     * @return Config
     * @throws InputException
     */
    public function loadFromArray(array $settings = [])
    {
        $settings = array_filter($settings);
        if (!array_key_exists('username', $settings) || !array_key_exists('password', $settings)) {
            throw new InputException('Not all mandatory config credentials are set');
        }
        $this->password = $settings['password'];
        $this->userName = $settings['username'];

        return $this;
    }


    /**
     * Get the connection timeout.
     *
     * @return int
     */
    public function getConnectionTimeout()
    {
        return $this->connectionTimeout;
    }

    /**
     * Set connection timeout.
     *
     * @param int $connectionTimeout
     */
    public function setConnectionTimeout($connectionTimeout)
    {
        $this->connectionTimeout = $connectionTimeout;
    }

    /**
     * Get the password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the password.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Return the timeout.
     *
     * @return int
     */
    public function getResponseTimeout()
    {
        return $this->responseTimeout;
    }

    /**
     * Set response timeout.
     *
     * @param int $responseTimeout
     */
    public function setResponseTimeout($responseTimeout)
    {
        $this->responseTimeout = $responseTimeout;
    }

    /**
     * Return the retry minutes
     *
     * @return int
     */
    public function getRetryMinutes()
    {
        return $this->retryMinutes;
    }

    /**
     * set retryMinutes.
     *
     * @param int $retryMinutes
     */
    public function setRetryMinutes($retryMinutes)
    {
        $this->retryMinutes = $retryMinutes;
    }

    /**
     * Return the username.
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set username.
     *
     * @param string $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function toArray()
    {
        return array_filter(
            [
                'connectionTimoout' => $this->getConnectionTimeout(),
                'password'          => $this->getPassword(),
                'responseTimeout'   => $this->getResponseTimeout(),
                'retryMinutes'      => $this->getRetryMinutes(),
                'userName'          => $this->getUserName(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getConnectorName()
    {
        return __NAMESPACE__ . '\Connector';
    }
}
