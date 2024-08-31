<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Exception thrown if a value does not match with a set of values. Typically, this happens
 * when a function calls another function and expects the return value to be of a certain
 * type or value, not including arithmetic or buffer related errors.
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
class UnexpectedValueException extends \UnexpectedValueException implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 951;

    /**
     *
     * {@inheritDoc}
     * @see \UnexpectedValueException::__construct()
     */
    public function __construct($message = "", $code = 951, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}