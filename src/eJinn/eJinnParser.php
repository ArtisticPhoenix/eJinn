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
final class eJinnParser
{
    
    /**
     * Build versison of this parser
     * 
     * Placed in the compiled classes @ejinn:buildVersion doc tag
     * when the build version is changed this should force rebuild
     * of all compiled classes
     * 
     * @var string
     */
    protected $buildVersion = '0.0.1';
    
    /**
     * Build time, the time the class was compiled on
     * This is placed in the @eJinn:buildTime doc tag
     * Obvioulsy we don't want to rebuild when the build
     * time changes
     * 
     * @var double
     */
    protected $buildTime;
    
    /**
     * 
     * @var string
     */
    protected $buildPath;
    
    /**
     * Keys that contain loclized data
     * @var array
     */
    protected $local = [
        "name"          => false,
        "code"          => false,
        'message'       => false
    ];
    
    /**
     * Keys that contain data appled to entities only
     * @var array
     */
    protected $containers = [
        "namespaces"    => [],
        "interfaces"    => [],
        "exceptions"    => []
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
        'impliments'    => [],
        "reserved"      => []
    ];
      
    /**
     * Doc comment format (template)
     * @var array
     */
    protected $doc = [
        "author"        => " * @author %s",
        "description"   => " * %s",
        "package"       => " * @package %s",
        "subpackage"    => " * @subpackage %s",
        "support"       => " * @link %s",
        "version"       => " * @varsion %s",
        "buildVersion"  => " * @eJinn:buildVersion %s",
        "buildTime"     => " * @eJinn:buildTime %s",
        "hash"          => " * @eJinn:hash %s"
    ];
    
    /**
     * Array of config properties used for the config hash
     * placed in the @eJinn:hash doc tag. This insures that 
     * the order and properties used in the hash will not change
     * witout our knowing it.
     * 
     * @var array
     */
    protected $hashMap =[
        'author'          => '',
        'description'     => '',
        'package'         => '',
        'subpackage'      => '',
        'support'         => '',
        'version'         => '',
        'buildpath'       => '',
        'extends'         => '',
        'severity'        => '',
        'impliments'      => [],
        'namespace'       => '',
        'name'            => '',
        'code'            => '',
        'buildVersion'    => '',
    ];
    
    
    /**
     *
     * @var string
     */
    protected $debug = true;
    
    /**
     *
     * @var array
     */
    protected $reserved = [];
    
    /**
     * keep only 0 and '0', not these
     * @var array
     */
    protected $nonReserve = [null,'',false];
    
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
    public function __construct(array $config = null, $buildpath = null)
    {
        if ($config) {
            $this->parse($config, $buildpath);
        }
    }
    
    /**
     * Global keys can be placed at almost any level
     * These are generic values that are passed down
     * through the configuration structure
     * @example
     * 'buildpath' - all entities need a build path and often they all use the same one
     * 
     * @return array
     */
    public function getGlobal(){
        return $this->global;
    }
    
    /**
     * Container keys are place at only the top level, and the namespace level
     * These contain other parseable elements.
     * @example
     * 'namespace' - these contain the namespace blocks
     * 
     * @return array
     */
    public function getContainers(){
        return $this->containers;
    }
    
    /**
     * Local keys can only be used at the Entity level
     * these are the most specific configuration options
     * @example
     * 'name' - each entity has it's own name
     * 
     * @return array
     */
    public function getLocal(){
        return $this->local;
    }
    
    /**
     * Return all possible keys exposed to the configuration 
     *
     * @return array
     */
    public function getAllKeys(){
        return array_merge($this->global, $this->containers, $this->local);
    }
    
    /**
     * reset the class
     * 
     * The generator is a 2 step process
     * 1. Parsing - validates and compiles the config
     * 2. Building - outputs the interface and exception classes
     * 
     */
    protected function reset()
    {
        $this->reserved = [];
        $this->exceptions = [];
        $this->interfaces = [];
        $this->buildTime = microtime(true); 
        $this->validateHashMap();   
    }
    
    /**
     * create the files
     */
    public function build()
    {
    }
    
    /**
     * Parse the eJinn config array
     *
     * @param array $eJinn
     */
    public function parse(array $eJinn, $buildpath)
    {
        
        $this->reset();
        
        //pre-process the array recursively
        $this->parseRecursive($eJinn);
        
        //clean and type check the reserved keys
        $this->reserved = array_unique($this->reserved);
        if (!ctype_digit(implode('', $this->reserved))) {
            die("Reserved Error codes must be integers");
        }

        //Seperate the namespace container
        $namespaces = $this->extractArrayElement('namespaces', $eJinn);
        if (!$namespaces) {
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
        
        //ck duplicate error codes & reserve error codes
        $usedCodes = array_column($this->exceptions,'code');
        $this->chDuplicateCodes($usedCodes);
        $this->chReserveCodes($usedCodes);
        
        //Finished Parsing
        
        //Debugging
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
        if ($current != 'namespaces') {
            $array = array_change_key_case($array, CASE_LOWER);
        }
        
        $this->preserveReservedCodes($array);

        foreach ($array as $key=>&$value) {
            if (is_array($value)) {
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
    protected function parseNamespaces(array $namespaces, array $global)
    {
        foreach ($namespaces as $ns => $config) {
            if (empty($config)) {
                return;
            }
            
            $impliments = [];
            
            $ns = trim($ns, "\\");
            
            $interfaces = $this->extractArrayElement('interfaces', $config);
            
            $exceptions =  $this->extractArrayElement('exceptions', $config);
            
            if (empty($interfaces) && empty($exceptions)) {
                die("Namespace[$ns] must contain either interfaces or exceptions");
            }
            
            //normalize merge global
            $namespace = array_replace($global, $config, ['namespace' => $ns]);
            
            //$this->debug($namespace, __LINE__);
            
            //check for keys not allowed at this level
            $this->ckBannedKeys("Namespace[$ns]", $namespace, $this->containers, $this->local);
            
            //check for unkown keys at this level.
            $this->ckUnkownKeys("Namespace[$ns]", $namespace, $this->global, ['namespace' => false]);

            if ($interfaces) {
                $impliments = $this->parseInterfaces($interfaces, $namespace);
            }
            
            if ($exceptions) {
                $namespace['impliments'] = array_replace($namespace['impliments'], $impliments);
                $this->parseExceptions($exceptions, $namespace);
            }
        }
    }
    
    /**
     * Parse an Interfaces block
     *
     * @param array $interfaces
     * @param array $namespace
     * @return array an array of interfaces (fully qualified names)
     */
    protected function parseInterfaces(array $interfaces, array $namespace)
    {
        $impliments = [];
        
        //$this->debug($namespace, __LINE__);
        
        foreach ($interfaces as $interface) {
            $interface = $this->parseEntity($interface, $namespace);
            unset($interface['impliments']);
            $impliments[] = $interface['qualifiedname'];
            
            $this->interfaces[$interface['qualifiedname']] = $interface;
        }
        
        
        return $impliments;
    }
    
    /**
     * parse an Excptions block
     *
     * @param array $exceptions
     * @param array $namespace
     */
    protected function parseExceptions(array $exceptions, array $namespace)
    {
        
        //$this->debug($namespace, __LINE__);
        
        foreach ($exceptions as $code => $exception) {
            $exception = $this->parseEntity($exception, $namespace);
            
            if (!is_int($code)) {
                die("Excetion[{$exception['namespace']}::{$exception['name']}] expected integer error code, given[{$code}]");
            }

            if (!isset($exception['code'])) {
                $exception['code'] = $code;
            }
            
            $this->exceptions[$exception['qualifiedname']] = $exception;
        }
    }
    
    /**
     * Parse an Entity ( Interface or Exception )
     *
     * @param mixed $entity
     * @param array $namespace
     * @return array
     */
    protected function parseEntity($entity, array $namespace)
    {
        if (!is_array($entity)) {
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
        
        $entity['eJinn:buildVersion'] = $this->buildVersion;
        $entity['eJinn:buildTime'] = $this->buildTime;
          
        //hash the entity for compile checking
        $entity['eJinn:hash'] = $this->hashEntityConfig($entity);
        
        
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
    protected function ckBannedKeys($set, array $input, array ...$control)
    {
        $diff = array_diff_key($input, ...$control);
        if (count($diff) != count($input)) {
            $banned = array_diff_key($input, $diff);
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
    protected function ckUnkownKeys($set, array $input, array ...$control)
    {
        $diff = array_diff_key($input, ...$control);
        if (!empty($diff)) {
            $s = count($diff) > 1 ? 's' : '';
            die("Unknown key{$s} '".implode("', '", array_keys($diff))."' in $set");
        }
    }
    
    /**
     * Seperate out and save any Reserved Error codes.
     *
     * @param array $array
     */
    protected function preserveReservedCodes(array &$array = null)
    {
        $reserved = $this->extractArrayElement('reserved', $array);
        
        //ignore if reserved is empty
        if ($reserved === false) return;

        if (!is_array($reserved)) {
            die("Expeted array for property reserved, given ".gettype($reserved));
        }
   
        foreach ($reserved as $reserve) {
            if (is_array($reserve)) {
                if (count($reserve) != 2) {
                    die("Nested reserved must contain exactly 2 elements");
                }
                $range = range(array_shift($reserve), array_shift($reserve));
                $this->reserved += array_combine($range, $range);
            } else if( !in_array($reserve, $this->nonReserve, true) ){
                //do not add [false,null,''], strict check
                $this->reserved[$reserve] = $reserve;
            }
        }
    }
    
    /**
     * Check for used Error codes present in the Reserved list
     * 
     * @param array $usedCodes
     */
    protected function chReserveCodes(array $usedCodes){
        $diff = array_intersect($usedCodes, $this->reserved);

        if(0 != ( $len = count($diff))){
            $s = ($len > 1) ? 's' : '';
            $excptionIdx = array_keys($this->exceptions);
            foreach ($diff as $k=>&$v){
                $v = "{$excptionIdx[$k]}::$v";
            }
            die("Reserved Error Code{$s} used '".implode("','", $diff)."'");
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
    protected function extractArrayElement($key, array &$array)
    {
        if (!isset($array[$key])) {
            return false;
        }
        
        $item = $array[$key];
        unset($array[$key]);
        return $item;
    }
    
    
    /**
     * Checked for Error Codes used more then once
     * 
     * @param array $usedCodes
     */
    protected function chDuplicateCodes(array $usedCodes){       
        //check for duplicate error codes
        $unique = array_unique($usedCodes);       
        $diff = array_diff_assoc($usedCodes, $unique);

        if(0 != ( $len = count($diff))){
            $s = ($len > 1) ? 's' : '';
            $excptionIdx = array_keys($this->exceptions);
            foreach ($diff as $k=>&$v){
                $v = "{$excptionIdx[$k]}::$v";
            }
            die("Duplicate Error Code{$s} for '".implode("','", $diff)."'");
        }
    }
    
    /**
     * Parse an name and a namespace
     *
     * This normalizes namespaces and creates a fully qualifed class name
     *
     * @param array $entity
     * @param array $config
     */
    protected function parseName(array &$entity)
    {
        $ns = "\\";
        
        //Parsed names cannot contain \\, ie. they must be relative paths
        if (false !== strpos($entity['name'], $ns)) {
            die("Entity name[{$entity['name']}] cannot contain a NS '$ns' IN ".__FILE__." ON ".__LINE__);
        }
        
        $entity['namespace'] = trim($entity['namespace'], $ns);
        
        if (!empty($entity['namespace'])) {
            $qName = $ns.$entity['namespace'].$ns.$entity['name'];
        } else {
            $qName = $ns.$entity['name'];
        }
        
        $entity['qualifiedname'] = $qName;
    }
    

    
    protected function hashEntityConfig(array $entity){
        //merge with the map, sets order fills in defaults
        $mapped = array_merge($this->hashMap, $entity);
        //intersect with the map, removes any extra properties from $mapped
        $mapped = array_intersect_key($mapped, $this->hashMap);
        $mapped['impliments'] = implode('|', $mapped['impliments']);
        
        return sha1('['.implode(']|[', $mapped).']');
    }
    
    protected function validateHashMap(){
        
    }
    
    /**
     * simple debug function  ( mainly for development )
     *
     * @param string $message
     * @param int $line __LINE__
     */
    protected function debug($message, $line, $title = '')
    {
        $elapsed = number_format((microtime(true) - $this->buildTime), 5);
        $o = [];
        $o[] = str_pad(" ".__CLASS__." ", 100, "*", STR_PAD_BOTH);
        $o[] = str_pad("[{$elapsed}/s] in ".__FILE__." on {$line}", 100, " ", STR_PAD_BOTH);
        $o[] = str_pad(" {$title} ", 100, "-", STR_PAD_BOTH);
        $o[] = var_export($message, true);
        $o[] = str_pad("", 100, "=", STR_PAD_BOTH);
        
        if ($this->debug) {
            echo implode("\n", $o)."\n\n";
        }
    }
}
