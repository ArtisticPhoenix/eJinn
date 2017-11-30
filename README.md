 eJinn (PHP v5.6+)
 =====
The Exception Genie
-------------------

### Overview ###
 * Do you like having one exception code per exception class? 
 * Do you like using interfaces to group exception classes togather?
 * Do you like having error codes that are unique in your whole library, or project?

 * Are you tired of keeping track of all those pesky exception codes?
 * Are you tired of creating those boring boilerplate exception and interface classes?
 * Are you tired of searching for the approrate exception, because you can't remember if you made one for this or that error?
 
If you answered _Yes_ to any of the above then __eJinn__ may be just what you have been missing.  __eJinn__ builds excpetion classes and interfaces based on a simple and flexable configuration file.  You can use a single configuration file or as many as you like.

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
__eJinn__ configuration is based on a _3 Tier_ tree with _Top Down Inheritance_.  This lets us have the maximum flexabillity while still maintaining the minimum configuration size. Properties are passed down the tree from the higher level _(Parent)_ to lower levels _(Children)_. If a property exists in both the _Child_ and the _Parent_, then the value for the _Child's_ property is used.

The configuration can be as simple as the _PHP_ array shown above, or you can use an _\\eJinn\\Reader_ class to convert many diffrent types of files into the _PHP_ array which is then feed into the _eJinnParser_ class.  The _Reader_ is desined to be extendable, so if one is not avalible for the file type of your choice then you can create your own or contact us and we may be able add it to the project.


* **Global:** the _Top_ level of the configuration tree. 

Config Properties
-----------------
Configs are seprated into 3 distinct levels _Global_, _Namespace_ and _Entity_.  

Inheritance: properties are passed down from a higher level to a lower level.  

Property matching in **eJinn** is case insensative, so the property named "version" will match ( but is not limited to ) any of the following: "VERSION", "Verison and "vErSiOn".

 - ### Global Level ###

 - ### Global Level ###
     -  **Author:**
  

  
  
  
 * **Namespace Level**
 
 * **Entity Level** 

