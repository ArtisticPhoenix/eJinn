 eJinn (alpha)
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

An example of an **eJinn** configuration ( as a _PHP_ array).
```php
    return [
        "author"        => "ArtisticPhoenix",
        "buildpath"     => "Exception",
        "description"   => "eJinn The Exception Genie",
        "package"       => "eJinn",
        "subpackage"    => "",
        "support"       => "https://github.com/ArtisticPhoenix/eJinn/issues",
        "version"       => "1.0.0",
        "namespaces"     => [
            //{key:Namespace} => [{value:Namespace tier}]
            "eJinn\\Exception"  => [
                "subpackage"    => "Exception",
                "buildpath"     => "Exception",
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
            ]//end namespace "eJinn\\Exception"
        ] //end namespaces
    ];//end config
```
__eJinn's__ configuration is based on a _Multiple Tier_ based tree with _Top Down Inheritance_.  This lets us have the maximum flexabillity while still maintaining the minimum configuration size. Properties are passed down the tree from the higher level _(Parent)_ to lower levels _(Children)_. If a property exists in both the _Child_ and the _Parent_, then the value for the _Child's_ property is used.

The configuration can be as simple as the _PHP_ array shown above, or you can use an `\\eJinn\\Reader\\{Type}` class to convert many diffrent types of files into the _PHP_ array which is then passed into the `\\eJinn\\eJinnParser` class.  The _Reader_ is desined to be extendable, so if one doesn't exist for the type of configuration file you prefer, let us know!

Configuration Schema
--------------------
Property matching in **eJinn** is case insensative, so the property named "version" will match ( but is not limited to ) any of the following: "VERSION", "Verison and "vErSiOn".

The main tiers of the configuration are _Global_, _Namespace_ and _Entity (Interface or Excption)_ . The _Global_ tier is the top level of the configuration array.  The _Namespace_ is the array contained within the __namespaces__ property. The _Interfaces_ and _Exceptions_ tiers contain the final definition for the Interface and Exception classes __eJinn__ will create.  For the most part we will refer to them collectivly as the _Entity_ tier. They share many simularities, however there are some signifigant diffrences. Interfaces are simpler then Excptions and therefore contain fewer configuration properties then Excptions. In genaral any _extra_ configuration properties that are in Interfaces will simply be ignored.  These will be displayed in the _Interface_ tier with __ignored__ in the __Required__ column.

With the exception of the container properties listed above, each tier can contain any of the properties from any of the tiers above it. However, it cannot contain any properites from any tiers below it. The _Entity_ tier can contain any of the configuration properties, but the _Global_ tier can only contain the properties defined within it.

Container properties are denoted with __protected__ in the __Required__ column below.  As stated above, these properties _Must_ exist at the tier shown and cannot be placed anywhere else in the configuration hierarchy. __eJinn__ will throw an exception and let you know if there are any problems in your configuration.

Internal properties are denoted with __private__ in the __Required__ column below.  In general these properties are not accessable outside the `\\eJinn\\eJinnParser` class and are shown here only for completeness and documentation purposes. They should **not** be included anywere within the configuration. 

### Global Tier ###
 Property          |   Type   |   Required  | Description
 ----------------- | -------- | ----------- | ------------------------------------------------------
 author            |  string  |     no      | Used as the `@author` tag in the Entity's Doc Comment.
 buildpath         |  string  |     yes     | Path where the Class files will be saved. Absolute path or relative to the configuration file.
 description       |  string  |     no      | Placed in the opening line of the Entitie's Doc Comment.
 package           |  string  |     no      | Used as the `@package` tag.
 subPackage        |  string  |     no      | Used as the `@subpackage` tag.
 support           |  string  |     no      | Used as the `@link` tag. This can be a URL or an Email. Support help for your project.               
 version           |  string  |     yes     | Used as the `@version` tag. Format `major.minor[.revision]`. __eJinn__ will recompile the classes if the version is changed.
 extends           |  string  |     no      | A base Exception class to extend, default is _PHP's_ `\\Excption` class. This should be a fully qualified class name.
 severity          |  string  |     no      | A default severity value, usefull only used when decendant of _PHP's_ `\\ErrorExcption` class. Also creates class constant `Class::SEVERITY`. The default is `E_USER_ERROR`               
 impliments        |  array   |     no      | Array of fully quallifed interfance names for excptions to impliment. Ignored by interface entities(excptions can impliment multiple interfaces). Interfaces created by __eJinn__ are automatically populated where aplicable.            
 reserved          |  array   |     no      | Array of integer codes, or nested arrays `[[min,max]]` for a range of integers. This is a sanity check for _error codes_ that should not be created by this configuration. 
 namespaces        |  array   |  protected  | Array of namespaces, the `key` should be the namespace which is used by the entities nested in this array.
 eJinnHash         |  string  |   private   | Used as the `@eJinn:Hash` tag. Configuration hash used to check when changes are made
 eJinnBuildVersion |  string  |   private   | Used as the `@eJinn:BuildVersion` tag. This allows the __eJinn__ project to force a recompile when updates are done to the compiler, seperate from the __eJinn__ version number.
 eJinnBuildDate    |  string  |   private   | Used as the `@eJinn:BuildDate` tag. Configuration hash used to check when changes are made

### Namespace Teir ### 
 Property          |   Type   |  Required   | Description
 ----------------- | -------- | ----------- | ------------------------------------------------------
 interfaces        |  array   |     no      | Array of interface classes that __eJinn__ will create ( post-parse )               
 exceptions        |  array   |     no      | Array of exception classes that __eJinn__ will create ( post-parse )     
 namespace         |  string  |   private   | The namespace taken from `$global['namespaces'][$namespace]`              
                
### Exception Tier ### 
 Property          |   Type   |  Required   | Description
 ----------------- | -------- | ----------- | ------------------------------------------------------
 name              |  string  |     yes     | Excption's Class name. Should not include a namespace, it should be the base class name.      
 code              |  integer |     yes     | Exceptions Error code taken from `$namespace['exceptions'][$code]`. The default error code `__construct($message={default},$code=...)`. And a class constant `Class::ERROR_CODE`
 severity          |  integer |     no      | see Global[severity], shown here to offset ~~Interface[severity]~~ 
 message           |  string  |     no      | A default error message `__construct($message={default},$code=...)`
 qName             |  string  |   private   | The fully qualied class name `namespace\\class`
 
 ### Interface Tier ### 
 Property          |   Type   |  Required   | Description
 ----------------- | -------- | ----------- | ------------------------------------------------------
 name              |  string  |     yes     | Interface's Class name. Should not include a namespace, it should be the base class name.
 qName             |  string  |   private   | The fully qualied class name `namespace\\class`
 code              |  integer |   ignored   |  
 severity          |  integer |   ignored   |  
 message           |  string  |   ignored   |  
 extends           |  string  |   ignored   |  
 impliments        |  array   |   ignored   |  
