<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Exception thrown if a resource cannot be accessed by the current user.
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
class PermissionException extends RuntimeException implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 2015;

    /**
     *
     * {@inheritDoc}
     * @see RuntimeException::__construct()
     */
    public function __construct($message = "", $code = 2015, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}