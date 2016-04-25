<?php

namespace WebservicesNl\Protocol\Soap\Exception;

/**
 * Interface ConverterInterface
 *
 * Webservices Exception converters. Converts PHP exceptions into a 'error' and back into Exception.
 */
interface ConverterInterface
{
    /**
     * Converts a protocol specific error into platform specific Domain Exception.
     *
     * @param mixed $error
     *
     * @return \Exception
     */
    public function convertToException($error);

    /**
     * Converts a Domain specific exception into protocol generic exception
     *
     * @param \Exception $exception
     *
     * @return mixed
     */
    public function convertFromException(\Exception $exception);
}
