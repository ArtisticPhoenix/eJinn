<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Error is thrown when an error occurs while parsing.
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
class ParseError extends \ParseError implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 969;

    /**
     *
     * {@inheritDoc}
     * @see \ParseError::__construct()
     */
    public function __construct($message = "", $code = 969, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}