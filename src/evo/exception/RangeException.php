<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Exception thrown to indicate range errors during program execution. Normally this means
 * there was an arithmetic error other than under/overflow. This represents errors that
 * should be detected at run time. This is the runtime version of DomainException.
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
class RangeException extends \RangeException implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 942;

    /**
     *
     * {@inheritDoc}
     * @see \RangeException::__construct()
     */
    public function __construct($message = "", $code = 942, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}