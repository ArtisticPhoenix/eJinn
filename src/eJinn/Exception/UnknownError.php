<?php
namespace eJinn\Exception;

/**
 *
 * @author ArtisticPhoenix
 * @license GPL-3.0
 * @version 1.0.0
 * @package eJinn
 * @subpackage Exception
 * @link https://github.com/ArtisticPhoenix/eJinn/issues
 */
class UnknownError extends \Exception implements \eJinn\ExceptionInterface
{
   
    /**
     *
     * @param string $message
     * @param number $code
     * @param \Exception $previous
     * @throws \eJinn\Exception\UnknownError
     */
    public function __construct($message = null, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
