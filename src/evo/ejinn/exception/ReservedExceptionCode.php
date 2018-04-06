<?php
namespace evo\ejinn\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * eJinn The Exception Genie
 *
 * @author ArtisticPhoenix
 * @package Evo
 * @subpackage Exception
 * @link https://github.com/ArtisticPhoenix/eJinn/issues
 * @version 1.0.0
 * @eJinn:buildVersion 1.0.0
 * @eJinn:buildTime 1522902666.5463
 */
class ReservedExceptionCode extends \Exception implements \evo\ejinn\exception\eJinnExceptionInterface
{

    /**
     * @var int
     */
    const ERROR_CODE = 10010;

    /**
     *
     * {@inheritDoc}
     * @see \Exception::__construct()
     */
    public function __construct($message = "", $code = 10010, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
