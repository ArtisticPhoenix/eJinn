<?php
namespace evo\ejinn;

use evo\ejinn\exception as E;

//use Evo\eJinn\Exception as E;

/**
 *
 * (c) 2016 Hugh Durham III
 *
 * For license information please view the LICENSE file included with this source code.
 *
 * @author HughDurham {ArtisticPhoenix}
 * @package Evo
 * @subpackage eJinn
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
    protected $buildVersion = '1.0.0';
    
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
        'cachefile'         => 'ejinn.cache',
        'uniqueexceptions'  => true,
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
        'implements'    => [],
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
        "description"           => " * %s\n *",
        "author"                => " * @author %s",
        "package"               => " * @package %s",
        "subpackage"            => " * @subpackage %s",
        "support"               => " * @link %s",
        "version"               => " * @varsion %s",
        "ejinn:buildversion"    => " * @eJinn:buildVersion %s",
        "ejinn:buildtime"       => " * @eJinn:buildTime %s",
    ];
     
    /**
     * an array containing ($global, $contianers, $local)
     * @var array
     */
    protected $allKeys = [];

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
     * @var string
     */
    protected $cacheHash;
    
    /**
     *
     * @var string
     */
    protected $interfaceTemplate;
    
    /**
     *
     * @var string
     */
    protected $exceptionTemplate;
    
    /**
     *
     * @var array
     */
    protected $introspectionCache = [];
    
    /**
     *
     * @param array $config - either an array config or a json config
     */
    public function __construct(array $config = null, $buildpath = null, array $options = [])
    {
        $this->interfaceTemplate = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'interface.tpl');
        $this->exceptionTemplate = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'exception.tpl');
        
        if ($config) {
            $this->parse($config, $buildpath, $options);
        }
    }
    
    /**
     * reset the class
     *
     * @todo update this
     *
     * The generator is a 2 step process
     * 1. Parsing - validates and compiles the config
     * 2. Building - outputs the interface and exception classes
     *
     */
    public function reset()
    {
        $this->reserved = [];
        $this->exceptions = [];
        $this->interfaces = [];
        $this->buildTime = microtime(true);
        $this->basePath = '';
        $this->options = [];
        $this->files = [];
        $this->lockFile = null;
        $this->cacheFile = null;
        $this->cacheHash = null;
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
     * set all options
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        //set defaults
        $this->options = $this->defaultOptions;
        
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }
    }
    
    /**
     * set a single option
     *
     * @param string $option
     * @param mixed $value
     */
    public function setOption($option, $value)
    {
        $option = strtolower($option);
        switch ($option) {
            case 'debug':
                if (!is_array($value)) {
                    $value = [$value];
                }
                //lowercase
                array_walk($value, function (&$item) {
                    $item = strtolower($item);
                });
            break;
            case 'lockfile':
            case 'cachefile':
                //normalize directory seperator
                $value = str_replace("\\", "/", $value);
           break;
        }
        
        if (!isset($this->defaultOptions[$option])) {
            throw new E\UnknownOption("Unknown option key '$option'");
        }
 
        $this->options[$option] = $value;
    }
    
    /**
     * return an options value, or return all options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * return a single option's value
     *
     * @param string $option - name of the option or null ( empty ) to return all options
     * @param mixed $silent - if false an exception is thrown when $option is not set, otherwise false is returned
     * @return mixed
     */
    public function getOption($option, $silent = false)
    {
        if (!isset($this->options[$option])) {
            if (!$silent) {
                throw new E\UnknownOption("Call to undefined option[$option]");
            }
            return false;
        }
        return $this->options[$option];
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
        
        //lowercase config keys -> except children of namespace.
        $eJinn = $this->recursiveArrayChangeKeyCase($config, CASE_LOWER, ['namespaces']);
        
        if (!isset($config['options'])) {
            $config['options'] = [];
        }
        
        $options = array_merge($config['options'], $options);
        
        //validate options
        $this->setOptions($options);
        
        unset($config['options']);
        
        //validate the base buildpath, ingore createpaths on the basepath
        $this->ckBuildPath("Config Path", $buildpath, true);
        
        //normalize paths should end with "/" and use Unix style DS
        $this->basePath = rtrim(str_replace("\\", "/", $buildpath), "/")."/";
        
        //check if the process is locked
        if ($this->isLocked()) {
            if (!$this->options['forceunlock']) {
                throw new E\ProcessLocked("Process is locked for config {$this->basePath}");
            }
            $this->debug("Force unlock bypassing lock file", 'isLocked');
        }
        
        //load and check the cache file for this config
        if ($this->loadAndCheckCache($config)) {
            if (!$this->options['forcerecompile']) {
                return false;
            }
            $this->debug("Force recompile bypassing cache", 'isLocked');
        }
        
        //lock the process
        $this->lock();
        


        //pre-process the array recursively
        $eJinn = $this->parseRecursive($eJinn);

        //clean and type check the reserved keys
        $this->reserved = array_unique($this->reserved);
        if (!empty($this->reserved) && !ctype_digit(implode($this->reserved))) {
            $this->debug($this->reserved);
            throw new E\InvalidDataType("Reserved Error codes must be integers");
        }

        //Seperate the namespace container
        $namespaces = $this->extractArrayElement('namespaces', $eJinn);
       
        if (!$namespaces) {
            throw new E\MissingRequired("Namespaces element is required");
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
            //save cache after building only
            $this->saveCache();
        }
        
        //unlock no matter if parse only or build
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
            $this->buildInterface($interface);
        }
        
        foreach ($this->exceptions as $qName => $exception) {
            $this->debug($qName, 'dev');
            
            $this->ckBuildPath($exception['name'], $exception['buildpath']);
            $this->buildException($exception);
        }
    }
    
    
    //========================================================//
    //                  PROTECTED BUILDER
    //========================================================//
    protected function buildDoc(array $conf)
    {
        $doc = [];
        
        foreach ($this->doc as $key=>$tpl) {
            if (isset($conf[$key])) {
                $doc[] = sprintf($tpl, $conf[$key]);
            }
        }
        
        return implode("\n", $doc);
    }
    
    /**
     *
     * @param array $interface
     */
    protected function buildInterface(array $interface)
    {
        $tpl = $this->interfaceTemplate;
        
        $doc = $this->buildDoc($interface);
        
        $namespace = empty($interface['namespace']) ? '' : "namespace {$interface['namespace']};";
        
        $name = $interface['name'];
        
        $pathname = $interface['pathname'];
        
        $tpl = str_replace([
           '{php}',
           '{namespace}',
           '{docblock}',
           '{name}'
        ], [
           '<?php',
           $namespace,
           $doc,
           $name
        ], $tpl);
        
        if (file_put_contents($pathname, $tpl)) {
            $this->debug("Created Interface {$name} At {$pathname}", [__function__, 'dev']);
        }
    }
    
    protected function buildException(array $exception)
    {
        $tpl = $this->exceptionTemplate;
        
        $exception['namespace'] = empty($exception['namespace']) ? '' : 'namespace '.ltrim($exception['namespace'], '\\').';';
        $exception['extends'] = '\\'.ltrim($exception['extends'], '\\');
        
        $name = $exception['name'];
        
        $pathname = $exception['pathname'];
        
        foreach ($exception['implements'] as &$implements) {
            $implements = '\\'.ltrim($implements, '\\');
            if (!interface_exists($implements)) {
                throw new E\UnknownInterface("Interface class {$implements} not found");
            }
        }
        
        $exception['implements'] = empty($exception['implements']) ? '' : ' implements '.implode(', ', $exception['implements']);
  
        //all exceptions must extend some base exception class
        $intro = $this->introspectExtendsConstruct($exception);
        $tpl = str_replace(
            [
            '{php}',
            '{construct_args}',
            '{parent_args}',
         ],
            [
             '<?php',
             $intro['construct_args'],
             $intro['parent_args'],
          ],
          $tpl
        );
        
        $exception['docblock'] = $this->buildDoc($exception);

        foreach ($exception as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            
            $tpl = str_replace('{'.$key.'}', $value, $tpl);
        }
        
        $tpl = preg_replace('/\{\w+\}/', '', $tpl);
        
        if (file_put_contents($pathname, $tpl)) {
            $this->debug("Created Exception {$name} At {$pathname}", [__function__, 'dev']);
        }
    }
    
    /**
     *
     * @param $extends
     * @throws \Exception
     * @return array
     */
    protected function introspectExtendsConstruct($exception)
    {
        $extends = $exception['extends'];
        
        $common_args = [
            'message'   => [
                'default' => ' = "{message}"'
             ],
            'code'      => [
                'default' => ' = {code}'
            ],
            'previous'  => [
                'type' => '\\Exception ',
                'default' => ' = null',
            ],
            'filename'  => [
                'default' => ' = null',
            ],
            'lineno'    => [
                'default' => ' = null',
            ],
            'severity'  => [
                'default' => ' = {severity}'
            ]
        ];
        
        if (!class_exists($extends)) {
            throw new E\UnknownClass("Extends class {$extends} not found");
        }
        
        if (!isset($this->introspectionCache[$extends])) {
            $construct_args = [];
            $parent_args = [];
            
            $Method = new \ReflectionMethod($extends, '__construct');
            
            $Args = $Method->getParameters();
            
            foreach ($Args as $Arg) {
                $export = \ReflectionParameter::export(
                    array(
                        $Arg->getDeclaringClass()->name,
                        $Arg->getDeclaringFunction()->name
                    ),
                    $Arg->name,
                    true
                );
                
                //$patt = '/\[\s(?:\<\w+\>\s)(?P<type>\w+\s)?(?P<arg>\$'.$Arg->name.'.*?)\s\]$/i';
                $patt = '/\[\s(?:\<\w+\>\s?)(?P<full>(?P<type>[\\\a-z0-9_]+)?(?:[^$]*)(?P<arg>\$'.$Arg->name.')(?P<default>\s=.+)?)\s]$/i';
                if (preg_match($patt, $export, $match)) {
                    $type = '';
                    if (!empty($match['type'])) {
                        $type = trim($match['type']);
                        if (strtolower($type) != 'array') {
                            $type = '\\'.$type;
                        }
                        $type .= ' ';
                    }
                    $arg = $match['arg'];
                    $default = empty($match['default']) ? '' : $match['default'];
                    
                    if (isset($common_args[$Arg->name])) {
                        if (empty($type) && isset($common_args[$Arg->name]['type'])) {
                            $type = $common_args[$Arg->name]['type'];
                        }
                        if (empty($default)) {
                            $default = $common_args[$Arg->name]['default'];
                        }
                    }
                        
                    $construct_args[] = $type.$arg.$default;
                    $parent_args[] = '$'.$Arg->name;
                } else {
                    echo htmlentities($patt)."\n";
                    $this->debug(htmlentities($export), 'dev');
                    throw new E\ParseError('Could not parse extends, introspection error');
                }
            }
            
            //cache it
            $this->introspectionCache[$extends] = [
                'construct_args' => implode(', ', $construct_args),
                'parent_args' => implode(', ', $parent_args),
            ];
        }
        return $this->introspectionCache[$extends];
    }
    
    protected function buildPath()
    {
    }
    
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
            
            $implements = [];
            
            $ns = trim($ns, "\\");
            
            $interfaces = $this->extractArrayElement('interfaces', $config);
            
            $exceptions = $this->extractArrayElement('exceptions', $config);

            if (empty($interfaces) && empty($exceptions)) {
                throw new E\MissingRequired("Namespace[$ns] must contain either interfaces or exceptions");
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
                //add defined interfaces to excpetions in this namespace
                $implements = $this->parseInterfaces($interfaces, $namespace);
            }
            
            if ($exceptions) {
                $namespace['implements'] = array_replace($namespace['implements'], $implements);
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
        $implements = [];
        foreach ($interfaces as $interface) {
            $interface = $this->parseEntity($interface, $namespace);
            unset($interface['implements']);
            $implements[] = $interface['qualifiedname'];
            
            $this->interfaces[$interface['qualifiedname']] = $interface;
        }
        return $implements;
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
                throw new E\InvalidDataType("Exception[{$exception['namespace']}::{$exception['name']}] expected integer error code, given[{$code}]");
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
            throw new E\ParseError("Entity name[{$entity['name']}] cannot contain a NS '$ns' IN ".__FILE__." ON ".__LINE__);
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
                        throw new E\ParseError("Invalid Buildpath: Array build path must be as follows ['psr'=>0] or ['psr'=>4]");
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
            throw new E\InvalidDataType("Expeted array for property reserved, given ".gettype($reserved));
        }
        
        foreach ($reserved as $reserve) {
            if (is_array($reserve)) {
                if (count($reserve) != 2) {
                    throw new E\ParseError("Nested reserved must contain exactly 2 elements");
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
            
            throw new E\ReservedKeyword("Reserved Key{$s} '".implode("', '", array_keys($banned))."' in $set");
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
            throw new E\UnknownKey("Unknown key{$s} '".implode("', '", array_keys($diff))."' in $set");
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
            throw new E\ReservedExceptionCode("Reserved Error Code{$s} used '".implode("','", $diff)."'");
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
        if (!$this->options['uniqueexceptions']) {
            return;
        }
        
        
        //check for duplicate error codes
        $unique = array_unique($usedCodes);
        $diff = array_diff_assoc($usedCodes, $unique);

        if (0 != ($len = count($diff))) {
            $s = ($len > 1) ? 's' : '';
            $excptionIdx = array_keys($this->exceptions);
            foreach ($diff as $k=>&$v) {
                $v = "{$excptionIdx[$k]}::$v";
            }
            throw new E\DuplicateExceptionCode("Duplicate Error Code{$s} for '".implode("','", $diff)."'");
        }
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
            throw new E\InvalidDataType("Invalid buildpath {$title} $path. Contains filename.");
        }

        if (!$ignoreOptions && $this->options['createpaths']) {
            return true;
        }
        
        if (!file_exists($path)) {
            throw new E\PathNotFound("Path {$title} $path not found.");
        }
        if (!is_writable($path)) {
            throw new E\PathNotWritable("Path {$title} $path is not writable.");
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
            $oLockFile = $this->options['lockfile'];
            
            if (empty($oLockFile)) {
                throw new E\InvalidDataType('Option[lockFile] cannot be empty');
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
        
        if (file_exists($lockFile)) {
            $this->debug("Config is Locked, with file $lockFile", 'isLocked');
            return true;
        }
        $this->debug("eJinn config is not locked", 'isLocked');
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
        
        $lockPath = dirname($lockFile);
        
        if (!is_dir($lockPath)) {
            throw new E\PathNotFound("Path not found $lockPath");
        }
        
        if (!is_writable($lockPath)) {
            throw new E\PathNotWritable("Path not writable $lockPath");
        }
        
        if (!@file_put_contents($lockFile, time())) {
            throw new E\CouldNotCreateFile("Unable to create lock file $lockfile");
        }
    }
    
    /**
     * Unlock the process
     */
    protected function unlock()
    {
        $lockFile = $this->getLockFile();
        $this->debug("Unlock: $lockFile", __FUNCTION__);
        unlink($lockFile);
    }
    
    /**
     *
     * @throws \Exception
     */
    protected function loadAndCheckCache($config)
    {
        $this->cacheHash = 'v{'.$this->buildVersion.'}::'.sha1(var_export($config, true));
        $this->debug($this->cacheHash, 'ConfigHash');
        
        $oCacheFile = $this->options['cachefile'];
        
        if (empty($oCacheFile)) {
            throw new E\InvalidDataType('Option[cacheFile] cannot be empty');
        }
        
        if (preg_match('/\//', $oCacheFile)) {
            $this->ckBuildPath('Cache Path', dirname($oCacheFile), true);
        } else {
            $oCacheFile = $this->basePath.$oCacheFile;
        }
        
        $this->cacheFile = $oCacheFile;

        $this->debug("Load cache file: {$this->cacheFile}", ['loadCache']);

        if (file_exists($this->cacheFile)) {
            $chachedHash = file_get_contents($this->cacheFile);
            
            if ($this->cacheHash == $chachedHash) {
                $this->debug("eJinn config is cached", 'isCached');
                return true;
            }
        }
        $this->debug("eJinn config is not cached", 'isCached');
        return false;
    }
    
    protected function saveCache()
    {
        file_put_contents($this->cacheFile, $this->cacheHash);
    }
    
    /**
     * simple debug function  ( mainly for development )
     *
     * @param string $message
     * @param mixed $key
     */
    protected function debug($message, $key = ['dev'])
    {
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
        
        if (!count(array_intersect($key, $this->options['debug']))) {
            return;
        }

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
