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
 * @eJinn:buildTime 1517558245.7034
 */
class JsonParseError extends \Exception
{

    /**
     *
     * {@inheritDoc}
     * @see \Exception::__construct()
     */
    public function __construct($message = "", $code = 1100, \Exception $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }
}
