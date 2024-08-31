<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * ErrorException thrown during shutdown by an uncaught Error.
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
class ShutdownErrorException extends ErrorException implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 25;

    /**
     *
     * {@inheritDoc}
     * @see ErrorException::__construct()
     */
    public function __construct($message = "", $code = 25, $severity = 1, $filename =null, $lineno = null, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
    }
}