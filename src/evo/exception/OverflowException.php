<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Exception thrown when adding an element to a full container.
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
class OverflowException extends \OverflowException implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 939;

    /**
     *
     * {@inheritDoc}
     * @see \OverflowException::__construct()
     */
    public function __construct($message = "", $code = 939, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}