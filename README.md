 eJinn (Beta)
 =====
The Exception Genie (PHP v5.6+)
-------------------

### Overview ###
 * Do you like having one exception code per exception class? 
 * Do you like using interfaces to group exception classes togather?
 * Do you like having error codes that are unique in your whole library, or project?

 * Are you tired of keeping track of all those pesky exception codes?
 * Are you tired of creating those boring boilerplate exception and interface classes?
 * Are you tired of searching for the approrate exception, because you can't remember if you made one for this or that error?
 
If you answered _Yes_ to any of the above then __eJinn__ may be just what you need.  __eJinn__ builds excpetion classes and interfaces based on a simple and flexable configuration file. __eJinn__ allows you to orginize all your _exception classes_ and _error codes_ in a logically based and easy to read configuration.  

__eJinn__ builds _PHP_ exception and interface class files for you.

An example of an **eJinn** configuration ( as a _PHP_ array).
```php
    return [
        "author"        => "ArtisticPhoenix",
        "buildpath"     => ["psr"=>4],
        "description"   => "eJinn The Exception Genie",
        "package"       => "eJinn",
        "subpackage"    => "",
        "support"       => "https://github.com/ArtisticPhoenix/eJinn/issues",
        "version"       => "1.0.0",
        "namespaces"     => [
            //{key:Namespace} => [{value:Namespace tier}]
            "eJinn\\Exception"  => [
                "subpackage"    => "Exception",
                "interfaces"    => [
                     //{key:numeric} => {value:Base class name OR Interface Tier}
                    "eJinnExceptionInterface"
                ],
                "exceptions" => [
                    //{key:Error_Code} => {value:Base class name OR Excption Tier}
                    0     => "UnknownError",
                    1001  => "ResservedCode",
                    1002  => "UnknownConfKey",
                    1003  => "DuplateCode",
                    1004  => "MissingRequired",
                    1005  => "KeyNotAllowed",
                    1006  => "KeyNotAllowed",
                    1007  => "ConfCollision",
                    1100  => [
                        "name" => "JsonParseError",
                        "reserved" => [[1101,1108]], //reserve range [1101-1108] @see: (json_last_error() + 1100)
                    ]
                ]//end exceptions
            ]//end namespace "eJinn\Exception"
        ] //end namespaces
    ];//end config
```
__eJinn's__ configuration is based on a _Multiple Tier_ based tree with _Top Down Inheritance_.  This lets us have the maximum flexabillity while still maintaining the minimum configuration size. Properties are passed down the tree from the higher level _(Parent)_ to lower levels _(Children)_. If a property exists in both the _Child_ and the _Parent_, then the value for the _Child's_ property is used.

The configuration can be as simple as the _PHP_ array shown above, or you can use an `\eJinn\Reader\{Type}` class to convert many diffrent types of files into the _PHP_ array which is then passed into the `\eJinn\eJinnParser` class.  The _Reader_ is desined to be extendable, so if one doesn't exist for the type of configuration file you prefer, let us know!

Configuration Schema
--------------------
Property matching in **eJinn** is case insensative, so the property named "version" will match ( but is not limited to ) any of the following: "VERSION", "Verison and "vErSiOn".

The main tiers of the configuration are _Global_, _Namespace_ and _Entity (Interface or Excption)_ . The _Global_ tier is the top level of the configuration array.  The _Namespace_ is the array contained within the __namespaces__ property. The _Interfaces_ and _Exceptions_ tiers contain the final definition for the Interface and Exception classes __eJinn__ will create.  For the most part we will refer to them collectivly as the _Entity_ tier. They share many simularities, however there are some signifigant diffrences. Interfaces are simpler then Excptions and therefore contain fewer configuration properties then Excptions. In genaral any _extra_ configuration properties that are in Interfaces will simply be ignored.  These will be displayed in the _Interface_ tier with __ignored__ in the __Required__ column.

With the exception of the container properties listed above, each tier can contain any of the properties from any of the tiers above it. However, it cannot contain any properites from any tiers below it. The _Entity_ tier can contain any of the configuration properties, but the _Global_ tier can only contain the properties defined within it.

Container properties are denoted with __protected__ in the __Required__ column below.  As stated above, these properties _Must_ exist at the tier shown and cannot be placed anywhere else in the configuration hierarchy. __eJinn__ will throw an exception and let you know if there are any problems in your configuration.

Internal properties are denoted with __private__ in the __Required__ column below.  In general these properties are not accessable outside the `\eJinn\eJinnParser` class and are shown here only for completeness and documentation purposes. They should **not** be included anywere within the configuration. 

Comment properties are properties that begin with a `_`.  These properties _(and their decendants)_ are removed(ignored) from the configuration while it is being proccessed by the parser.  This is useful because it allows you to exclude chunks of the configuration without actually deleting them. You can also leave yourself development notes with in the configuration by simply doing something like this `_coment = "eJinn is the most awesomeist thing I ever saw"`.

### Global Tier ###
 Property          |   Type   |   Required  | Description
 ----------------- | -------- | ----------- | ------------------------------------------------------
 author            |  string  |     no      | Used as the `@author` tag in the Entity's Doc Comment.
 buildpath         |  mixed   |     no      | * See below 
 description       |  string  |     no      | Placed in the opening line of the Entitie's Doc Comment.
 package           |  string  |     no      | Used as the `@package` tag.
 subPackage        |  string  |     no      | Used as the `@subpackage` tag.
 support           |  string  |     no      | Used as the `@link` tag. This can be a URL or an Email. Support help for your project.               
 version           |  string  |     yes     | Used as the `@version` tag. Format `major.minor[.revision]`. __eJinn__ will recompile the classes if the version is changed.
 extends           |  string  |     no      | A base Exception class to extend, default is _PHP's_ `\Excption` class. This should be a fully qualified class name.
 severity          | integer  |     no      | A default severity value, usefull only when entity is a decendant of _PHP's_ `\ErrorExcption` class. Also creates class constant `Class::SEVERITY`. The default is `E_USER_ERROR`               
 implements        |  array   |     no      | Array of fully quallifed interfance names for excptions to impliment. Ignored by interface entities(excptions can impliment multiple interfaces). Interfaces created by __eJinn__ are automatically populated where aplicable.            
 reserved          |  array   |     no      | Array of integer codes, or nested arrays `[[min,max]]` for a range of integers. This is a sanity check for _error codes_ that should not be created by this configuration. 
 namespaces        |  array   |  protected  | Array of namespaces, the `key` should be the namespace which is used by the entities nested in this array.
 eJinn:Hash        |  string  |   private   | Used as the `@eJinn:hash` tag. Configuration hash used to check when changes are made
 eJinn:BuildVersion|  string  |   private   | Used as the `@eJinn:buildVersion` tag. This allows the __eJinn__ project to force a recompile when updates are done to the compiler, seperate from the __eJinn__ version number.
 eJinn:Buildtime   |  float   |   private   | Used as the `@eJinn:buildTime` tag.  Time when this config was last parsed _(microtime)_
eJinn:Pathname     |  string  |   private   | class Path and filename

### Namespace Teir ### 
 Property          |   Type   |  Required   | Description
 ----------------- | -------- | ----------- | ------------------------------------------------------
 interfaces        |  array   |     no      | Array of interface classes that __eJinn__ will create ( post-parse ) This is not required but you must have either this property or exceptions, or both.           
 exceptions        |  array   |     no      | Array of exception classes that __eJinn__ will create ( post-parse ) This is not required but you must have either this property or interfaces, or both.               
 namespace         |  string  |   private   | The namespace taken from `$global['namespaces'][$namespace]` ie. the key of the namespaces array from the _Global Tier_ putting namespace as the key insures no duplacte namespaces are allowed, and it makes to much sense not to do  it that way.
 psr               |  number  |   private   | PSR setting at this namespace tier, this is for intarnal storage of the value of buildpath if using the psr array at this level.
                
### Exception Tier ### 
 Property          |   Type   |  Required   | Description
 ----------------- | -------- | ----------- | ------------------------------------------------------
 name              |  string  |     yes     | Excption's Class name. Should not include a namespace, it should be the base class name. 
 code              |  integer |     yes     | Exceptions Error code taken from `$namespace['exceptions'][$code]`. The default error code `__construct($message={default},$code=...)`. And a class constant `Class::ERROR_CODE`
 severity          |  integer |     no      | see Global[severity], shown here to offset ~~Interface[severity]~~ 
 message           |  string  |     no      | A default error message `__construct($message={default},$code=...)`
 qulifiedName      |  string  |   private   | The fully qualied class name `namespace\class`
 
 ### Interface Tier ### 
 Property          |   Type   |  Required   | Description
 ----------------- | -------- | ----------- | ------------------------------------------------------
 name              |  string  |     yes     | Interface's Class name. Should not include a namespace, it should be the base class name.
 qulifiedName      |  string  |   private   | The fully qualied class name `namespace\class`
 code              |  integer |   ignored   | Not Aplicable to this entity type
 severity          |  integer |   ignored   | Not Aplicable to this entity type 
 message           |  string  |   ignored   | Not Aplicable to this entity type 
 extends           |  string  |   ignored   | Not Aplicable to this entity type 
 implements        |  array   |   ignored   | Not Aplicable to this entity type 
 
 - **buildpath** some special consideration for the _buildpath_ property:
     - The default value is the location of the configuration file currently being proccessed.
     - If programmatically running __eJinn__, then this is the second argument of `eJinn\eJinnParser::parse()`.
     - When overriding, this can be either a path relative to parent tier's buildpath, or an absolute path. Unlike other properties that are simply replaced, buildpaths are appended when relative and replaced when absolute. Relative paths should __not__ begin with a `/`.  Absolute paths should begin with a `/` on Unix based systems and the drive letter on windows `c:\` _(either `/` or `\` is acceptable on window)_. 
     - A few _special_ values are avalible for the buildpath, These are desinged specifically for autoloaders and are an array instead of the normal string type associated with this property. You may have noticed this in the example configuration. 
     `["psr" => 0]` and `["psr" => 4]`. When using either, the value of the current buildpath _(at that tier)_ will have the namespace appended to it with the following considerations:
        - For `["psr" => 0]`: Any `_` underscores in the classname will be replace with a directory seperator. No special considerations are made for `_` underscores in the namespace.
        - For `["psr" => 4]`: No special considerations are made for the `_` underscore.
    - Filepaths should exist, and should be writable by _PHP_ running under the current user.
       
A short build path example:
```php
   $config = [
       "buildpath" => "/home/app",  //root path overide with absolute path
       "namespaces" => [
          "Models\\Users\\Exceptions" => [
                "buildpath" => ["psr" => 4],
                "exceptions" => [
                      100 => "UnknownUser",
                      101 => "InvalidPasword",
                ]
          ], 
          "Models\\Products\\Exceptions" => [
                "buildpath" => "Models/Products/Exceptions",
                "exceptions" => [
                      200 => "UnknownProduct",
                ]
          ] 
      ]
   ];
  ```
These 2 paths are roughly equivalent, they will product the following files.
 - /home/app/Models/Users/Exceptions/UnknownUser.php _(class \\Models\\Users\\Exceptions\\UnknownUser errorCode 100)_
 - /home/app/Models/Users/Exceptions/InvalidPasword.php _(class \\Models\\Users\\Exceptions\\InvalidPasword errorCode 101)_
 - /home/app/Models/Products/Exceptions/UnknownProduct.php _(class \\Models\\Users\\Exceptions\\UnknownProduct errorCode 200)_
 
Full PSR-4 example _(assming the configuration file was located in `/home/app`)_ this is equivalent to the above example.

  ```php
$config = [
       "buildpath" => ["psr" => 4],
       "namespaces" => [
          "Models\\Users\\Exceptions" => [
                "exceptions" => [
                      100 => "UnknownUser",
                      101 => "InvalidPasword",
                ]
          ], 
          "Models\\Products\\Exceptions" => [
                "exceptions" => [
                      200 => "UnknownProduct",
                ]
          ] 
      ]
   ];
```
Short PSR-0 example _(assming the configuration file was located in `/home/app`)_

  ```php
$config = [
       "buildpath" => ["psr" => 0],
       "namespaces" => [
          "Models\\Users" => [
                "exceptions" => [
                      100 => "Exception_UnknownUser",
                      101 => "Exception_InvalidPasword",
                ]
          ]            
      ]
   ];
```
This will create the folowing 2 classes.
 - /home/app/Models/Users/Exception/UnknownUser.php _(class \Models\Users\Exception_UnknownUser errorCode 100)_
 - /home/app/Models/Users/Exception/InvalidPasword.php _(class \Models\Users\Exception_InvalidPasword errorCode 101)_

PSR-0 allows for shorter namespaces, but still gives the seperation at the file level. This can make is somewhat easier to code as their is less need for `use` statements. But it's slightly more confusing as to where the file is located at.  Personally I prefer PSR-4.  For example the namespace is simply `Models\Users` so when used within the `Models\Users\User` class, you would not need to include a `use` tag for these exceptions, however the `_` is replaced in the path, putting the exceptions in their own seperate sub-directory.  When calling them you would do this `throw Excption_UnknownUser()` instead of just `throw UnknownUser` as the PSR-4 example.  However the PSR-4 example also requires this `use Models\Users\Exceptions\{ClassName};` for each exception class. That said, you could also do this in PSR-4 `use Models\Users\Exceptions as Exception;` with aliasing and then throw them like `throw Exeption\UnknownUser()` which is my prefered way.

       
### Build Options ###        
 Options           |   Type   |  Description
 ----------------- | -------- | -----------------------------------------------------------------
 uniqueExceptions  | boolean  | When true will throw an exception if duplicate codes are found in the config, when false duplicates are ignored. This has no effect on codes set as reserved in the config. 
 forceUnlock       | boolean  | In the event some error occurs that prevents deleteing the `.lock` file you can delete it manually or set this option to `true` to force the parser to run.
 lockFile          | string   | The name of the lock file, this should be either the full path and filename, or just the filename. In the case of just a filename the parser buildpath is used `eJinnParser::parse($config, $buildpath, $options)`
 forceRecompile    | boolean  | There are serveral ways that a config will be recompiled. You can set this option to `true` to force recompiling to override any caching.
 cacheFile         |string   | The name of the cache file, this should be either the full path and filename, or just the filename. In the case of just a filename the parser buildpath is used `eJinnParser::parse($config, $buildpath, $options)`
 debug             |  array   | Mainly for development.  When you add a tag to the debugger array __eJinn__ will output debugging infomation assocated to that tag. Typically this is the name of a particular method in the parser class. For a complete list see the __Debugging__ section.
 parseOnly         | boolean  | When this is set to `true` only the _parsing_ stage is ran. No actual files are created by __eJinn__. This can be useful for doing a dry run.  
 createPath         | boolean  | __eJinn__ will attempt to build any missing folders in the path of the execption and interfaces.  Caution should be taken when using this option.  It's suggest to set `['parseOnly'=>true, debug=>['showFiles']]` options as well the first time it is ran to insure the config will create the proper file locations.  Validation on missing folders is bypassed by this option, for obvious reasons. ( if they don't exist, we create them, so no errors for that )    
 
### Pre-Reading ###
Pre-Reading is defined as the act of opening a configuration file and translating it into the array structure given above. The __eJinn__ parser class only understands _PHP_ array structure above.  By seperating this out into it's own unique step, __eJinn__ can use virtual any configuration file type possible.  

### Parsing ###
Parsing is defined as the act of taking the _PHP_ configuration array and proccessing it.  The main job of this step is to flatten out the ineritance tree, and assign all relevent values to either an exception or interface entity.  During this step we also check the configuration for various errors.

### Compiling ###
Compiling is defined as the act of creating the actual _PHP_ class files for the entities found in the configuration file. 


### Locking ###
Locking is defined as the act of creating a _eJinn.lock_ file, which prevents mulitple processes from running the parser/compiler at the same time.  This file will be deleted when the compiling process completes. 

### Caching ###
Caching is defined as the act of creating a _eJinn.cache_ file, this file stores a reference or hash of the config file.  This hash is used to tell if any changes are made to the config file, between compilings. If no changes were made the parser will not complile the config. You can delete the _.cache_ file to force _eJinn_ to recompile all entites. You can also set the runtime option of _forcereCompile_ to `true` to cause the parser to recompile the config.

### Exception Considerations ###

Without going into to much detail, I will briefly explain why it's benifical to use unique exceptions.  The obvious example is this:    
```php
   //Catch a single exception ( based on class )
   try{     
      throw \Excption("some really verbose message");   
   }catch(\Exception $e ){
       echo $e->getMessage(); //prints "?"
   }
```

This is probably the worse exception example I could think of.  There is very little you can tell by this what exception it's surpressing or what you should do if it's an acceptable error, or a fatal one.  This is a slightly improved version:
```php
   //Catch a single exception ( based on class )
   try{     
      throw \Excption("some really verbose message", 100);   
   }catch(\Exception $e ){
       if($e->getCode() == 200 )
         echo $e->getMessage(); //prints "?"
       else
         throw \Excption("rethrow", $e->getCode(), $e);   
   }
```

This still dosn't give us a whole lot of options on catching and ignoring the error.
A much better way is something like this:
```php
   //Catch a single exception ( based on class )
   try{     
      throw \Excption("some really verbose message", 100);   
   }catch(\eJinn\Exception\ResservedCode $e ){
       //catch only class \eJinn\Exception\ResservedCode
   }catch(\eJinn\Exception\eJinnExceptionInterface $e ){
       //catch any class that implements \eJinn\Exception\eJinnExceptionInterface
   }
```
Now we have very fine grained control over our error handling. We can catch only the errors we want, and we can handle a range of error in diffrent `catch` blocks.  The only problem with this type of error handling is the added hassle in setting the exception classes and keeping track of them.  

This is exactly the issue __eJinn__ was designed to handle.  By simplifing the creation and tracking of these exceptions we can create as many exceptions as we need and have a sinular place to keep track of them.


### Other Configuration Examples ###
Minimal Config.
```php
return [
    "version"       => "1.0.0",
    "namespaces"     => [
        ""  => [
            "exceptions" => [
                0     => "UnknownError",
            ]
        ]
    ]
];
```
The above configureation will create a single exception file, this will be created at the location of the configuration file with no namespace. So if we had our config at `/home/app/Exceptions` then we would get this file:

 - /home/app/Exceptions/UnknownError _( class \UnknownError errorCode 0)_


Still a work in progress.

 * beta release with basic PHP config
 * then (not in a specific order)
     * testing and more testing
     * composer publish
     * work on some basic Readers
     * cli interface
     * http interface
     * unit testing
     * documentation & examples (wikki)
     
     