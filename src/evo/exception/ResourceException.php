<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Exception thrown for a resource.
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
class ResourceException extends RuntimeException implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 2000;

    /**
     *
     * {@inheritDoc}
     * @see RuntimeException::__construct()
     */
    public function __construct($message = "", $code = 2000, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}