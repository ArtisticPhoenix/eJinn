<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Exception thrown when the functionality is not fully implemented yet. Useful as a
 * trackable placeholder for development
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
class NotImplementedException extends EvoException implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 20;

    /**
     *
     * {@inheritDoc}
     * @see EvoException::__construct()
     */
    public function __construct($message = "", $code = 20, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}