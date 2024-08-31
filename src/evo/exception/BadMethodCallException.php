<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Exception thrown if a callback refers to an undefined method or if some arguments are
 * missing.
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
class BadMethodCallException extends \BadMethodCallException implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 906;

    /**
     *
     * {@inheritDoc}
     * @see \BadMethodCallException::__construct()
     */
    public function __construct($message = "", $code = 906, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}