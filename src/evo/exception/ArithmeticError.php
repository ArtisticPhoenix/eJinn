<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Error is thrown when an error occurs while performing mathematical operations. These
 * errors include attempting to perform a bitshift by a negative amount, and any call to
 * intdiv() that would result in a value outside the possible bounds of an int.
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
class ArithmeticError extends \ArithmeticError implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 957;

    /**
     *
     * {@inheritDoc}
     * @see \ArithmeticError::__construct()
     */
    public function __construct($message = "", $code = 957, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}