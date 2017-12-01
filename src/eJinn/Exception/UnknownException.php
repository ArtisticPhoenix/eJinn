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
 * @eJinn:hash afewadsff2324rsfsfdfDFE323Q
 * @eJinn:buildVersion 0.0.1
 * @eJinn:buildTime 1512128233.7546949
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
