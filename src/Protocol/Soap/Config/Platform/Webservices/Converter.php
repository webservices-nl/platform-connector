<?php

namespace WebservicesNl\Protocol\Soap\Config\Platform\Webservices;

use WebservicesNl\Common\Exception\Exception as WebserviceException;
use WebservicesNl\Common\Exception\ServerException;
use WebservicesNl\Protocol\Soap\Exception\ConverterInterface;

/**
 * Webservice SoapConverter.
 *
 * Converts generic Soap fault back into PHP exception with Webservices domain logic exceptions.
 */
class Converter implements ConverterInterface
{
    /**
     * @param \SoapFault $fault
     *
     * @return WebserviceException
     * @throws ServerException
     */
    public function convertToException($fault)
    {
        $errorClassName = isset($fault->{'detail'}->{'errorcode'}) ? $fault->{'detail'}->{'errorcode'} : 'Server';
        $errorClassFQ = 'WebservicesNl\Common\Exception\\' . str_replace('.', '\\', $errorClassName) . 'Exception';

        // should we throw an error about throwing an error? or just create a default error?
        if (!class_exists($errorClassFQ)) {
            throw new ServerException("Could not convert errorCode: '$errorClassName'");
        }

        /** @var WebserviceException $exception */
        return new $errorClassFQ($fault->getMessage(), $fault->getCode());
    }

    /**
     * @param \Exception $exception
     *
     * @return void
     * @throws \DomainException
     */
    public function convertFromException(\Exception $exception)
    {
        throw new \DomainException('Not yet implemented');
    }

    /**
     * Return error (build statically).
     *
     * @return static
     */
    public static function build()
    {
        return new static();
    }
}
