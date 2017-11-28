<?php
namespace eJinn;


/**
 *
 * (c) 2016 Hugh Durham III
 *
 * For license information please view the LICENSE file included with this source code.
 *
 * @author HughDurham {ArtisticPhoenix}
 * @package eJinn
 *
 */
final class eJinnGenerator
{

    /**
     * all availble keys
     * changing the order of this will affect the value of configHash
     * @var array
     */
    protected static $schema;
    
    /**
     * Keys that contain loclized data
     * @var array
     */
    protected static $local = [
        "name"          => false,
        "parse"         => true
    ];
    
    /**
     * Keys that contain data appled to entities only
     * @var array
     */
    protected static $containers = [
        "namespaces"    => [],
        "interfaces"    => [],
        "exceptions"    => [],
        "reserverange"  => [],
        "reserve"       => []  
    ];

    /**
     * Keys that contain data applied gobaly
     * @var array
     */
    protected static $global = [
        "author"        => false,
        "description"   => false,
        "package"       => false,
        "subpackage"    => false,
        "support"       => false,
        "version"       => false,
        "buildpath"     => false,
        "extends"       => "\Exception",
        "severity"      => E_ERROR,
    ];
    
    /**
     * Doc comment format
     * @var array
     */
    protected static $doc = [
        "author"        => "@author %s",
        "description"   => "%s",
        "package"       => "@package %s",
        "subpackage"    => "@subpackage %s",
        "support"       => "@link %s",
        "version"       => "@varsion %s",
    ];
    
    /**
     *
     * @var array
     */
    protected $reserved = [];
    
    /**
     *
     * @var array
     */
    protected $exceptions = [];
    
    /**
     *
     * @var array
     */
    protected $interfaces = [];
    
    /**
     *
     * @param array $config - either an array config or a json config
     */
    public function __construct(array $config = null)
    {
        if (!self::$schema) {
            self::$schema = array_merge(self::$containers, self::$global, self::$local);
        }

        if ($config) {
            $this->setConf($config);
        }
    }
    
    /**
     *
     * @param array $config
     */
    public function setConf(array $config)
    {
        $this->reset();
        $this->parseConf($config);
    }
    
    /**
     * create the files
     */
    public function build()
    {
    }
    
    /**
     * reset the builder
     */
    protected function reset()
    {
        $this->reserved = [];
        $this->exceptions = [];
        $this->interfaces = [];
    }
    
    
    protected function parseConf(array $eJinn)
    {
        //lowercase all keys at this level
        $eJinn = array_change_key_case($eJinn);
        
        //check for any unknown keys in our array
        //$this->ckUnknownKeys($eJinn, "in top level of config");
        
        //Check if namespaces exist, and then seperate
        if (!isset($eJinn['namespaces']) || empty($eJinn['namespaces'])) {
            die("Error: IN ".__FILE__." ON ".__LINE__); //@todo: throw new MissingRequired("Missing namespace key");
        }
        
        //seperate
        $namespaces = $this->cutArrayKey('namespaces', $eJinn);
        
        //parse the reserved Error Codes at this level and remove
        $this->preserveReservedCodes($eJinn);
        
        //make sure we only have global data
        //$this->ckAllowed($eJinn, self::$global, "in top level of config");
        
        foreach ($namespaces as $namespace => $config) {
            if (empty($conf)) continue; //empty namespace are ignored
            
            $this->changeKeyCase($config);
            
            
            //lowercase keys
           /* $config = array_change_key_case($config);
            //check for unknown keys
            $this->ckUnknownKeys($config, "in Namespace[$namespace]");
            
            $conf['namespace'] = $namespace;
            
            $conf = array_merge(self::$global, $global, $conf);
            
            $this->preserveReservedCodes($conf);

            $this->parseNamespace($namespace, $config, $global);*/
        }
        print_r( str_pad( " FINAL " , 60, '=', STR_PAD_BOTH)."\n");
        
        print_r( str_pad(str_pad( " Reserved " , 30, '-', STR_PAD_BOTH), 60, ' ', STR_PAD_BOTH)."\n");
        print_r($this->reserved);

        print_r( str_pad(str_pad( " Interfaces " , 30, '-', STR_PAD_BOTH), 60, ' ', STR_PAD_BOTH)."\n");
        print_r($this->interfaces);
        
        print_r( str_pad(str_pad( " Excpetions " , 30, '-', STR_PAD_BOTH), 60, ' ', STR_PAD_BOTH)."\n");
        print_r($this->exceptions);
        
    }
    
    protected function parseNamespace( $namespace, $conf, $global){

        
        //seperate interfaces
        $interfaces = false;
        if (isset($conf['interfaces']) && !empty($conf['interfaces'])) {
            $interfaces = array_unique($conf['interfaces']);
        }
        unset($conf['interfaces']);
        
        //seprate exceptions
        $exceptions = false;
        if (isset($conf['exceptions']) && !empty($conf['exceptions'])) {
            $exceptions = $conf['exceptions'];
        }
        unset($conf['exceptions']);
        
        if (!$interfaces && !$exceptions) {
            die("Error: IN ".__FILE__." ON ".__LINE__); //@todo: throw new MissingRequired("Namespace[$namespace] must include either interfaces or exceptions");
        }
        
        $impliments = [];
        
        if ($interfaces) {        
            foreach ($interfaces as $interface) {
                $impliments[] = $this->parseInterface($interface, $conf);
            }
        }
        
        if ($exceptions) {
            foreach ($exceptions as $exception) {
                $this->parseExceptions($exception, $conf, $impliments);
            }
        }
    }
    
    /**
     *
     * @param array $exceptions
     * @param array $global
     */
    protected function parseInterface($interface, array $global)
    {
        if (!is_array($interface)) {
            $interface = ['name' => $interface];
        } else {
            $interface = array_change_key_case($interface);
            if(!$interface['parse']){
                //non-parsed interface,
                return "\\".ltrim($interface['name'],"\\");
            }  
        }
        
        $interface = $this->parseEntity($interface, $global);
        
        if (isset($this->interfaces[$interface['qName']])){
            print_r(str_pad(' Duplicate Interface ', 60, '*', STR_PAD_BOTH)."\n");          
            //die("Error: IN ".__FILE__." ON ".__LINE__);
            return;
        }
        
        $this->interfaces[$interface['qName']] = $interface;
        
        return $interface['qName'];
    }
    
    /**
     *
     * @param array $exceptions
     * @param array $global
     */
    protected function parseExceptions($exception, array $global)
    {
        //print_r($exception);
    }
    
    
    /**
     *
     * @param string $namespace
     * @param mixed $interface
     * @param array $global
     * @return string
     */
    protected function parseEntity(array $entity, array $global)
    {
        print_r(str_pad(' '.__METHOD__.' ', 60, '=', STR_PAD_BOTH)."\n");
        
        if (!isset($entity['name'])) {
            die("Error: IN ".__FILE__." ON ".__LINE__); //@todo: throw new MissingRequired("Interfaces must include a name");
        }
        
        $this->ckUnknownKeys($entity, "in Namespace[{$global['namespace']}]");
        
        $entity = array_merge(self::$global, $global, $entity);
        
        $this->preserveReservedCodes($entity);
        
        $entity['qName'] = $this->parseName($entity);
        
        $entity['hash'] = $this->hashConfig($entity);
        
        return $entity;
    }
    
    /*
     * 
     * @param string $namespace
     * @param mixed $interface
     * @param array $global
     * @return string
     *
    protected function parseInterface($namespace, $interface, array $global)
    {
        print_r(str_pad(' '.__METHOD__.' ', 60, '=', STR_PAD_BOTH)."\n");

        if (!is_array($interface)) {
            $interface = ['name' => $interface];
        } else {
            $interface = array_change_key_case($interface);
            
            if(!$interface['parse']){ 
                //non-parsed interface, 
                return "\\".ltrim($interface['name'],"\\");
            }

            if (!isset($interface['name'])) {
                die("Error: IN ".__FILE__." ON ".__LINE__); //@todo: throw new MissingRequired("Interfaces must include a name");
            }
            $this->ckUnknownKeys($interface, "in Namespace[$namespace]");
        }
        
        $interface = array_merge(self::$global, $global, $interface);
        
        $this->preserveReservedCodes($interface);
        
        $interface['qName'] = $this->parseName($interface);
        
        $interface['hash'] = $this->hashConfig($interface);
        
        if (isset($this->interfaces[$interface['qName']])){
            print_r(str_pad(' Duplicate Interface ', 60, '*', STR_PAD_BOTH)."\n");
            
            //die("Error: IN ".__FILE__." ON ".__LINE__);
        }
        
        $this->interfaces[$interface['qName']] = $interface;
        
        return $interface['qName'];
    }*/
         
    /**
     * 
     * @param array $item
     * @return string
     */
    protected function parseName(array &$item){
        $ns = "\\";
        
        //Parsed names cannot contain \\, ie. they must be relative paths
        if( false !== strpos($item['name'], $ns)){
            die("Error: IN ".__FILE__." ON ".__LINE__);
        }
        
        $item['namespace'] = trim($item['namespace'], $ns);
        
        if(!empty($item['namespace'])){
            return $ns.$item['namespace'].$ns.$item['name'];
        }else{
            return $ns.$item['name'];
        }

    }
      
    /**
     * set the reserved codes ( if present )
     * remove from origin by refrence
     *
     * @param array $conf
     * @throws MissingRequired
     */
    protected function preserveReservedCodes(array &$array = null)
    {
        if (!$array) return;
  
        if (isset($array['reserve']) && is_array($array['reserve'])) {
            $this->reserved = array_merge($this->reserved, $array['reserve']);
        }
     
        if (isset($array['reserverange'])&& is_array($array['reserverange'])) {
            if (2 != ($count = count($array['reserverange']))) {
                die("Error: IN ".__FILE__." ON ".__LINE__); //@todo: throw new MissingRequired("key:reserveRange expects exactly 2 elements, give {$count}");
            }
            $range = range(array_pop($array['reserverange']), array_pop($array['reserverange']));
            $this->reserved = array_merge($this->reserved, $range);
        }
     
        unset($array['reserve'],$array['reserverange']);
    }
     
    /**
     * check if any keys are present that don't exist in our
     * global key array
     *
     * @param array $array
     */
    protected function ckUnknownKeys(array $array)
    {
        $diff = array_diff_key($array, self::$schema);
        if (!empty($diff)) {
            $s = count($diff) > 1 ? "s" : "";
             
            die("Error: IN ".__FILE__." ON ".__LINE__); //@todo: throw new UnknownConfKey("Unknown Key{$s}\"".implode('","', array_keys($diff))."\"");
        }
    }
     
    /**
     *
     * @param array $array
     * @param array $allowed
     * @throws UnknownConfKey
     */
    protected function ckAllowed(array $array, array $allowed, $message = '')
    {
        $diff = array_diff_key($array, $allowed);
        if (!empty($diff)) {
            if (!empty($message)) {
                $message = ' '.$message;
            }
            
            $s = count($diff) > 1 ? "s" : "";
            
            die("Error: IN ".__FILE__." ON ".__LINE__); //@todo: throw new KeyNotAllowed("Key{$s} \"".implode('","', array_keys($diff))."\" not allowed{$message}");
        }
    }
    
    /**
     * Cut an item for array, by key.
     * 
     * returns the item on success, false on failure
     * the oringal array is also modifed by removing the item
     * 
     * @param string $key
     * @param array $array
     * @return boolean|mixed
     */
    protected function cutArrayKey($key, array &$array ){
        if(!isset($array[$key])) return false;
        
        $item = $array[$key];
        unset($array[$key]);
        return $item;
    }
    /**
    * Changes the case of all keys in an array
    * 
    * @link http://www.php.net/manual/en/function.array-change-key-case.php
    * @param array $array The array to work on
    * @param int $case [CASE_LOWER] or CASE_UPPER
    * @return array an array with its keys lower or uppercased
    */
    protected function changeKeyCase(array &$array, $case = CASE_LOWER){
        $array = array_change_key_case($array, $case);
    }
      
      
    /**
     * create a hash from an object config
     *
     * @param array $array
     * @return string
     */
    protected function hashConfig(array $array)
    {
        //remove this for obvious reasons
        unset($array['hash']);
           
        return sha1(implode($this->removeNestedArrays($array)));
    }
       
    /**
    * Remove any nested arrays
    *
    * @param array $array
    * @return array
    */
    protected function removeNestedArrays(array $array)
    {
        return array_filter($array, function ($item) {
            //remove array items, these are not part of the items config
            return !is_array($item);
        });
    }
}
