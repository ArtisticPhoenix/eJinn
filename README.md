# eJinn 
_(pronunced eeGin)_

**The Exception Genie (*PHP* 5.6+)**

 - Do you like having one exception code per exception class? 
 - Do you like using interfaces to group exception classes togather?
 - Do you like having error codes that are unique in your whole library, or project?

 - Are you tired of keeping track of all those pesky exception codes?
 - Are you tired of creating those boring boilerplate exception and interface classes?
 - Are you tired of searching for the approrate exception, because you can't remember if you made one for this or that error?
 
 - Do you want a simple configuration file to manage all your exceptions?
 
Then **eJinn** may be just for you!
 
**eJinn** compiles and builds exception classes for you! **eJinn** compiles and builds exception interfaces for you! **eJinn** keeps track of changes made to the configuration of each exception. **eJinn** can be bootstraped in your project and will build the exceptions it needs to at run time.  **eJinn** can precompile your exceptions during development. **eJinn** employees a Multiple Reader concept, so **eJinn** can use various types of config files ( eg, *PHP* array, Json, XML or even programtically buid your config). **eJinn** exceptions can be thrown with multiple error codes and/or messages if you want.  **eJinn** exceptions can extend a base excption that was created outside of **eJinn**.  **eJinn** exceptions can impliment interfaces outside of **eJinn**. 


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
                        "reserved" => [[1101,1108]], //reserve range 1101 - 1108 (json_last_error() + 1100)
                    ]
                ]
            ]
        ]
    ];
```

With this simple command `@todo: command` **eJinn** will parse the above configuration file and build all the exceptions we need of our project.  **eJinn** configuration  files are based on a pass down inherited structure. With some exceptions, the configured values will be passed down through the config hierarchy. If the child level contains the same key it will override the value from the level above. This way **eJinn** gives you the most configuration options with the least amount of work and the smallest space.  No one likes huge configuration files with obsure properties that you have to keep looking up all the time.  **eJinn** does what it says and says what it does, without sacrificing functionality or flexabillity.

If you're lazy like me, but still like bullet proof code, then **eJinn** may be just what the doctor ordered. 

**Config Properties** 
Configs are seprated into 3 distinct levels.  Properties can only be passed down from a higher leve to a lower level.  If a prooperty exits in both levels the property from the lower level takes precidence over the property from the upper level.  The only exception to this is the properties that are "containers" for the next level down. These properties can only exist on the level they are presented on in the above example.  These are _namespaces_, _interfaces_ and _exceptions_

 - **Global Level** 
 - **Namespace Level**
 - **Entity Level** 

