<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Error is thrown when the type of an argument is correct but the value of it is incorrect.
 * For example, passing a negative integer when the function expects a positive one, or
 * passing an empty string/array when the function expects it to not be empty.
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
class ValueError extends \ValueError implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 975;

    /**
     *
     * {@inheritDoc}
     * @see \ValueError::__construct()
     */
    public function __construct($message = "", $code = 975, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}