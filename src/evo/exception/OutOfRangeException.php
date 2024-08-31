<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Exception thrown when an illegal index was requested. This represents errors that should
 * be detected at compile time. This is the runtime version of DomainException
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
class OutOfRangeException extends \OutOfRangeException implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 936;

    /**
     *
     * {@inheritDoc}
     * @see \OutOfRangeException::__construct()
     */
    public function __construct($message = "", $code = 936, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}