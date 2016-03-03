<?php

namespace Webservicesnl\Soap\Exception;

use Webservicesnl\Exception\Exception;
use Webservicesnl\Exception\ServerException;

/**
 * Class Converter.
 */
class Converter
{
    /**
     * @param \SoapFault $fault
     *
     * @return Exception;
     *
     * @throws ServerException
     */
    public function convertToException(\SoapFault $fault)
    {
        $errorCode = isset($fault->{'detail'}->{'errorCode'}) ? $fault->{'detail'}->{'errorCode'} : 'Server';
        $errorClassFQ = str_replace('.', '\\', $errorCode) . 'Exception';

        if (!class_exists($errorClassFQ)) {
            throw new ServerException($fault->getMessage());
        }

        /** @var Exception $exception */
        $exception = new $errorClassFQ($fault->getMessage());

        return $exception;
    }
}
