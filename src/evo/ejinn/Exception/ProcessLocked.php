<?php
namespace evo\ejinn\Exception;

/**
 * (eJinn Generated File, do not edit directly)
 * eJinn The Exception Genie
 *
 * @author ArtisticPhoenix
 * @package Evo
 * @subpackage Exception
 * @link https://github.com/ArtisticPhoenix/eJinn/issues
 * @varsion 1.0.0
 * @eJinn:buildVersion 1.0.0
 * @eJinn:buildTime 1520920949.2729
 */
class ProcessLocked extends \Exception implements \evo\ejinn\Exception\eJinnExceptionInterface
{

	/**
	 * @var int
	 */
	const ERROR_CODE = 1012;

    /**
     *
     * {@inheritDoc}
     * @see \Exception::__construct()
     */
    public function __construct($message = "", $code = 1012, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
