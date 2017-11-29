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
        if (!ctype_digit(implode('', $this->reserved))) {
            die("Reserved Error codes must be integers");
        }
        
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
            $this->ckUnkownKeys("Namespace[$ns]", $namespace, $this->global);

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
        
        if (!$reserved) {
            return;
        }
        
        if (!is_array($reserved)) {
            die("Reserved must be an array of Error Codes");
        }
   
        foreach ($reserved as $reserve) {
            if (is_array($reserve)) {
                if (count($reserve) != 2) {
                    die("Nested reserved must contain exactly 2 elements");
                }
                $range = range(array_shift($reserve), array_shift($reserve));
                $this->reserved += array_combine($range, $range);
            } else {
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
    
    /**
     * simple debug function  ( mainly for development )
     *
     * @param string $message
     * @param int $line __LINE__
     */
    protected function debug($message, $line, $title = '')
    {
        $elapsed = number_format((microtime(true) - $this->microtime), 5);
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
