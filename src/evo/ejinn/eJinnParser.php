<?php
namespace evo\ejinn;

use evo\exception as E;

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
     * Build version of this parser
     *
     * Placed in the compiled classes @ejinn:buildVersion doc tag
     * when the build version is changed this should force rebuild
     * of all compiled classes
     *
     * @var string
     */
    protected string $buildVersion = '2.1.0';
    
    /**
     * Build time, the time the class was compiled on
     * This is placed in the @eJinn:buildTime doc tag
     * Obviously we don't want to rebuild when the build
     * time changes, this is just for informational purposes
     *
     * @var float
     */
    protected float $buildTime;
    
    /**
     *
     * @var string
     */
    protected string $basePath;
    
    /**
     * list of permitted options for the parser
     * @var array
     */
    protected array $defaultOptions = [
        'forceUnlock'       => false,
        'forceRecompile'    => false,
        'debug'             => true,
        'createPaths'       => false,
        'parseOnly'         => false,
        'export'            => false,
        'lockFile'          => 'ejinn.lock',
        'cacheFile'         => 'ejinn.cache',
        'uniqueExceptions'  => true,
    ];

    /**
     * runtime options
     *
     * @var array
     */
    protected array $options = [];

    /**
     * Keys that contain localized data
     * @var array
     */
    protected array $local = [
        "name"          => "",
        "description"   => "",
        "code"          => false,
        'message'       => "",
      //  "line"          => "",
      //  "file"          => "",
        "extends"       => "",
        "implements"    => ""
    ];

    /**
     * these keys can only be used in "local"
     */
    protected array $onlyLocal = ["code", "message", "name", "severity", "line", "file"];

    /**
     * Keys that contain data applied to entities only
     * @var array
     */
    protected array $containers = [
        "namespaces"    => [],
        "interfaces"    => [],
        "exceptions"    => []
    ];

    /**
     * Keys that contain data applied globally
     * @var array
     */
    protected array $global = [
        "author"        => "",
        "description"   => "",
        "license"       => "",
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
    protected array $private = [
        'psr'                   => false,
        'namespace'             => '',
        'pathname'              => '',
        'ejinn:buildVersion'    => '',
        'ejinn:buildTime'       => '',
        'ejinn:pathname'        => '',
        'qname'                 => ''
    ];

    /**
     * Doc comment format (template)
     * @var array
     */
    protected array $doc = [
        "description"           => " * %s",
        "author"                => " * @author %s",
        "package"               => " * @package %s",
        "subpackage"            => " * @subpackage %s",
        "support"               => " * @link %s",
        "version"               => " * @version %s",
        "license"               => " * @license %s",
        "ejinn:buildVersion"    => " * @eJinn:buildVersion %s",
        "ejinn:buildTime"       => " * @eJinn:buildTime %s",
    ];

    /**
     * an array containing ($global, $containers, $local)
     * @var array
     */
    protected array $allKeys = [];

    /**
     * reserved code numbers
     * @var array
     */
    protected array $reserved = [];

    /**
     * keep only 0 and '0', not these
     * because this is in a recursive function
     * it may be better to have a property then
     * hardcoded array
     *
     * @var array
     */
    protected array $nonReserve = [null,'',false];

    /**
     *
     * @var array
     */
    protected array $exceptions = [];

    /**
     *
     * @var array
     */
    protected array $interfaces = [];

    /**
     * Constructor argument cache
     * @var array
     */
    protected array $introspectionCache = [
        'Error' => [
            'construct_args' => '$message = "{message}", $code = {code}, \Throwable $previous = null',
            'parent_args' => '$message, $code, $previous'
        ],'ErrorException' => [
            'construct_args' => '$message = "{message}", $code = {code}, $severity = {severity}, $filename =null, $lineno = null, \Throwable $previous = null',
            'parent_args' => '$message, $code, $severity, $filename, $lineno, $previous'
        ],'Exception' => [
            'construct_args' => '$message = "{message}", $code = {code}, \Throwable $previous = null',
            'parent_args' => '$message, $code, $previous'
        ]
    ];

    /**
     *
     * @var array
     */
    protected array $files = [];

    /**
     *
     * @var string
     */
    protected string $lockFile;

    /**
     *
     * @var string
     */
    protected string $cacheFile;

    /**
     *
     * @var string
     */
    protected string $cacheHash;

    /**
     *
     * @var string
     */
    protected string $interfaceTemplate;

    /**
     *
     * @var string
     */
    protected string $exceptionTemplate;

    /**
     * @param array|null $config
     * @param string|null $buildpath
     * @param array $options
     *
     */
    public function __construct(?array $config = null, ?string $buildpath = null, array $options = [])
    {
        $this->interfaceTemplate = <<<TPL
{php}
{namespace}

/**
 * (eJinn Generated File, do not edit directly)
{docblock}
 */
interface {name} {extends}{implements}
{
}
TPL;

        $this->exceptionTemplate = <<<TPL
{php}
{namespace}

/**
 * (eJinn Generated File, do not edit directly)
{docblock}
 */
class {name} extends {extends}{implements}
{

    /**
     * For easier access to the error code
     * @var int
     */
    const int ERROR_CODE = {code};

    /**
     *
     * @see {extends}::__construct()
     */
    public function __construct({construct_args})
    {
        parent::__construct(...func_get_args());
    }
}
TPL;
        if ($config) {
            $this->parse($config, $buildpath, $options);
        }
    }

    /**
     * reset the class

     * The generator is a 2-step process
     * 1. Parsing - validates and compiles the config
     * 2. Building - outputs the interface and exception classes
     * @return $this
     */

  public function reset(): self
    {
        $this->reserved     = [];
        $this->exceptions   = [];
        $this->interfaces   = [];
        $this->buildTime    = microtime(true);
        $this->basePath     = '';
        $this->options      = [];
        $this->files        = [];
        $this->lockFile     = '';
        $this->cacheFile    = '';
        $this->cacheHash    = '';
        return $this;
    }

    /**
     * Global keys can be placed at almost any level
     * These are generic values that are passed down
     * through the configuration structure
     * @example
     * 'buildpath' - all entities need a build path, and often they all use the same one
     *
     * @return array
     */
    public function getGlobal(): array
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
    public function getContainers(): array
    {
        return $this->containers;
    }

    /**
     * Local keys can only be used at the Entity level
     * these are the most specific configuration options
     * @example
     * 'name' - each entity has its own name
     *
     * @return array
     */
    public function getLocal(): array
    {
        return $this->local;
    }

    /**
     * Return all possible keys exposed to the configuration
     *
     * @return array
     */
    public function getAllKeys(): array
    {
        if (!$this->allKeys) {
            $this->allKeys = array_merge($this->global, $this->containers, $this->local);
        }

        return $this->allKeys;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options): self
    {
        //set defaults
        $this->options = $this->defaultOptions;
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }
        return $this;
    }

    /**
     * set a single option
     *
     * @param string $option
     * @param mixed $value
     * @return $this
     *
     * @throws E\OutOfBoundsException
     */
    public function setOption(string $option, mixed $value): self
    {
        switch ($option) {
            case 'lockFile':
            case 'cacheFile':
                //normalize directory separator
                $value = str_replace("\\", "/", $value);
           break;
        }

        if (!isset($this->defaultOptions[$option])) {
            throw new E\OutOfBoundsException("Unknown parser option key '$option'.");
        }

        $this->options[$option] = $value;
        return $this;
    }

    /**
     * return all options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get a single parser option's value
     *
     * @param string $option - name of the option or null ( empty ) to return all options
     * @param bool $silent - if false an exception is thrown when $option is not set, otherwise false is returned
     * @return mixed
     * @throws E\OutOfBoundsException
     */
    public function getOption(string $option, bool $silent = false) : mixed
    {
        if (!isset($this->options[$option])) {
            if (!$silent) {
                throw new E\OutOfBoundsException("Undefined option '$option'.");
            }
            return null;
        }
        return $this->options[$option];
    }

    /**
     *
     * @param array $config
     * @param string $buildpath
     * @param array $options
     * @return $this
     *
     * @throws E\TypeError
     * @throws E\RangeException
     * @throws E\LockoutException
     */
    public function parse(array $config, string $buildpath, array $options = []): self
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

        //validate the base buildpath, ignore create paths on the base path
        $this->ckBuildPath("Config Path", $buildpath, true);

        //normalize paths should end with "/" and use Unix style DS
        $this->basePath = rtrim(str_replace("\\", "/", $buildpath), "/")."/";

        //check if the process is locked
        if ($this->isLocked()) {
            if (!$this->options['forceUnlock']) {
                throw new E\LockoutException("Process is locked for config $this->basePath, use option 'forceUnlock'.");
            }
            $this->debug("Force unlock bypassing lock file");
        }

        //load and check the cache file for this config
        if ($this->loadAndCheckCache($config)) {
            if (!$this->options['forceRecompile']) {
                return $this;
            }
            $this->debug("Force recompile bypassing cache");
        }

        //lock the process
        $this->lock();

        //pre-process the array recursively
        $eJinn = $this->parseRecursive($eJinn);

        //clean and type check the reserved keys
        $this->reserved = array_unique($this->reserved);
        if (!empty($this->reserved) && !ctype_digit(implode($this->reserved))) {
            $this->debug($this->reserved);
            throw new E\TypeError("Reserved Error codes must be integers.");
        }

        //Separate the namespace container
        $namespaces = $this->extractArrayElement('namespaces', $eJinn);

        if (!$namespaces) {
            throw new E\RangeException("At least one Namespaces element is required.");
        }

        //normalize merge global
        $global = array_replace($this->global, $eJinn);

        //check for keys not allowed at this level
        $this->ckBannedKeys(
            "Global Tier",
            $global,
            $this->containers,
            $this->onlyLocal,
            $this->private
        );

        //check for unknown keys at this level.
        $this->ckUnknownKeys("Global Tier", $global, $this->global);

        //continue parsing
        $this->parseNamespaces($namespaces, $global);

        //ck duplicate error codes & reserve error codes
        $usedCodes = array_column($this->exceptions, 'code');
        $this->ckDuplicateCodes($usedCodes);
        $this->ckReserveCodes($usedCodes);
        //----> Finished Parsing

        //Debugging
        $this->debug($this->files);

        //$this->debug($this->interfaces);
        //$this->debug($this->exceptions);

        if (!$this->options['parseOnly']) {
            $this->build();
            //save cache after building only
            $this->saveCache();
        }

        if ($export = $this->options['export']) {
            $export = !is_file($export) ? $this->basePath."ejinn_export.php" : str_replace("\\","/",$export);

            if(!preg_match('/\.php$/', $export)){
                $export .= '.php';
            }

            $exceptions = var_export($this->exceptions, true);
            file_put_contents($export, "<?php\nreturn $exceptions;");
            $this->debug("Exported Exceptions to $export");
        }

        //unlock no matter if parse only or build
        $this->unlock();
        return $this;
    }

    /**
     * create the files
     * @return void
     */
    protected function build(): void
    {
        foreach ($this->interfaces as $interface) {
            $this->ckBuildPath($interface['name'], $interface['buildpath']);
            $this->buildInterface($interface);
        }

        foreach ($this->exceptions as $exception) {
            $this->ckBuildPath($exception['name'], $exception['buildpath']);
            $this->buildException($exception);
        }
    }

    //========================================================//
    //                  PROTECTED BUILDER
    //========================================================//
    /**
     * @param array $conf
     * @return string
     */
    protected function buildDoc(array $conf): string
    {
        $doc = [];
        foreach ($this->doc as $key=>$tpl) {
            if (isset($conf[$key])) {
                $value = $conf[$key];
                if('description' == $key){
                    if(90 < strlen($value)) {
                        $filter = [
                            '/[\r\n]/' => ' ',
                            '/\s{2,}/' => ' ',
                            '/^\s*|\s*$/' => '',
                        ];
                        
                        $value = preg_replace(array_keys($filter), $filter, $value);
                        $value = wordwrap($value, 90, "\n * ");
                    }
                    $value .= "\n * ";
                }
                $doc[] = sprintf($tpl, $value);
            }
        }
        return implode("\n", $doc);
    }

    /**
     * @param array $interface
     * @return void
     */
    protected function buildInterface(array $interface): void
    {
        $tpl = $this->interfaceTemplate;

        $doc = $this->buildDoc($interface);

        $namespace = empty($interface['namespace']) ? '' : "namespace {$interface['namespace']};";

        $name = $interface['name'];

        $extends = empty($interface['extends']) ? '' : "extends ".$interface['extends'];

        $pathname = $interface['pathname'];

        $tpl = str_replace([
           '{php}',
           '{namespace}',
           '{docblock}',
           '{name}',
           '{extends}',
           '{implements}'
        ], [
           '<?php',
           $namespace,
           $doc,
           $name,
           $extends,
            ''
        ], $tpl);

        if (file_put_contents($pathname, $tpl)) {
            $this->debug("Created Interface: $name At $pathname");
        }
    }

    /**
     * @param array $exception
     * @return void
     */
    protected function buildException(array $exception): void
    {
        $tpl = $this->exceptionTemplate;
        $pathname = $exception['pathname'];
        $exception['namespace'] = empty($exception['namespace']) ? '' : 'namespace '.ltrim($exception['namespace'], '\\').';';

        $name = $exception['name'];
        if($exception['implements'] && !is_array($exception['implements'])) $exception['implements'] = [$exception['implements']];
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

        $tpl = preg_replace('/\{\w+}/', '', $tpl);

        if (file_put_contents($pathname, $tpl)) {
            $this->debug("Created Exception: $name At $pathname");
        }
    }

    /**
     * @param array $exceptions
     * @return array
     */
    protected function introspectExtendsConstruct(array $exceptions): array
    {
        $extends = $exceptions['extends'];

        if(is_a($extends, \Error::class, true)){
            return $this->introspectionCache['Error'];
        }else if(is_a($extends, \ErrorException::class, true)){
            return $this->introspectionCache['ErrorException'];
        }else{
            return $this->introspectionCache['Exception'];
        }
    }

    //========================================================//
    //                  PROTECTED PARSERS
    //========================================================//
    /**
     * @param array $array
     * @return array
     */
    protected function parseRecursive(array $array): array
    {
        $internal = [];

        $this->preserveReservedCodes($array);

        foreach ($array as $key => $value) {
            $key = (string)$key;
            //remove elements and their descendants if they start with an '_' commented
            if (str_starts_with($key, '_')) continue;

            if (is_array($value)) $value = $this->parseRecursive($value); //recursive

            $internal[$key] = $value;
        }

        return $internal;
    }

    /**
     * @param array $namespaces
     * @param array $global
     * @return void
     *
     * @throws E\ParseError
     */
    protected function parseNamespaces(array $namespaces, array $global): void
    {
        foreach ($namespaces as $ns => $config) {
            if (empty($config)) {
                return;
            }

            $ns = trim($ns, "\\");

            $interfaces = $this->extractArrayElement('interfaces', $config);

            $exceptions = $this->extractArrayElement('exceptions', $config);

            if (empty($interfaces) && empty($exceptions)) {
                throw new E\ParseError("Namespace[$ns] must contain either interfaces or exceptions.");
            }

            if (empty($ns)) {
                $ns = '\\';
            }

            //check for keys not allowed at this level
            $this->ckBannedKeys(
                "Namespace[$ns]",
                $config,
                $this->containers,
                $this->onlyLocal,
                $this->private
            );

            //check for unknown keys at this level.
            $this->ckUnknownKeys("Namespace[$ns]", $config, $this->global, ['namespace' => false]);

            $namespace = $this->compact($global, $config, ['namespace' => $ns]);

            if($interfaces) $this->parseInterfaces($interfaces, $namespace);

            if ($exceptions) $this->parseExceptions($exceptions, $namespace);
        }
    }

    /**
     * @param array $interfaces
     * @param array $namespace
     * @return array
     */
    protected function parseInterfaces(array $interfaces, array $namespace): array
    {
        $implements = [];
        //$namespace['extends'] = []; //<---

        foreach ($interfaces as $interface) {
            $interface = $this->parseEntity($interface, $namespace, true);

            unset($interface['implements']);
            $implements[] = $interface['qualifiedName'];

            $this->interfaces[$interface['qualifiedName']] = $interface;
        }
        return $implements;
    }

    /**
     * Parse an Exception
     *
     * @param array $exceptions
     * @param array $namespace
     * @return void
     *
     * @throws E\ParseError
     */
    protected function parseExceptions(array $exceptions, array $namespace): void
    {
        foreach ($exceptions as $code => $exception) {
            $exception = $this->parseEntity($exception, $namespace);

            if (!is_int($code)) throw new E\TypeError("Exception[{$exception['namespace']}::{$exception['name']}] expected integer code, given[".getType($code)."]");

            if (!isset($exception['code'])) $exception['code'] = $code;

            $this->exceptions[$exception['qualifiedName']] = $exception;
        }
    }

    /**
     * Parse an Entity ( Interface or Exception )
     *
     * @param string|array $entity
     * @param array $namespace
     * @param bool $interface
     * @return array
     */
    protected function parseEntity(string|array $entity, array $namespace, bool $interface=false): array
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

        if($interface){
            unset(
                $namespace['extends'],
                $namespace['implements'],
                $namespace['reserved']
            );
        }

        //combine the namespace and entity
        $entity = $this->compact($namespace, $entity);

        //parse the fully qualified name from the namespace and the name
        $entity = $this->parseName($entity);

        //parse the pathname
        $entity = $this->parsePath($entity);

        //add our build version
        $entity['ejinn:buildVersion'] = $this->buildVersion;

        //add our build time
        $entity['ejinn:buildTime'] = $this->buildTime;

        return $entity;
    }

    /**
     * Parse a name and a namespace
     * This normalizes namespaces and creates a fully qualified class name
     *
     * @param array $entity
     * @return array
     *
     * @throws E\ParseError
     */
    protected function parseName(array $entity): array
    {
        $ns = "\\";

        //Parsed names cannot contain \\, i.e. they must be relative paths
        if (str_contains($entity['name'], $ns)) {
            throw new E\ParseError("Entity name[{$entity['name']}] cannot contain a NS '$ns' IN ".__FILE__." ON ".__LINE__);
        }

        $entity['namespace'] = trim($entity['namespace'], $ns);

        if (!empty($entity['namespace'])) {
            $qName = $ns.$entity['namespace'].$ns.$entity['name'];
        } else {
            $qName = $ns.$entity['name'];
        }

        $entity['qualifiedName'] = $qName;
        return $entity;
    }

    /**
     * @param array $entity
     * @return array
     */
    protected function parsePath(array $entity): array
    {
        if (isset($entity['psr']) && $entity['psr'] == '0') {
            $filename = str_replace('_', '/', $entity['name']);
            //cant use the qualified name as the _ change is only in the name
            $filename = $entity['namespace'].'/'.$filename;
        } elseif (isset($entity['psr']) && $entity['psr'] == '4') {
            $filename = $entity['qualifiedName'];
        } else {
            $filename = $entity['name'];
        }

        $pathname = $entity['buildpath'] . $filename . '.php';

        //normalize to Unix style
        $pathname = str_replace("\\", "/", $pathname);

        //replace any run on '/' just in case
        $pathname = preg_replace('/\/{2,}/', '/', $pathname);

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
     * @param array ...$arrays - one or more arrays to compact
     * @return array
     */
    protected function compact(array ...$arrays): array
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
                    if (
                        !isset($bp['psr']) ||
                        (is_array($bp['psr']) && count($bp['psr']) != 1) ||
                        (!is_array($bp['psr']) && !preg_match('/^([04])$/', $bp['psr']))
                    ) {
                        throw new E\RangeException("Invalid Buildpath: Array build path must be as follows ['psr'=>0] or ['psr'=>4]");
                    }
                    $psr = $bp['psr'];
                } else {
                    $bp = str_replace("\\", "/", $bp); //normalize the DS ( makes matching easier )

                    //if not, check if its relative or absolute
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
     * Separate out and save any Reserved Error codes.
     *
     * @param array|null $array
     * @return void
     *
     * @throws E\TypeError
     * @throws E\LengthException
     */
    protected function preserveReservedCodes(array &$array = null): void
    {
        $reserved = $this->extractArrayElement('reserved', $array);

        //ignore if reserved is empty
        if ($reserved === false) {
            return;
        }

        if (!is_array($reserved)) {
            throw new E\TypeError("Expected array for property 'reserved', given '".gettype($reserved)."'.");
        }

        foreach ($reserved as $reserve) {
            if (is_array($reserve)) {
                if (count($reserve) != 2) {
                    throw new E\LengthException("Nested reserved codes must contain exactly 2 elements.");
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
     * the original array is also modified by removing the item
     *
     * @param string $key
     * @param array $array
     * @return mixed - false if the item is not found
     */
    protected function extractArrayElement(string $key, array &$array): mixed
    {
        if (!isset($array[$key])) {
            return false;
        }

        $item = $array[$key];
        unset($array[$key]);
        return $item;
    }

    /**
     * Change the casing of array keys recursively ( normalization )
     *
     * @param array $array the input array
     * @param int $case the case to change it to CASE_LOWER[default], or CASE_UPPER
     * @param array $exclude an array of keys who's nested array should not have the case changed
     * @param string|null $current the current key, used for excluding the child keys
     * @return array
     */
    protected function recursiveArrayChangeKeyCase(
        array $array,
        int $case = CASE_LOWER,
        array $exclude = [],
        ?string $current = null
    ): array {
        if (!in_array($current, $exclude, true)) {
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
     * @param string $set
     * @param array $input
     * @param array ...$control
     * @return void
     */
    protected function ckBannedKeys(string $set, array $input, array ...$control): void
    {
        $diff = array_diff_key($input, ...$control);
        if (count($diff) != count($input)) {
            $banned = array_diff_key($input, $diff);
            $s = count($banned) > 1 ? 's' : '';

            throw new E\TypeError("Using reserved Key$s '".implode("', '", array_keys($banned))."' in $set");
        }
    }

    /**
     * check for unknown keys
     * check that input keys are present in all controls
     *
     * @param string $set identifier for the level
     * @param array $input
     * @param array $control
     */
    protected function ckUnknownKeys(string $set, array $input, array ...$control): void
    {
        $diff = array_diff_key($input, ...$control);
        if (!empty($diff)) {
            $s = count($diff) > 1 ? 's' : '';
            throw new E\OutOfBoundsException("Unknown key$s '".implode("', '", array_keys($diff))."' in $set");
        }
    }

    /**
     * @param array $usedCodes
     * @return void
     */
    protected function ckReserveCodes(array $usedCodes): void
    {
        $diff = array_intersect($usedCodes, $this->reserved);

        if (0 != ($len = count($diff))) {
            $s = ($len > 1) ? 's' : '';
            $exceptionIdx = array_keys($this->exceptions);
            foreach ($diff as $k=>&$v) {
                $v = "$exceptionIdx[$k]::$v";
            }
            throw new E\RangeException("Reserved Error Code$s used '".implode("','", $diff)."'");
        }
    }

    /**
     * Check for Error Codes used more than once
     *
     * @param array $usedCodes
     * @return void
     *
     * @throws E\RangeException
     */
    protected function ckDuplicateCodes(array $usedCodes): void
    {
        if (!$this->options['uniqueExceptions']) {
            return;
        }

        //check for duplicate error codes
        $unique = array_unique($usedCodes);
        $diff = array_diff_assoc($usedCodes, $unique);

        if (0 != ($len = count($diff))) {
            $s = ($len > 1) ? 's' : '';
            $exceptionIdx = array_keys($this->exceptions);
            foreach ($diff as $k=>&$v) {
                $v = "$exceptionIdx[$k]::$v";
            }
            throw new E\RangeException("Duplicate Error Code$s for '".implode("','", $diff)."'");
        }
    }

    /**
     * @param string $title
     * @param string $path
     * @param bool $ignoreOptions
     * @return bool
     */

    /**
     * @param string $title
     * @param string $path
     * @param bool $ignoreOptions
     * @return bool
     *
     * @throws E\InvalidArgumentException
     * @throws E\BadDirException
     * @throws E\WritableDirException
     */
    protected function ckBuildPath(string $title, string $path, bool $ignoreOptions = false): bool
    {
        if (false !== strrchr($path, '.php')) {
            throw new E\InvalidArgumentException("Invalid buildpath $title $path. Contains filename.");
        }

        if (!$ignoreOptions && $this->options['createPaths']) {
            return true;
        }

        if (!file_exists($path)) {
            throw new E\BadDirException("Path $title $path not found.");
        }
        if (!is_writable($path)) {
            throw new E\WritableDirException("Path $title $path is not writable.");
        }

        return true;
    }

    /**
     * @return string
     *
     * @throws E\ValueError
     */
    public function getLockFile(): string
    {
        if (!$this->lockFile) {
            $oLockFile = $this->options['lockFile'];

            if (empty($oLockFile)) {
                throw new E\ValueError('Option[lockFile] cannot be empty');
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
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        $lockFile = $this->getLockFile();

        if (file_exists($lockFile)) {
            return true;
        }

        return false;
    }

    /**
     * @return void
     */
    protected function lock(): void
    {
        $lockFile = $this->getLockFile();

        $lockPath = dirname($lockFile);

        if (!is_dir($lockPath))  throw new E\BadDirException("Path not found $lockPath");
        if (!is_writable($lockPath)) throw new E\WritableDirException("Path not writable $lockPath");
        if (!@file_put_contents($lockFile, time())) throw new E\BadFileException("Unable to create lock file $lockFile");
    }

    /**
     * @return void
     */
    protected function unlock(): void
    {
        $lockFile = $this->getLockFile();
        $this->debug("Unlock: $lockFile");
        unlink($lockFile);
    }

    /**
     * @param array $config
     * @return bool
     */
    protected function loadAndCheckCache(array $config): bool
    {
        $this->cacheHash = 'v{'.$this->buildVersion.'}::'.sha1(var_export($config, true));

        $oCacheFile = $this->options['cacheFile'];
        
        if (empty($oCacheFile)) {
            throw new E\ValueError('Option[cacheFile] cannot be empty');
        }
        
        if (preg_match('/\//', $oCacheFile)) {
            $this->ckBuildPath('Cache Path', dirname($oCacheFile), true);
        } else {
            $oCacheFile = $this->basePath.$oCacheFile;
        }
        
        $this->cacheFile = $oCacheFile;

        if (file_exists($this->cacheFile)) {
            if ($this->cacheHash == file_get_contents($this->cacheFile)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return void
     */
    protected function saveCache(): void
    {
        file_put_contents($this->cacheFile, $this->cacheHash);
    }
    
    /**
     * @param mixed $message
     * @return void
     */
    protected function debug(mixed $message) : void
    {
        if(!$this->options['debug']) return;

        $trace = $this->debugTrace(1);

        $elapsed = number_format((microtime(true) - $this->buildTime), 5);
        $o = [];
        $o[] = str_pad(" ".__CLASS__." ", 100, "=", STR_PAD_BOTH);
        $o[] = str_pad("[$elapsed/s] in {$trace['file']} on {$trace['line']}", 100, " ", STR_PAD_BOTH);
        $o[] = str_pad(" {$trace['function']} ", 100, "=", STR_PAD_BOTH);
        $o[] = var_export($message, true);
        $o[] = str_pad("", 100, "-", STR_PAD_BOTH);
        
        echo implode("\n", $o)."\n\n";
    }

    /**
     * get the line this function was called from ( $offset ) for debugging
     *
     * @param int $offset
     * @return array
     */
    public static function debugTrace(int $offset = 0): array
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