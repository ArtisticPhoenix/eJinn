<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Exception thrown if a value does not adhere to a defined valid data domain. This
 * represents errors that should be detected at compile time.
 * 
 * @author ArtisticPhoenix
 * @package Evo
 * @subpackage Evo
 * @link https://github.com/ArtisticPhoenix/eJinn/issues
 * @version 2.0.0
 * @license GPL-3.0
 * @eJinn:buildVersion 2.0.0
 * @eJinn:buildTime 1725132818.516
 */
class DomainException extends \DomainException implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 921;

    /**
     *
     * {@inheritDoc}
     * @see \DomainException::__construct()
     */
    public function __construct($message = "", $code = 921, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}