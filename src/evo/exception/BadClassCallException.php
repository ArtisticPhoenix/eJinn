<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Exception thrown if a callback refers to an undefined class or if some arguments are
 * missing from classes constructor.
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
class BadClassCallException extends LogicException implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 903;

    /**
     *
     * {@inheritDoc}
     * @see LogicException::__construct()
     */
    public function __construct($message = "", $code = 903, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}