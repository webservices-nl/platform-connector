<?php

namespace WebservicesNl\Soap\Exception;

use WebservicesNl\Common\Exception\Exception as WebserviceException;
use WebservicesNl\Common\Exception\ServerException;

/**
 * Class Converter.
 */
class Converter
{
    /**
     * @param \SoapFault $fault
     *
     * @return WebserviceException
     *
     * @throws ServerException
     */
    public function convertToException(\SoapFault $fault)
    {
        $errorClassName = isset($fault->{'detail'}->{'errorCode'}) ? $fault->{'detail'}->{'errorCode'} : 'Server';
        $errorClassFQ = 'WebservicesNl\Common\Exception\\' . str_replace('.', '\\', $errorClassName) . 'Exception';

        // should we throw an error about throwing an error? or just create a default error?
        if (!class_exists($errorClassFQ)) {
            throw new ServerException($fault->getMessage());
        }

        /** @var WebserviceException $exception */
        return new $errorClassFQ($fault->getMessage(), $fault->getCode());
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
