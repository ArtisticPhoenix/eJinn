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
     * properties that have a Doc tag, ( or go in the doc comment )
     *
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
     * @param array $conf- either an array config or a json config
     */
    public function __construct(array $conf = null)
    {
        //print_r( str_pad( __METHOD__ , 60, '-', STR_PAD_BOTH)."\n");
        
        if (!self::$schema) {
            self::$schema = array_merge(self::$local, self::$global);
        }
        
        
        if ($conf) {
            $this->setConf($conf);
        }
    }
    
    /**
     *
     * @param array $conf
     */
    public function setConf(array $conf)
    {
        $this->parseConf($conf);
    }
    
    /**
     *
     */
    public function build()
    {
    }
    
    
    protected function parseConf(array &$conf)
    {
        //print_r( str_pad( __METHOD__ , 60, '-', STR_PAD_BOTH)."\n");
        
        //lower case all keys at this level
        $conf = array_change_key_case($conf);
        
        //check for any unknown array keys
        $this->ckUnknownKeys($conf, "in top level of config");
        
        //Check if namespaces exist, and then seperate
        if (!isset($conf['namespaces']) || empty($conf['namespaces'])) {
            die("Error: IN ".__FILE__." ON ".__LINE__); //@todo: throw new MissingRequired("Missing namespace key");
        }
        $namespaces = $conf['namespaces'];
        unset($conf['namespaces']);
        
        //parse the reserved Error Codes at this level and remove
        $this->preserveReserve($conf);
        
        //make sure we only have global data
        $this->ckAllowed($conf, self::$global, "in top level of config");
        
        //save this
        $global = $conf;
        
        foreach ($namespaces as $namespace => $conf) {
            //print_r( str_pad( ' FOREACH:NAMESPACE ' , 60, '=', STR_PAD_BOTH)."\n");
            if (empty($conf)) {
                continue;
            }
            
            $this->parseNamespace($namespace, $conf, $global);
        }
    }
    
    protected function parseNamespace( $namespace, $conf, $global){
        //lowercase keys
        $conf = array_change_key_case($conf);
        //check for unknown keys
        $this->ckUnknownKeys($conf, "in Namespace[$namespace]");
        
        $conf['namespace'] = $namespace;
        
        $conf = array_merge(self::$global, $global, $conf);
        
        $this->preserveReserve($conf);
        
        //seperate interfaces
        $interfaces = false;
        if (isset($conf['interfaces']) && !empty($conf['interfaces'])) {
            $interfaces = $conf['interfaces'];
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
        
        if ($interfaces) {
            $impliments = $this->parseInterfaces($interfaces, $conf);
        }
    }
    
    protected function parseInterfaces(array $interfaces, array $global)
    {
        print_r(str_pad(' '.__METHOD__.' ', 60, '=', STR_PAD_BOTH)."\n");
        //print_r($interfaces);
        $impliments = [];
        foreach ($interfaces as $interface) {
            if (!is_array($interface)) {
                $interface = ['name' => $interface];
            } else {
                $interface = array_change_key_case($interface);
                if (!isset($interface['name'])) {
                    die("Error: IN ".__FILE__." ON ".__LINE__); //@todo: throw new MissingRequired("Interfaces must include a name");
                }
                $this->ckUnknownKeys($conf, "in Namespace[$namespace]");
            }
            
            $interface = array_merge(self::$global, $global, $interface);
            
            $this->preserveReserve($interface);
            
            $interface['qName'] = $this->parseName($interface);
            
            $interface['hash'] = $this->hashConfig($interface);
            
            if (isset($this->interfaces[$interface['qName']])){
                
                die("Error: IN ".__FILE__." ON ".__LINE__);
            }
            
            $this->interfaces[$interface['qName']] = $interface;
            $impliments[] = $interface['qName'];

        }
        return $impliments;
    }
      
    /**
     * 
     * @param array $exceptions
     * @param array $global
     */
    protected function parseExceptions(array $exceptions, array $global)
    {
        print_r($exceptions);
    }
    
    /**
     * 
     * @param array $item
     * @return string
     */
    protected function parseName(array &$item){
        $ns = "\\";
        
        if( false !== strpos($item['name'], $ns)){
            //names cannot contain \\
            die("Error: IN ".__FILE__." ON ".__LINE__);
        }
        
        if(substr($item['name'], 0, 1) == '+'){
            $item['name'] = substr($item['name'], 1);
            $item['isExternal'] = true;
        }
        
        $item['namespace'] = trim($item['namespace'], $ns);
        
        if(!empty($item['namespace'])){
            return $ns.$item['namespace'].$ns.$item['name'];
        }else{
            return $ns.$item['name'];
        }
    }
    
    
    
    /**
     * create a fully qualified classname
     * \namespace\class
     *
     * @param string $namespace
     * @param string $class
     *
    protected function createQualifedName(&$item)
    {
        $ns = "\\";
        
        if()
        
        
        
        /*if (false !== strpos($item['name'], $ns)) {
            //if the name contanis any "\\" it's treated as the fully qualified name
            $qName = trim($item['name'], $ns);
            if (false !== strpos($qName, $ns)) {
                $name = ltrim(strrchr($qName, $ns), $ns);
                $namespace = $ns.trim(substr_replace($qName, "", -strlen($name)), $ns).$ns;
            } else {
                $name = $qName;
                $namespace = "";
            }
        } else {
            $namespace = trim($item['namespace'], $ns);
            $name = $item['name'];
            if (!empty($namespace)) {
                $qName = $ns.$name.$ns;
            } else {
                $qName = $ns.$namespace.$ns.name;
            }
        }
        $item['name'] = $name;
        $item['namespace'] = $namespace;
        return $qName;*/
    }
    
    
    /**
     * set the reserved codes ( if present )
     * remove from origin by refrence
     *
     * @param array $conf
     * @throws MissingRequired
     */
    protected function preserveReserve(array &$conf = null)
    {
        if (!$conf) {
            return;
        }
         
        print_r(str_pad(' '.__METHOD__.' ', 60, '=', STR_PAD_BOTH)."\n");
        print_r($conf);
         
        if (isset($conf['reserve']) && is_array($conf['reserve'])) {
            $this->reserved = array_merge($this->reserved, $conf['reserve']);
        }
     
        if (isset($conf['reserverange'])&& is_array($conf['reserverange'])) {
            if (2 != ($count = count($conf['reserverange']))) {
                die("Error: IN ".__FILE__." ON ".__LINE__); //@todo: throw new MissingRequired("key:reserveRange expects exactly 2 elements, give {$count}");
            }
            $range = range(array_pop($conf['reserverange']), array_pop($conf['reserverange']));
            $this->reserved = array_merge($this->reserved, $range);
        }
     
        unset($conf['reserve'],$conf['reserverange']);
    }
     
    /**
     * check if any keys are present that don't exist in our
     * global key array
     *
     * @param array $conf
     */
    protected function ckUnknownKeys(array $conf)
    {
        $diff = array_diff_key($conf, self::$schema);
        if (!empty($diff)) {
            $s = count($diff) > 1 ? "s" : "";
             
            die("Error: IN ".__FILE__." ON ".__LINE__); //@todo: throw new UnknownConfKey("Unknown Key{$s}\"".implode('","', array_keys($diff))."\"");
        }
    }
     
    /**
     *
     * @param array $conf
     * @param array $allowed
     * @throws UnknownConfKey
     */
    protected function ckAllowed(array $conf, array $allowed, $message = '')
    {
        $diff = array_diff_key($conf, $allowed);
        if (!empty($diff)) {
            if (!empty($message)) {
                $message = ' '.$message;
            }
            
            $s = count($diff) > 1 ? "s" : "";
            
            die("Error: IN ".__FILE__." ON ".__LINE__); //@todo: throw new KeyNotAllowed(
                "Key{$s} \"".implode('","', array_keys($diff))."\" not allowed{$message}"
            );
        }
    }
      
      
    /**
     * create a hash from an object config
     *
     * @param array $conf
     * @return string
     */
    protected function hashConfig(array $conf)
    {
        //remove this for obvious reasons
        unset($conf['hash']);
           
        return sha1(implode($this->removeNestedArrays($conf)));
    }
       
    /**
    * Remove any nested arrays
    *
    * @param array $conf
    * @return array
    */
    protected function removeNestedArrays(array $conf)
    {
        return array_filter($conf, function ($item) {
            //remove array items, these are not part of the items config
            return !is_array($item);
        });
    }
}
