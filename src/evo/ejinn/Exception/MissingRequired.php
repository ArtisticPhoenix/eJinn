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
 * @eJinn:buildTime 1520921778.8741
 */
class MissingRequired extends \Exception implements \evo\ejinn\Exception\eJinnExceptionInterface
{

	/**
	 * @var int
	 */
	const ERROR_CODE = 1008;

    /**
     *
     * {@inheritDoc}
     * @see \Exception::__construct()
     */
    public function __construct($message = "", $code = 1008, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
