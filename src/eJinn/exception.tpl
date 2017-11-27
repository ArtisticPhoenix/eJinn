<?php
{$namespace}
{$use}
/**
 * {$docBlock}
 */
class {$name} extends {$extends}{$impliments}
{
	const ERROR_CODE = {$code};
	
	const SEVERITY = {$severity};
   
    /**
     *
     * @param string $message
     * @param number $code
     * @param \Exception $previous
     * @throws \{$class}
     */
    public function __construct($message = null, $code = self::ERROR_CODE, $severity = self::SEVERITY, $filename = null, $lineno = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
