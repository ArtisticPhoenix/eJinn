<?php
namespace eJinn\Exception;

/**
 *
 * eJinn The Exception Genie
 * (eJinn genenerated class, do not edit)
 *
 * @author ArtisticPhoenix
 * @license GPL-3.0
 * @version 1.0.0
 * @package eJinn
 * @subpackage Exception
 * @link https://github.com/ArtisticPhoenix/eJinn/issues
 * @cacheKey afewadsff2324rsfsfdfDFE323Q
 */
class UnknownException extends \Exception implements \eJinn\Exception\eJinnExceptionInterface
{
   
    /**
     *
     * @param string $message [optional]
     * @param number $code [optional]
     * @param \Exception $previous [optional]
     * @throws \eJinn\Exception\UnknownError
     */
    public function __construct($message = null, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
