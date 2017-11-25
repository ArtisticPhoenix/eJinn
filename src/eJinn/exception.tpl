<?php
{$namespace}

/**
 * {$docBlock}
 */
class {$class} extends \Exception {$interface}
{
   
    /**
     *
     * @param string $message
     * @param number $code
     * @param \Exception $previous
     * @throws \{$class}
     */
    public function __construct($message = null, $code = {$code}, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
