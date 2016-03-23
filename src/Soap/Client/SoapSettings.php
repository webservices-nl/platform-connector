<?php

namespace WebservicesNl\Soap\Client;

use WebservicesNl\Common\Exception\Client\InputException;

/**
 * Class SoapSettings.
 *
 * Class for all the soapClient settings
 *
 * @link http://php.net/manual/en/soapclient.soapclient.php
 *
 * For HTTP authentication, the login and password options can be used to supply credentials.
 * For making an HTTP connection through a proxy server, the options
 *  - proxy_host
 *  - proxy_port
 *  - proxy_login
 *  - proxy_password
 *
 *  For HTTPS client certificate authentication use local_cert and passphrase options
 *
 */
class SoapSettings
{
    const DEFAULT_CONNECTION_TIMEOUT = 5;
    const DEFAULT_RESPONSE_TIMEOUT = 20;
    const USER_AGENT = 'WebservicesNlSoapClient/PHP/2.0';

    /**
     * An authentication may be supplied in the authentication option.
     * The authentication method may be either SOAP_AUTHENTICATION_BASIC (default) or SOAP_AUTHENTICATION_DIGEST.
     *
     * @var int
     */
    private $authentication = SOAP_AUTHENTICATION_BASIC;

    /**
     * The cache_wsdl option is one of WSDL_CACHE_NONE, WSDL_CACHE_DISK, WSDL_CACHE_MEMORY or WSDL_CACHE_BOTH.
     *
     * @var int
     */
    private $cacheWsdl = WSDL_CACHE_NONE;

    /**
     * @var array | null
     */
    private $classMap;

    /**
     * The compression option allows to use compression of HTTP SOAP requests and responses.
     *
     * @var null
     */
    private $compression = SOAP_COMPRESSION_ACCEPT;

    /**
     * The connection_timeout option defines a timeout in seconds for the connection to the SOAP service.
     *
     * This option does not define a timeout for services with slow responses. To limit the time to wait for calls to
     * finish the default_socket_timeout setting is available.
     *
     * @var int
     */
    private $connectionTimeout = self::DEFAULT_CONNECTION_TIMEOUT;

    /**
     * The stream_context option is a resource for context.
     *
     * @var resource
     */
    private $context;

    /**
     * The encoding option defines internal character encoding.
     * This option does not change the encoding of SOAP requests (it is always utf-8), but converts strings into it.
     *
     * @var
     */
    private $encoding = 'UTF-8';

    /**
     * The exceptions option is a boolean value defining whether soap errors throw exceptions of type SoapFault.
     *
     * @var bool
     */
    private $exceptions = true;

    /**
     * The features option is a bitmask of SOAP_SINGLE_ELEMENT_ARRAYS, SOAP_USE_XSI_ARRAY_TYPE, SOAP_WAIT_ONE_WAY_CALLS.
     *
     * @var int
     */
    private $features = SOAP_SINGLE_ELEMENT_ARRAYS;

    /**
     * Optional value defining whether to send the Connection: 'Keep-Alive' or Connection: 'close'.
     *
     * @var bool
     */
    private $keepAlive = true;

    /**
     * path to cert_key.pem.
     *
     * @var string
     */
    private $localCert;

    /**
     * Passphrase for localCert pem.
     *
     * @var string
     */
    private $passphrase;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $proxyHost;

    /**
     * @var string
     */
    private $proxyLogin;
    /**
     * @var string
     */
    private $proxyPassword;

    /**
     * @var string
     */
    private $proxyPort;

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
    private $retryMinutes = 60;

    /**
     * All possible SOAP SSL methods.
     *
     * @var array
     */
    public static $sslMethods =
        [
            SOAP_SSL_METHOD_TLS,
            SOAP_SSL_METHOD_SSLv2,
            SOAP_SSL_METHOD_SSLv3,
            SOAP_SSL_METHOD_SSLv23,
        ];

    /**
     * The ssl_method option is one of SOAP_SSL_METHOD_TLS, SOAP_SSL_METHOD_SSLv2, SOAP_SSL_METHOD_SSLv3 or
     * SOAP_SSL_METHOD_SSLv23.
     *
     * (Only PHP 5.5+)
     *
     */
    private $sslMethod = SOAP_SSL_METHOD_SSLv23;

    /**
     * @var array|null
     *                 The typemap option is an array of type mappings.
     *                 Type mapping is an array with keys type_name, type_ns (namespace URI),
     *                 from_xml (callback accepting one string parameter) and to_xml
     *                 (callback accepting one object parameter).
     *
     * Example array("type_ns"   => "http://schemas.nothing.com",
     *               "type_name" => "book",
     *               "from_xml"  => "some_function_name" callback accepting one string parameter
     *               "to_xml"    => "some_function_name" callback accepting one string parameter")
     *
     */
    private $typeMap;

    /**
     * Soap Version (either SOAP_1_1 or SOAP_1_2).
     *
     * @var int
     */
    private $soapVersion = SOAP_1_1;

    /**
     * Must be set in non-WSDL mode.
     *
     * @var string
     */
    private $uri;

    /**
     * The user_agent option specifies string to use in User-Agent header.
     *
     * @var string
     */
    private $userAgent = self::USER_AGENT;

    /**
     * @var string
     */
    private $username;

    /**
     * Load config object from array.
     *
     * @param array $options
     *
     * @return SoapSettings
     *
     * @throws InputException
     */
    public static function loadFromArray(array $options)
    {
        $options = array_filter($options);
        if (!array_key_exists('username', $options) || !array_key_exists('password', $options)) {
            throw new InputException('Not all mandatory config credentials are set');
        }

        $config = new static();
        foreach ($options as $key => $value) {
            $name = 'set' . ucfirst(strtolower($key));
            if (method_exists($config, $name)) {
                $config->{$name}($value);
            }
        }

        return $config;
    }

    /**
     * @return int
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @param int $authentication
     *
     * @return SoapSettings
     */
    public function setAuthentication($authentication)
    {
        $this->authentication = $authentication;

        return $this;
    }

    /**
     * @return int
     */
    public function getCacheWsdl()
    {
        return $this->cacheWsdl;
    }

    /**
     * @param int $cacheWsdl
     *
     * @return SoapSettings
     */
    public function setCacheWsdl($cacheWsdl)
    {
        $this->cacheWsdl = $cacheWsdl;

        return $this;
    }

    /**
     * Returns class mapping.
     *
     * @return array|null
     */
    public function getClassMap()
    {
        return $this->classMap;
    }

    /**
     * @param array|null $classMap
     *
     * @return SoapSettings
     */
    public function setClassMap($classMap)
    {
        $this->classMap = $classMap;

        return $this;
    }

    /**
     */
    public function getCompression()
    {
        return $this->compression;
    }

    /**
     * @param null $compression
     *
     * @return SoapSettings
     */
    public function setCompression($compression)
    {
        if (in_array($compression, [SOAP_COMPRESSION_ACCEPT, SOAP_COMPRESSION_GZIP, SOAP_COMPRESSION_DEFLATE], true)) {
            $this->compression = $compression;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getConnectionTimeout()
    {
        return $this->connectionTimeout;
    }

    /**
     * @param int $connectionTimeout
     *
     * @return SoapSettings
     *
     * @throws InputException
     */
    public function setConnectionTimeout($connectionTimeout)
    {
        if (!is_int($connectionTimeout)) {
            throw new InputException('Not a valid timeout');
        }
        $this->connectionTimeout = $connectionTimeout;

        return $this;
    }

    /**
     * @return resource
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param resource $context
     *
     * @return SoapSettings
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param mixed $encoding
     *
     * @return SoapSettings
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasExceptions()
    {
        return $this->exceptions;
    }

    /**
     * @param bool $exceptions
     *
     * @return SoapSettings
     */
    public function setExceptions($exceptions)
    {
        $this->exceptions = $exceptions;

        return $this;
    }

    /**
     * @return int
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * Set SoapFeatures bitmask.
     *
     * @param int $features
     *
     * @return SoapSettings
     */
    public function setFeatures($features)
    {
        if (in_array($features, [SOAP_SINGLE_ELEMENT_ARRAYS, SOAP_USE_XSI_ARRAY_TYPE, SOAP_WAIT_ONE_WAY_CALLS], true)) {
            $this->features |= $features;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isKeepAlive()
    {
        return $this->keepAlive;
    }

    /**
     * @param bool $keepAlive
     *
     * @return SoapSettings
     */
    public function setKeepAlive($keepAlive)
    {
        $this->keepAlive = $keepAlive;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocalCert()
    {
        return $this->localCert;
    }

    /**
     * @param string $localCert
     *
     * @return SoapSettings
     */
    public function setLocalCert($localCert)
    {
        $this->localCert = $localCert;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassphrase()
    {
        return $this->passphrase;
    }

    /**
     * @param string $passPhrase
     *
     * @return SoapSettings
     */
    public function setPassphrase($passPhrase)
    {
        $this->passphrase = $passPhrase;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return SoapSettings
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getProxyHost()
    {
        return $this->proxyHost;
    }

    /**
     * @param string $proxyHost
     *
     * @return SoapSettings
     */
    public function setProxyHost($proxyHost)
    {
        $this->proxyHost = $proxyHost;

        return $this;
    }

    /**
     * @return string
     */
    public function getProxyLogin()
    {
        return $this->proxyLogin;
    }

    /**
     * @param string $proxyLogin
     *
     * @return SoapSettings
     */
    public function setProxyLogin($proxyLogin)
    {
        $this->proxyLogin = $proxyLogin;

        return $this;
    }

    /**
     * @return string
     */
    public function getProxyPassword()
    {
        return $this->proxyPassword;
    }

    /**
     * @param string $proxyPassword
     *
     * @return SoapSettings
     */
    public function setProxyPassword($proxyPassword)
    {
        $this->proxyPassword = $proxyPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getProxyPort()
    {
        return $this->proxyPort;
    }

    /**
     * @param string $proxyPort
     *
     * @return SoapSettings
     */
    public function setProxyPort($proxyPort)
    {
        $this->proxyPort = $proxyPort;

        return $this;
    }

    /**
     * @return int
     */
    public function getResponseTimeout()
    {
        return $this->responseTimeout;
    }

    /**
     * @param int $responseTimeout
     *
     * @return SoapSettings
     */
    public function setResponseTimeout($responseTimeout)
    {
        $this->responseTimeout = $responseTimeout;

        return $this;
    }

    /**
     * @return int
     */
    public function getSoapVersion()
    {
        return $this->soapVersion;
    }

    /**
     * Defaults to 1.
     *
     * @param int $soapVersion
     */
    public function setSoapVersion($soapVersion)
    {
        if (in_array($soapVersion, [SOAP_1_1, SOAP_1_2], false)) {
            $this->soapVersion = $soapVersion;
        }
    }

    /**
     * @return int
     */
    public function getSslMethod()
    {
        return $this->sslMethod;
    }

    /**
     * php 5.4+.
     *
     * @param int $sslMethod
     *
     * @return SoapSettings
     */
    public function setSslMethod($sslMethod)
    {
        if (PHP_VERSION_ID >= 50400 && in_array($sslMethod, self::$sslMethods, true)) {
            $this->sslMethod = $sslMethod;
        }

        return $this;
    }

    /**
     * @return array|null
     */
    public function getTypeMap()
    {
        return $this->typeMap;
    }

    /**
     * @param array|null $typeMap
     *
     * @return SoapSettings
     */
    public function setTypeMap($typeMap)
    {
        $this->typeMap = $typeMap;

        return $this;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     *
     * @return SoapSettings
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     *
     * @return SoapSettings
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return SoapSettings
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * @return int
     */
    public function getRetryMinutes()
    {
        return $this->retryMinutes;
    }

    /**
     * @param int $retryMinutes
     */
    public function setRetryMinutes($retryMinutes)
    {
        $this->retryMinutes = $retryMinutes;
    }
}
