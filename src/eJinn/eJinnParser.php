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
    protected $basePath;
    
    /**
     * list of permitted options
     *
     * @var array
     */
    protected $defaultOptions = [
        'forceunlock'       => false,
        'forcerecompile'    => false,
        'debug'             => ['dev'],  //"none" set to none in production
        'createpaths'       => false,
        'parseonly'         => false,
        'lockfile'          => 'ejinn.lock',
        'cachefile'         => 'ejinn.cache'
    ];
    
    /**
     * runtime options
     *
     * @var array
     */
    protected $options = [];
    
    /**
     * Keys that contain loclized data
     * @var array
     */
    protected $local = [
        "name"          => "",
        "code"          => false,
        'message'       => ""
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
        "author"        => "",
        "description"   => "",
        "package"       => "",
        "subpackage"    => "",
        "support"       => "",
        "version"       => "",
        "buildpath"     => "",
        "extends"       => "\Exception",
        "severity"      => E_ERROR,
        'impliments'    => [],
        "reserved"      => []
    ];
    
    /**
     *
     * @var array
     */
    protected $private = [
        'psr'                   => false,
        'namespace'             => '',
        'pathname'              => '',
        'ejinn:hash'            => '',
        'ejinn:buildversion'    => '',
        'ejinn:buildtime'       => '',
        'ejinn:pathname'        => '',
        'qname'                 => ''
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
        "buildversion"  => " * @eJinn:buildVersion %s",
        "buildtime"     => " * @eJinn:buildTime %s",
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
        'buildversion'    => '',
    ];
    
    /**
     * an array containing ($global, $contianers, $local)
     * @var array
     */
    protected $allKeys = [];
        
    /**
     * Ouput debug messages
     *
     * @var bool
     */
    protected $debug = [];
    
    /**
     *
     * @var array
     */
    protected $reserved = [];
    
    /**
     * keep only 0 and '0', not these
     * becuase this is in a recursive function
     * it may be better to have a property then
     * hardcoded array
     *
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
     * @var array
     */
    protected $files = [];
    
    /**
     *
     * @var string
     */
    protected $lockFile;
    
    /**
     *
     * @var string
     */
    protected $cacheFile;
    
    /**
     *
     * @param array $config - either an array config or a json config
     */
    public function __construct(array $config = null, $buildpath = null, array $options = [])
    {
        if ($config) {
            $this->parse($config, $buildpath, $options);
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
    public function getGlobal()
    {
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
    public function getContainers()
    {
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
    public function getLocal()
    {
        return $this->local;
    }
    
    /**
     * Return all possible keys exposed to the configuration
     *
     * @return array
     */
    public function getAllKeys()
    {
        if (!$this->allKeys) {
            $this->allKeys = array_merge($this->global, $this->containers, $this->local);
        }
        
        return $this->allKeys;
    }
    
    /**
     * Parse the eJinn config array
     *
     * @param array $config
     * @param string $buildpath - basepath to build off of ( typically the base path of the config )
     *
     */
    public function parse(array $config, $buildpath, array $options = [])
    {
        //reset on a new parse call
        $this->reset();
        
        //validate options
        $options = $this->recursiveArrayChangeKeyCase($options);
        $this->ckUnkownKeys("Options", $options, $this->defaultOptions);
        $this->options = array_replace($this->defaultOptions, $options);
        
        if (isset($this->options['debug'])) {
            $this->debug = array_map('strtolower', $this->options['debug']);
        }
        
        //validate the base buildpath, ingore createpaths on the basepath
        $this->ckBuildPath("Config Path", $buildpath, true);
        
        //normalize paths should end with "/" and use Unix style DS
        $this->basePath = rtrim(str_replace("\\", "/", $buildpath), "/")."/";
        
        //check if the process is locked
        if ($this->isLocked() && !$this->options['forceunlock']) {
            throw new \Exception("Process is locked for config {$this->basePath}");
        }
        //lock the process
        $this->lock();
        
        //lowercase config keys -> except children of namespace.
        $eJinn = $this->recursiveArrayChangeKeyCase($config, CASE_LOWER, ['namespaces']);

        //pre-process the array recursively
        $eJinn = $this->parseRecursive($eJinn);

        //clean and type check the reserved keys
        $this->reserved = array_unique($this->reserved);
        if (!empty($this->reserved) && !ctype_digit(implode($this->reserved))) {
            $this->debug($this->reserved);
            throw new \Exception("Reserved Error codes must be integers");
        }

        //Seperate the namespace container
        $namespaces = $this->extractArrayElement('namespaces', $eJinn);
        if (!$namespaces) {
            throw new \Exception("Namespaces element is required");
        }
        
        //normalize merge global
        $global = array_replace($this->global, $eJinn);
        
        //check for keys not allowed at this level
        $this->ckBannedKeys(
            "Global Teir",
            $global,
            $this->containers,
            $this->local,
            $this->private
        );
        
        //check for unkown keys at this level.
        $this->ckUnkownKeys("Global Tier", $global, $this->global);
               
        //continue parsing
        $this->parseNamespaces($namespaces, $global);
        
        //ck duplicate error codes & reserve error codes
        $usedCodes = array_column($this->exceptions, 'code');
        $this->ckDuplicateCodes($usedCodes);
        $this->ckReserveCodes($usedCodes);
        
        //Finished Parsing
        
        //Debugging
        $this->debug($this->files, 'showFiles');
        $this->debug($this->reserved, 'showReserved');
        $this->debug($this->interfaces, 'showInterfaces');
        $this->debug($this->exceptions, 'showException');
        $this->debug([
            'interfaces' => $this->interfaces,
            'exceptions' => $this->exceptions
        ], 'showEntities');
        
        $this->debug($this->options, 'dev');
        
        if (!$this->options['parseonly']) {
            $this->build();
        }
        
        $this->unlock();
    }
    
    /**
     * create the files
     */
    protected function build()
    {
        $entities = [];
        
        foreach ($this->interfaces as $qName => $interface) {
            $this->ckBuildPath($interface['name'], $interface['buildpath']);
        }
        
        foreach ($this->exceptions as $qName => $exceptions) {
            $this->ckBuildPath($interface['name'], $interface['buildpath']);
        }
    }
    
    
    //========================================================//
    //                  PROTECTED BUILDER
    //========================================================//
    
    
    
    
    //========================================================//
    //                  PROTECTED PARSERS
    //========================================================//
    
    /**
     * preform transforms on the eJinn array recursivly
     *
     * @param array $array pass by refrence
     * @param string $current
     */
    protected function parseRecursive(array $array, $current = null)
    {
        $internal = [];
        
        $this->preserveReservedCodes($array);

        foreach ($array as $key=>$value) {
            $key = (string)$key;
            
            if (substr($key, 0, 1) == '_') {
                //remove elements and their decendants with an _
                continue;
            }
            
            if (is_array($value)) {
                $value = $this->parseRecursive($value, $key); //recursive
            }
            
            $internal[$key] = $value;
        }
        
        return $internal;
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
            
            $exceptions = $this->extractArrayElement('exceptions', $config);

            if (empty($interfaces) && empty($exceptions)) {
                throw new \Exception("Namespace[$ns] must contain either interfaces or exceptions");
            }
                       
            if (empty($ns)) {
                $ns = '\\';
            }
 
            //check for keys not allowed at this level
            $this->ckBannedKeys(
                "Namespace[$ns]",
                $config,
                $this->containers,
                $this->local,
                $this->private
            );
            
            //check for unkown keys at this level.
            $this->ckUnkownKeys("Namespace[$ns]", $config, $this->global, ['namespace' => false]);
                      
            $namespace = $this->compact($global, $config, ['namespace' => $ns]);
            
            //$this->debug($namespace);

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
        foreach ($exceptions as $code => $exception) {
            $exception = $this->parseEntity($exception, $namespace);
            
            if (!is_int($code)) {
                throw new \Exception("Excetion[{$exception['namespace']}::{$exception['name']}] expected integer error code, given[{$code}]");
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
            $this->private
        );
        
        //combine the namespace and entity
        $entity = $this->compact($namespace, $entity);
                
        //parse the fully qualified name from the namespace and the name
        $entity = $this->parseName($entity);
        
        //parse the pathname
        $entity = $this->parsePath($entity);
        
        //add our build version
        $entity['ejinn:buildversion'] = $this->buildVersion;
        
        //add our build time
        $entity['ejinn:buildtime'] = $this->buildTime;
          
        //hash the entity for compile cache checking
        $entity['ejinn:hash'] = $this->hashEntityConfig($entity);

        return $entity;
    }
    
    /**
     * Parse an name and a namespace
     *
     * This normalizes namespaces and creates a fully qualifed class name
     *
     * @param array $entity
     * @param array $config
     */
    protected function parseName(array $entity)
    {
        $ns = "\\";
        
        //Parsed names cannot contain \\, ie. they must be relative paths
        if (false !== strpos($entity['name'], $ns)) {
            throw new \Exception("Entity name[{$entity['name']}] cannot contain a NS '$ns' IN ".__FILE__." ON ".__LINE__);
        }
        
        $entity['namespace'] = trim($entity['namespace'], $ns);
        
        if (!empty($entity['namespace'])) {
            $qName = $ns.$entity['namespace'].$ns.$entity['name'];
        } else {
            $qName = $ns.$entity['name'];
        }
        
        $entity['qualifiedname'] = $qName;
        return $entity;
    }
    
    /**
     *
     * @param array $parent
     * @param array $child
     */
    protected function parsePath($entity)
    {
        if (isset($entity['psr']) && $entity['psr'] == '0') {
            $filename = str_replace('_', '/', $entity['name']);
            //cant use the quilified name as the _ change is only in the name
            $filename = $entity['namespace'].'/'.$filename;
        } elseif (isset($entity['psr']) && $entity['psr'] == '4') {
            $filename = $entity['qualifiedname'];
        } else {
            $filename = $entity['name'];
        }
        
        $pathname = $entity['buildpath'] . $filename . '.php';
        
        
        
        //normalize to Unix style
        $pathname = str_replace("\\", "/", $pathname);
        
        //replace any run on '/' just in case
        $pathname = preg_replace('/\/{2,}/', '/', $pathname);
        
        //noralize to the file
        //$pathname = str_replace("/", DIRECTORY_SEPARATOR, $pathname);
        
        $entity['buildpath'] = dirname($pathname)."/";
        
        $this->files[] = $pathname;

        $entity['pathname'] = $pathname;
        return $entity;
    }
    
    //========================================================//
    //                  HELPERS
    //========================================================//
    
    /**
     * Compact 2 or more tiers
     *
     * inputs should be in the highest to lowest tiers.
     * Higher tiers will generally overwrite lower tiers
     *
     * @param array ...$arrays
     * @return array
     */
    protected function compact(array ...$arrays)
    {
        if (1 == ($len = count($arrays))) {
            return reset($arrays);
        } //requires 2 or more arrays

        $compact = [];
        
        $buildpath = $this->basePath;
        $psr = false;
        
        for ($i=0; $i<$len; $i++) {
            if (isset($arrays[$i]['buildpath'])) {
                $bp = $arrays[$i]['buildpath']; //localize
                //if it's an array then check for PSR
                if (is_array($bp)) {
                    if (!isset($bp['psr']) || count($bp['psr']) != 1 || !preg_match('/^(0|4)$/', $bp['psr'])) {
                        throw new \Exception("Invalid Buildpath: Array build path must be as follows ['psr'=>0] or ['psr'=>4]");
                    }
                    $psr = $bp['psr'];
                } else {
                    $bp = str_replace("\\", "/", $bp); //normalize the DS ( makes matching easier )
                    
                    //if not check if it's relative or absolute
                    if (preg_match('/^([a-zA-Z]:\/|\/)/', $bp)) {
                        //if the patch starts with / Unix Absolute, if it starts with [a-z]:/ such as c:/ windows absolute
                        $buildpath = $bp; //make sure it ends with /
                    } else {
                        //append relative paths
                        $buildpath .= $bp;
                    }
                }
            }
            
            $arrays[$i]['buildpath'] = rtrim($buildpath, "/"). "/"; //make sure it ends with a /
            
            if (!isset($arrays[$i]['psr']) && $psr !== false) {
                //don't overwrite psr - if it's present
                $arrays[$i]['psr'] = $psr;
            }
            $compact = array_replace($compact, $arrays[$i]);
        }
        
        return $compact;
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
        $this->basePath = '';
        $this->ckHashMap();
        $this->options = [];
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
        if ($reserved === false) {
            return;
        }
        
        if (!is_array($reserved)) {
            throw new \Exception("Expeted array for property reserved, given ".gettype($reserved));
        }
        
        foreach ($reserved as $reserve) {
            if (is_array($reserve)) {
                if (count($reserve) != 2) {
                    throw new \Exception("Nested reserved must contain exactly 2 elements");
                }
                $range = range(array_shift($reserve), array_shift($reserve));
                $this->reserved += array_combine($range, $range);
            } elseif (!in_array($reserve, $this->nonReserve, true)) {
                //do not add [false,null,''], strict check
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
     * hash the entities config ( for cacheing purposes )
     *
     * @param array $entity
     * @return string
     */
    protected function hashEntityConfig(array $entity)
    {
        //merge with the map, sets order fills in defaults
        $mapped = array_merge($this->hashMap, $entity);
        //intersect with the map, removes any extra properties from $mapped
        $mapped = array_intersect_key($mapped, $this->hashMap);
        $mapped['impliments'] = implode('|', $mapped['impliments']);
        
        return sha1('['.implode(']|[', $mapped).']');
    }
    
    /**
     * Change the cassing of array keys recursivly ( normalization )
     *
     * @param array $array the input array
     * @param string $case the case to change it to CASE_LOWER[default], or CASE_UPER
     * @param array $exclude an array of keys who's nested array should not have the case changed
     * @param string $current the current key, used for excluding the child keys
     */
    protected function recursiveArrayChangeKeyCase(array $array, $case = CASE_LOWER, array $exclude = [], $current = null)
    {
        if (!in_array($current, $exclude, true)) {
            //           $this->debug($current);
            $array = array_change_key_case($array, $case);
        }

        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = $this->recursiveArrayChangeKeyCase($value, $case, $exclude, $key); //recursive
            }
        }
        return $array;
    }
    
    //========================================================//
    //                  VALIDATORS
    //========================================================//
    
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
            
            throw new \Exception("Banned Key{$s} '".implode("', '", array_keys($banned))."' in $set");
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
            throw new \Exception("Unknown key{$s} '".implode("', '", array_keys($diff))."' in $set");
        }
    }
        
    /**
     * Check for used Error codes present in the Reserved list
     *
     * @param array $usedCodes
     */
    protected function ckReserveCodes(array $usedCodes)
    {
        $diff = array_intersect($usedCodes, $this->reserved);

        if (0 != ($len = count($diff))) {
            $s = ($len > 1) ? 's' : '';
            $excptionIdx = array_keys($this->exceptions);
            foreach ($diff as $k=>&$v) {
                $v = "{$excptionIdx[$k]}::$v";
            }
            throw new \Exception("Reserved Error Code{$s} used '".implode("','", $diff)."'");
        }
    }
    
    /**
     * Checked for Error Codes used more then once
     *
     *
     * @param array $usedCodes
     */
    protected function ckDuplicateCodes(array $usedCodes)
    {
        //check for duplicate error codes
        $unique = array_unique($usedCodes);
        $diff = array_diff_assoc($usedCodes, $unique);

        if (0 != ($len = count($diff))) {
            $s = ($len > 1) ? 's' : '';
            $excptionIdx = array_keys($this->exceptions);
            foreach ($diff as $k=>&$v) {
                $v = "{$excptionIdx[$k]}::$v";
            }
            throw new \Exception("Duplicate Error Code{$s} for '".implode("','", $diff)."'");
        }
    }
    
    /**
     * validate the hash map
     */
    protected function ckHashMap()
    {
        $this->ckUnkownKeys(
            'HashMap',
            $this->hashMap,
            $this->getAllKeys(),
            [
                "namespace"     => false,
                "qname"         => false,
                "impliments"    => false,
                "buildversion"  => false
            ]
        );
    }
    
    /**
     *
     * @param string $title path title for debugging and error reporting
     * @param string $path
     * @param string $ignoreOptions when createpaths is set we ignore missing dirs
     */
    protected function ckBuildPath($title, $path, $ignoreOptions = false)
    {
        if (false !== strrchr($path, '.php')) {
            throw new \Exception("Invalid buildpath {$title} $path. Contains filename.");
        }

        if (!$ignoreOptions && $this->options['createpaths']) {
            return true;
        }
        
        if (!file_exists($path)) {
            throw new \Exception("Path {$title} $path not found.");
        }
        if (!is_writable($path)) {
            throw new \Exception("Path {$title} $path is not writable.");
        }
        
        return true;
    }
    
    /**
     * Lock file can be set in options['lockfile'], this can be either a filename
     * which will use the basePath ( default build path ) or this can be a full path.
     *
     * @return string
     */
    public function getLockFile()
    {
        if (!$this->lockFile) {
            $oLockFile = str_replace('\\', '/', $this->options['lockfile']);
            
            if (empty($oLockFile)) {
                throw new \Exception('Option[lockFile] cannot be empty');
            }

            if (preg_match('/\//', $oLockFile)) {
                $this->ckBuildPath('Lock Path', dirname($oLockFile), true);
                $this->lockFile = $oLockFile;
            } else {
                $this->lockFile = $this->basePath.$oLockFile;
            }
        }
        
        return $this->lockFile;
    }
    
    /**
     * Check if the process is locked
     * @return boolean
     */
    public function isLocked()
    {
        $lockFile = $this->getLockFile();
        $this->debug("Is Locked: $lockFile", __FUNCTION__);
        if (file_exists($lockFile)) {
            return true;
        }
        return false;
    }
    
    /**
     * Lock the process
     * @throws \Exception
     */
    protected function lock()
    {
        $lockFile = $this->getLockFile();
        $this->debug("UnLock: $lockFile", __FUNCTION__);
        if (!@file_put_contents($lockFile, time())) {
            throw new \Exception("Unable to create lock file $lockfile");
        }
    }
    
    /**
     * Unlock the process
     */
    protected function unlock()
    {
        $lockFile = $this->getLockFile();
        $this->debug("Unlock: $lockFile", __FUNCTION__);
        @unlink($lockfile);
    }
    
    protected function loadCache()
    {
    }
    
    protected function saveCache()
    {
    }
    
    /**
     * simple debug function  ( mainly for development )
     *
     * @param string $message
     * @param mixed $key
     */
    protected function debug($message, $key = ['dev'])
    {
        if (!is_array($this->debug)) {
            return;
        }

        $trace = $this->debugTrace(1);
        
        
        if (!$key) {
            $key = [$trace['function']];
        } else {
            if (!is_array($key)) {
                $key = [$key];
            }
            //make sure the function is always a debug key
            if (!in_array($trace['function'], $key)) {
                $key[] = $trace['function'];
            }
        }
        
        $key = array_map('strtolower', $key);

        //print_r($key);
        
        if (!empty($this->debug) && !count(array_intersect($key, $this->debug))) {
            return;
        }

        //if(!empty($this->debug) && !in_array(strtolower($key),$this->debug)) return;

        $elapsed = number_format((microtime(true) - $this->buildTime), 5);
        $o = [];
        $o[] = str_pad(" ".__CLASS__." ", 100, "=", STR_PAD_BOTH);
        $o[] = str_pad("[{$elapsed}/s] in {$trace['file']} on {$trace['line']}", 100, " ", STR_PAD_BOTH);
        $o[] = str_pad(" {$trace['function']} ", 100, "=", STR_PAD_BOTH);
        $o[] = var_export($message, true);
        $o[] = str_pad("", 100, "-", STR_PAD_BOTH);
        
        echo implode("\n", $o)."\n\n";
    }
    
    
    /**
     * get the line this function was called from ( $offset )
     *
     * @param number $offset
     * @return array
     */
    public static function debugTrace($offset = 0)
    {
        ++$offset; //add one for this methods call
        
        //get the backtrace, pull only the top level and only as deep as we need to
        $trace = debug_backtrace(false|DEBUG_BACKTRACE_IGNORE_ARGS, $offset + 1);

        if (!$trace) {
            return [
               'file'       => __FILE__,
               'line'       => 'Unknown',
               'function'   => '{Unknown}'
           ];
        }
        
        //get the line from the previous function call, ie where $this->debug() was called
        $tbt = $trace[$offset];
        $tbt['line'] = $trace[$offset-1]['line'];
        return $tbt;
    }
}
