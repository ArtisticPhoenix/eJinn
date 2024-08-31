<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Exception thrown if a file has an invalid extension.
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
class BadFileExtensionException extends FilesystemException implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 2530;

    /**
     *
     * {@inheritDoc}
     * @see FilesystemException::__construct()
     */
    public function __construct($message = "", $code = 2530, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}