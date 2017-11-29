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
     * Keys that contain loclized data
     * @var array
     */
    protected $local = [
        "name"          => false,
        //"parse"         => true,
        "code"          => false,
    ];
    
    /**
     * Keys that contain data appled to entities only
     * @var array
     */
    protected $containers = [
        "namespaces"    => [],
        "interfaces"    => [],
        "exceptions"    => [],
        "reserved"      => []  
    ];

    /**
     * Keys that contain data applied gobaly
     * @var array
     */
    protected $global = [
        "author"        => false,
        "description"   => false,
        "package"       => false,
        "subpackage"    => false,
        "support"       => false,
        "version"       => false,
        "buildpath"     => false,
        "extends"       => "\Exception",
        "severity"      => E_ERROR,
        "namespace"     => false,
        'impliments'    => [],
    ];
    
    /**
     * Doc comment format
     * @var array
     */
    protected $doc = [
        "author"        => "@author %s",
        "description"   => "%s",
        "package"       => "@package %s",
        "subpackage"    => "@subpackage %s",
        "support"       => "@link %s",
        "version"       => "@varsion %s",
    ];
    
    /**
     * 
     * @var string
     */
    protected $debug = true;
    
    /**
     * 
     * @var number
     */
    protected $microtime;
    
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
        $this->microtime = microtime(true);

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
        $this->parse($config);
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
    
    /**
     * Parse the eJinn config array
     * 
     * @param array $eJinn
     */
    protected function parse(array $eJinn)
    {
        //pre-process the array recursively
        $this->parseRecursive($eJinn);
        unset($this->reserved['']);
        //check reserved keys
        if(!ctype_digit(implode('',$this->reserved))){
            die("Reserved Error codes must be integers");
        }
        
        $namespaces = $this->extractArrayElement('namespaces', $eJinn);
        if(!$namespaces){
            die("Namespaces element is required");
        }
        
        //normalize merge global
        $global = array_replace($this->global, $eJinn);
        
        //check for keys not allowed at this level
        $this->ckBannedKeys("TopLevel", $global, $this->containers, $this->local);
        
        //check for unkown keys at this level.
        $this->ckUnkownKeys("TopLevel", $global, $this->global);
               
        //continue parsing
        $this->parseNamespaces($namespaces, $global);
        
        
        $this->debug($this->reserved, __LINE__, "Reserved");
        
        $this->debug($this->interfaces, __LINE__, "Interfaces");
        
        $this->debug($this->exceptions, __LINE__, "Exceptions");
            
    }
    
    /**
     * preform transforms on the eJinn array recursivly
     *
     * @param array $array pass by refrence
     * @param string $current
     */
    protected function parseRecursive(array &$array, $current = null)
    {
        //$this->debug( __FUNCTION__, __LINE__);
        if($current != 'namespaces'){
            $array = array_change_key_case($array, CASE_LOWER);
        }
        
        $this->preserveReservedCodes($array);
        
        foreach ($array as $key=>&$value){
            if(is_array($value)){
                $this->parseRecursive($value, $key);
            }
        }
    }
    
    /**
     * parse namespaces
     * 
     * @param array $namespaces
     * @param array $global
     */
    protected function parseNamespaces(array $namespaces, array $global){
        foreach ($namespaces as $ns => $config){
            if( empty($config) ) return;
            
            $impliments = [];
            
            $ns = trim($ns, "\\");
            
            $interfaces = $this->extractArrayElement('interfaces', $config);           
            
            $exceptions =  $this->extractArrayElement('exceptions', $config);            
            
            if(empty($interfaces) && empty($exceptions)){
                die("Namespace[$ns] must contain either interfaces or exceptions");
            }
            
            //normalize merge global
            $namespace = array_replace($global, $config, ['namespace' => $ns]);
            
            //$this->debug($namespace, __LINE__);
            
            //check for keys not allowed at this level
            $this->ckBannedKeys("Namespace[$ns]", $namespace, $this->containers, $this->local);
            
            //check for unkown keys at this level.
            $this->ckUnkownKeys("Namespace[$ns]", $namespace, $this->global);

            if($interfaces){
                $impliments = $this->parseInterfaces($interfaces, $namespace);
            }
            
            if($exceptions){
                $namespace['impliments'] = array_replace($namespace['impliments'] , $impliments);              
                $this->parseExceptions($exceptions, $namespace);
            }            
        }
    }
    
    protected function parseInterfaces(array $interfaces, array $namespace){
        $impliments = [];
        
        //$this->debug($namespace, __LINE__);
        
        foreach ($interfaces as $interface){
 
            $interface = $this->parseEntity($interface, $namespace);
            unset($interface['impliments']);
            $impliments[] = $interface['qualifiedname'];
            
            $this->interfaces[$interface['qualifiedname']] = $interface;
        }
        
        
        return $impliments;
    }
    
    
    protected function parseExceptions(array $exceptions, array $namespace){
        
        //$this->debug($namespace, __LINE__);
        
        foreach ($exceptions as $code => $exception){
            
           
            $exception = $this->parseEntity($exception, $namespace);
            
            if(!is_int($code)){
                die("Excetion[{$exception['namespace']}::{$exception['name']}] expected integer error code, given[{$code}]");
            }

            if(!isset($exception['code'])){
                $exception['code'] = $code;
            }
            
            $this->exceptions[$exception['qualifiedname']] = $exception;
        }
        
    }
    
    
    protected function parseEntity($entity, $namespace){
        
        if(!is_array($entity)){
            $entity = ['name'=>$entity];
        }
        
        $this->ckBannedKeys(
            "Entity[{$namespace['namespace']}::{$entity['name']}]",
            $entity,
            $this->containers,
            ["namespace" => 1]
        );
        
        $entity = array_replace($namespace, $entity);
                
        $this->parseName($entity);
        
        return $entity;
    }
 

    
    /**
     * check for keys not allowed at this level
     * check that input keys are not present in any controls
     * 
     * @param string $set indentifier for the level
     * @param array $input
     * @param array $control  ...variadic 
     */
    protected function ckBannedKeys($set, array $input, array ...$control){
        $diff = array_diff_key($input, ...$control);
        if(count($diff) != count($input)){
            $banned = array_diff_key($input,$diff);
            $s = count($banned) > 1 ? 's' : '';
            
            die("Banned Key{$s} '".implode("', '", array_keys($banned))."' in $set");
        }  
    }
    
    /**
     * check for unknown keys
     * check that input keys are present in all controls
     * 
     * @param string $set indentifier for the level
     * @param array $input
     * @param array $control
     */
    protected function ckUnkownKeys($set, array $input, array ...$control){
        $diff = array_diff_key($input, ...$control);
        if(!empty($diff)){
            $s = count($diff) > 1 ? 's' : '';
            die("Unknown key{$s} '".implode("', '", array_keys($diff))."' in $set");         
        }   
    }
    
    /**
     * 
     * @param array $array
     */
    protected function preserveReservedCodes(array &$array = null)
    {        
        $reserved = $this->extractArrayElement('reserved', $array);
        
        if(!$reserved) return;
        
        if(!is_array($reserved)){
            die("Reserved must be an array of Error Codes");
        }
   
        foreach ($reserved as $reserve){
            if(is_array($reserve)){
                if(count($reserve) != 2){
                    die("Nested reserved must contain exactly 2 elements");
                }
                $range = range(array_shift($reserve), array_shift($reserve));
                $this->reserved += array_combine($range, $range);
            }else{
                $this->reserved[$reserve] = $reserve;
            }
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
    protected function extractArrayElement($key, array &$array ){
        if(!isset($array[$key])) return false;
        
        $item = $array[$key];
        unset($array[$key]);
        return $item;
    }
    
    
   /**
    * 
    * @param array $entity
    * @param array $config
    * @return string
    */
    protected function parseName(array &$entity){
        $ns = "\\";
        
        //Parsed names cannot contain \\, ie. they must be relative paths
        if( false !== strpos($entity['name'], $ns)){
            die("Entity name[{$entity['name']}] cannot contain a NS '$ns' IN ".__FILE__." ON ".__LINE__);
        }
        
        $entity['namespace'] = trim($entity['namespace'], $ns);
        
        if(!empty($entity['namespace'])){
            $qName = $ns.$entity['namespace'].$ns.$entity['name'];
        }else{
            $qName = $ns.$entity['name'];
        }    
        
        $entity['qualifiedname'] = $qName;
    }
    
    /**
     * 
     * @param string $message
     * @param int $line __LINE__
     */
    protected function debug($message, $line, $title = ''){
        $elapsed = number_format((microtime(true) - $this->microtime), 5);
        $o = [];
        $o[] = str_pad(" ".__CLASS__." ", 100, "*", STR_PAD_BOTH);
        $o[] = str_pad("[{$elapsed}/s] in ".__FILE__." on {$line}", 100, " ", STR_PAD_BOTH);
        $o[] = str_pad(" {$title} ", 100, "-", STR_PAD_BOTH);
        $o[] = var_export($message, true);      
        $o[] = str_pad("", 100, "=", STR_PAD_BOTH);
        
        if($this->debug) echo implode("\n", $o)."\n\n";
 
    }
        /*  
        
        
        
        if($this->debug) echo "[{$elapsed}/ms]".$message." in ".__FILE__." on $line\n";/*
    }
    
    //remove and record all the reserved codes
   // $this->preserveReservedCodesRecursive($eJinn);
    
    /**
     * Change the case keys in an array ( recursive )
     *
     * @param array $array Pass by refrence
     * @param string $case [CASE_LOWER] or CASE_UPPER
     * @param array $exclude any array of keys whos nested array keys should be excluded
     * @param number $current the current key, comparied in excluded
     */
   /* protected function changeKeyCaseRecursive(array &$array, $case = CASE_LOWER, array $exclude = [], $current=0)
    {
        if(!in_array($current, $exclude, true)){
            $array = array_change_key_case($array, $case);
        }
        
        foreach ($array as $key=>&$value){
            if(is_array($value)){
                $this->changeKeyCaseRecursive($value, $case, $exclude, $key);
            }
        }
    }
    
    
    
    
    
    
      /*  //lowercase all the keys execpt those under 'namespaces'
        $this->changeKeyCaseRecursive($eJinn, CASE_LOWER, ['namespaces']);
        

        
        echo "===========================\n";
        print_r($eJinn);
        
        //lowercase all keys at this level
        /*$this->changeKeyCase($eJinn);
        
        //check for any unknown keys in our array
        $this->ckUnknownKeys($eJinn);
    
        //seperate
        $namespaces = $this->cutArrayKey('namespaces', $eJinn)
        
        //parse the reserved Error Codes at this level and remove
        
        
        //make sure we only have global data
        $this->ckAllowed($eJinn, self::$global, "in top level of config");
        
        foreach ($namespaces as $namespace => $config) {
            if (empty($conf)) continue; //empty namespace are ignored
            
            $this->changeKeyCase($config);
            
            //check for any keys outside our schema
            $this->ckUnknownKeys($config, "in Namespace[$namespace]");
            
            $config['namespace'] = $namespace;
            $config = array_merge(self::$global, $global, $config);
            
            //get and remove any protected Error Codes
            $this->preserveReservedCodes($conf);
            
            //parse each namespace
            $this->parseNamespace($namespace, $config, $global);

        }
        print_r( str_pad( " FINAL " , 60, '=', STR_PAD_BOTH)."\n");
        
        print_r( str_pad(str_pad( " Reserved " , 30, '-', STR_PAD_BOTH), 60, ' ', STR_PAD_BOTH)."\n");
        print_r($this->reserved);

        print_r( str_pad(str_pad( " Interfaces " , 30, '-', STR_PAD_BOTH), 60, ' ', STR_PAD_BOTH)."\n");
        print_r($this->interfaces);
        
        print_r( str_pad(str_pad( " Excpetions " , 30, '-', STR_PAD_BOTH), 60, ' ', STR_PAD_BOTH)."\n");
        print_r($this->exceptions);*/
     
  //  }
    
   /*protected function parseNamespace( $namespace, $conf, $global){ 
        //seperate interfaces
        /*$interfaces = false;
        if (isset($conf['interfaces']) && !empty($conf['interfaces'])) {
            $interfaces = array_unique($conf['interfaces']);
        }
        unset($conf['interfaces']);*//*
        
        //$interfaces = $this->cutArrayKey('interfaces', $array)
        
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
    /*protected function parseInterface($interface, array $global)
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
   /* protected function parseExceptions($exception, array $global)
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
    /*protected function parseEntity(array $entity, array $global)
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
     * set the reserved codes ( if present )
     * remove from origin by refrence
     *
     * @param array $conf
     * @throws MissingRequired
     */
   /* protected function preserveReservedCodes(array &$array = null)
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
    }*/
    
   /* protected function preserveReservedCodesRecursive(array &$array){
        
        $reservedRange = $this->cutArrayKey('reserverange', $array);
        
        
        $reserved = $this->cutArrayKey('reserved', $array);
        
        
        
    }
     
    /**
     * check if any keys are present that don't exist in our
     * global key array
     *
     * @param array $array
     */
  /*  protected function ckUnknownKeys(array $array)
    {
        $diff = array_diff_key($array, self::$schema);
        if (!empty($diff)) {
            $s = count($diff) > 1 ? "s" : "";
             
            die("Error: IN ".__FILE__." ON ".__LINE__); //@todo: throw new UnknownConfKey("Unknown Key{$s}\"".implode('","', array_keys($diff))."\"");
        }
    }*/
     
    /**
     *
     * @param array $array
     * @param array $allowed
     * @throws UnknownConfKey
     */
   /* protected function ckAllowed(array $array, array $allowed, $message = '')
    {
        $diff = array_diff_key($array, $allowed);
        if (!empty($diff)) {
            if (!empty($message)) {
                $message = ' '.$message;
            }
            
            $s = count($diff) > 1 ? "s" : "";
            
            die("Error: IN ".__FILE__." ON ".__LINE__); //@todo: throw new KeyNotAllowed("Key{$s} \"".implode('","', array_keys($diff))."\" not allowed{$message}");
        }
    }*/
    


      
    /**
     * create a hash from an object config
     *
     * @param array $array
     * @return string
     */
   /* protected function hashConfig(array $array)
    {
        //remove this for obvious reasons
        unset($array['hash']);
           
        return sha1(implode($this->removeNestedArrays($array)));
    }*/
       
    /**
    * Remove any nested arrays
    *
    * @param array $array
    * @return array
    */
   /* protected function removeNestedArrays(array $array)
    {
        return array_filter($array, function ($item) {
            //remove array items, these are not part of the items config
            return !is_array($item);
        });
    }*/
}
