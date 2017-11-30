 eJinn 
 =====
_(pronunced eeGin)_

The Exception Genie (*PHP* 5.6+)
--------------------------------
 * Do you like having one exception code per exception class? 
 * Do you like using interfaces to group exception classes togather?
 * Do you like having error codes that are unique in your whole library, or project?

 * Are you tired of keeping track of all those pesky exception codes?
 * Are you tired of creating those boring boilerplate exception and interface classes?
 * Are you tired of searching for the approrate exception, because you can't remember if you made one for this or that error?
 
Overview
--------
**eJinn** compiles and builds exception and interface classes for you! **eJinn** keeps track of changes made to the configuration of each exception, and rebuilds them only when changes have been made. **eJinn** can be bootstraped in your project and will build the exceptions it needs to at run time or you can precompile them during development. **eJinn** uses a seperate configuration _Reader_ class, so you can extedn the formats it can read beyond those that it comes with.
**eJinn** exceptions can extend any other _Base_ excption, even if it was created externally.  **eJinn** exceptions can impliment interfaces created externally. **eJinn** exceptions can extend either the _\\Exception_ and _\\ErrorExcption_ default _PHP_ base classes, those extending _\\ErrorExcepton_ also support _Severty_, external _File_ names and _Line_ numbers. **eJinn** exceptions can still be thrown with multiple error codes and/or messages, the predefined code becomes the _Default_. 


An example of an **eJinn** *PHP* array config.

 
```php
    return [
        "author"        => "ArtisticPhoenix",
        "description"   => "eJinn The Exception Genie",
        "package"       => "eJinn",
        "subpackage"    => "",
        "support"       => "https://github.com/ArtisticPhoenix/eJinn/issues",
        "version"       => "1.0.0",
        "buildpath"     => "Exception",
        "namespaces"     => [
            //{Namespace} => [{Namespace Level}]
            "eJinn\\Exception"  => [
                "subpackage"    => "Exception",
                "buildpath"     => "Exception",
                "interfaces"    => [
                    "eJinnExceptionInterface"
                ],
                "exceptions" => [
                    //{Error Code} => {Base Class Name}
                    0     => "UnknownError",
                    1001  => "ResservedCode",
                    1002  => "UnknownConfKey",
                    1003  => "DuplateCode",
                    1004  => "MissingRequired",
                    1005  => "KeyNotAllowed",
                    1006  => "KeyNotAllowed",
                    1007  => "ConfCollision",
                    "1100"  => [
                        "name" => "JsonParseError",
                        "reserved" => [[1101,1108]], //reserve range [1101-1108] @see: (json_last_error() + 1100)
                    ]
                ]//end exceptions
            ]//end namespace "eJinn\\Exception"
        ] //end namespaces
    ];//end config
```

With this simple command `@todo: command` **eJinn** will parse the above configuration file and build all the exceptions we need of our project.  **eJinn** configuration  files are based on a pass down inherited structure. With some exceptions, the configured values will be passed down through the config hierarchy. If the child level contains the same key it will override the value from the level above. This way **eJinn** gives you the most configuration options with the least amount of work and the smallest space.  No one likes huge configuration files with obsure properties that you have to keep looking up all the time.  **eJinn** does what it says and says what it does, without sacrificing functionality or flexabillity.

If you're lazy like me, but still like bullet proof code, then **eJinn** may be just what the doctor ordered. 

Config Properties
-----------------
Configs are seprated into 3 distinct levels.  Properties can only be passed down from a higher levle to a lower level.  If a prooperty exits in both levels the property from the lower level takes precidence over the property from the upper level, and it will be used instead.  Container properties can only exist on the level they are presented on in the above example.  These are _namespaces_, _interfaces_ and _exceptions_.  Property matching in **eJinn** is case insensative, so the property named "version" will match ( but is not limited to ) any of the following: "VERSION", "Verison and "vErSiOn".

 - **Global Level** 
 - **Namespace Level**
 - **Entity Level** 

