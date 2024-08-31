<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Error is thrown when too few arguments are passed to a user-defined function or method.
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
class ArgumentCountError extends \ArgumentCountError implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 954;

    /**
     *
     * {@inheritDoc}
     * @see \ArgumentCountError::__construct()
     */
    public function __construct($message = "", $code = 954, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}