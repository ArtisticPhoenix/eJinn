<?php
namespace \eJinn\Exception;

/**
 * (eJinn Generated File, do not edit directly)
 * eJinn The Exception Genie
 *
 * @author ArtisticPhoenix
 * @package eJinn
 * @subpackage Exception
 * @link https://github.com/ArtisticPhoenix/eJinn/issues
 * @varsion 1.0.0
 * @eJinn:buildVersion 0.0.1
 * @eJinn:buildTime 1518638282.5547
 */
class UnknownError extends \Exception implements \eJinn\Exception\eJinnExceptionInterface
{

    /**
     *
     * {@inheritDoc}
     * @see \Exception::__construct()
     */
    public function __construct($message = "", $code = 0, \Exception $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }
}
