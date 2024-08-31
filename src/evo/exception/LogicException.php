<?php
namespace evo\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Exception that represents error in the program logic. This kind of exception should lead
 * directly to a fix in your code
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
class LogicException extends \LogicException implements EvoExceptionInterface
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = 930;

    /**
     *
     * {@inheritDoc}
     * @see \LogicException::__construct()
     */
    public function __construct($message = "", $code = 930, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}