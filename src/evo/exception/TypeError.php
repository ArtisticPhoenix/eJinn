<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Error is thrown when the value being set for a class property does not match the
 * property's corresponding declared type Or the argument type being passed to a function
 * does not match its corresponding declared parameter type Or a value being returned from a
 * function does not match the declared function return type.
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
class TypeError extends \TypeError implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 972;

    /**
     *
     * {@inheritDoc}
     * @see \TypeError::__construct()
     */
    public function __construct($message = "", $code = 972, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}