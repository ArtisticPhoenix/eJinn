<?php
namespace eJinn;

use eJinn\Exception\UnknownError;

class eJinnGenerator
{
    /**
     * 
     * @param mixed $conf - either an array config or a json config
     */
    public function __construct( $conf ){
        if(!is_array($conf)){
            $conf = json_decode($json, true);
            if($code = json_last_error()){
                throw new UnknownError(json_last_error_msg(), $code);
            }
        }
    }
    
}
