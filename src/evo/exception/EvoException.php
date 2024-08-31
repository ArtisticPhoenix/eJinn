<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Base Exception class. May be used as a placeholder or generic exceptions.
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
class EvoException extends \Exception implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 0;

    /**
     *
     * {@inheritDoc}
     * @see \Exception::__construct()
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}