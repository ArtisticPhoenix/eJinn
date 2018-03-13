<?php
namespace evo\ejinn\Exception;

/**
 * (eJinn Generated File, do not edit directly)
 * eJinn The Exception Genie
 *
 * @author ArtisticPhoenix
 * @package Evo
 * @subpackage Exception
 * @link https://github.com/ArtisticPhoenix/eJinn/issues
 * @varsion 1.0.0
 * @eJinn:buildVersion 1.0.0
 * @eJinn:buildTime 1520918860.8837
 */
class ReservedKeyword extends \Exception implements \evo\ejinn\Exception\eJinnExceptionInterface
{

    /**
     *
     * {@inheritDoc}
     * @see \Exception::__construct()
     */
    public function __construct($message = "", $code = 1009, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
