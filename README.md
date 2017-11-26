# eJinn 
_(pronunced eeGin)_

**The Exception Genie**

 - Do you like having 1 exception per exception class? 
 - Are you tired of creating those boring boliler plate classes?
 - Do you like having error codes that are unique in you're whole library, or project?
 - Are you tired of keeping track of all those pesky exception codes?
 
Then this little framework may be just for you!
 
**eJin** builds exception classes for you! **eJin** builds exception interfaces for you! **eJin** keeps track of changes made to the configuration of each exception. This lets you bootstrap **eJin** in your project and it will only build the exceptions it needs to at run time.  Of course you can precompile your exceptions too. **eJin** employees a _Multiple Reader_ concept so with a bit of work you can use various types of config files, such as Json, XML. The default type is a PHP array.
 
 
 ```php
[
   "author" => "ArtisticPhoenix",
   "description" => "eJinn The Exception Genie",
   "package" => "eJinn",
   "subpackage" => "Exception",
   "support" => "https://github.com/ArtisticPhoenix/eJinn/issues",
   "version" => "1.0.0",
   "buildpath" => "Exception",
   "namespace" => "eJinn\\Exception",
   "extends" => "Exception",    
   "interfaces" => [
		[
			"class" => "eJinnException"
		]
   ],
   "exception" => [
		[
			"class" => "eJinnException",
			"code" => 0
		],[
       		"class" => "JsonError",
			"code" => 1
       ]
   ],
]
```

 Then with a simple command you can generate exception classes bassed on the `"exception"` object. _@todo: give example_
 
 
**eJinn Config Properties**

 - **author:** Your name, included in the Doc Block tag `@author`.
 
 - **description:** Short discription placed at the top of the Doc Block.
 
 - **package:** Your package, included in the Doc Block tag `@package`.

 - **subpackage:** Your sub package, included in the Doc Block tag `@subpackage`.

 - **support:** Link or email for support or documention, included in the Doc Block tag `@see`.

 - **version**_(required)_**:** Your exception's version.  Because **eJinn** keeps track of the configuration, changing the version will force rebuilding the exception classes.
 
 - **buildpath**_(required)_**:** Location to place the generated files.
 
 - **interfaces:** List of Interfaces to impliment.
     - **class**_(required)_**:** Base name of the interface _(without the namespace)_.

     -  _(Overwrite):_ You can use any of the top level tags in a nested `interface`, this includes the _"exception"_. If an exception is nested in the _"interfaces"_ list, then it will inherit first from the interface item it's nested in and then from the top level ( global ) properties.
 
 - **namespace:** Namespace for the exception classes.
 
 - **extends:** Base exception class to extend. _Default: \Exception_
 
 - **exceptions**_(required)_**:** A list of Excptions.  All exceptions created by a given config _must_ have unique error codes within that config. .
 
    - **class**_(required)_**:** Relative Class name of the exception.
	
	- **code**_(required)_**:** The exception's error code.  This number ( and class pair ) must be unique witin this config or **eJinn** will throw an Execption.

    - _(Overwrite):_ You can use any of the top level properties inside the nested `exception` objects. This includes the _"interface"_ properties.  If an interface is nested in the _"exception"_ property, then it will inherit first from the exception item it's in, then from the top level ( global ) properties.	
	
General principals and benifits of using "these" types of exceptions:
    
```php
   //Catch a single excption ( based on class )
   try{  
   
   }catch(\eJinn\Exception\UnknownError $e ){
       //catches only this specific error
       echo $e->getCode(); //prints "0"
   }
```

In the above code we are catching one single exception based on the class. Sure you could do something like this:

```php

   try{  
   
   }catch(\Exception $e ){
       //catches all exceptions
       if( $e->getCode() == 0){
       	echo $e->getCode(); //prints "0"
       }      
   }
```

But the problem with this is if its an error we can't handle in the catch block, then what do we do with it?  Typically you would issue another exception with the third argument _($previous)_ being `$e`. Which is less then ideal, beacause it's going to muck up your statcktrace and generally make things misserable to read.  This is true anytime you check exceptions based on their _errorCode_.

Ok so we covered catching a specific exception, so you might be wondering how would I catch a subset of Excptions.  For this purpose we can use an Interface. It's possible to use Inheritance ( extending a parent exception ) for this.

Interfaces actually have a big advantage over inheritance. In _PHP_ your classes can only have one ancestor _(each)_. They can an only `extend` one parent class andfor exceptions to work correctly, they already have to extend `\Excption`.  However, a class can impliment as many interfaces as you want.  So with interfaces its possible to overlap subsets.  For example:

```php
	class ExcepptionA extends \Exception impliments InterfaceA{

	}

	class ExcepptionB extends \Exception impliments InterfaceA, InterfaceB{

	}

	class ExcepptionC extends \Exception impliments InterfaceA, InterfaceB, InterfaceC{

	}
```

As you can see above, we can catch all three exceptions by using `InterfaceA` in our catch block.  We can catch _ExcepptionB_ and _ExcepptionC_ by using `InterfaceB`, and by using `InterfaceC` you can catch only _ExcepptionC_.

The interfaces _eJinn_ needs is very simpe:

```php 
	namespace MyProject;

	interface MyInterface
	{
	}
```

If you want to include an existing interface, set it's buildpath to boolean `false`.

```php
	//...
	"interface:" [
		[
        		"class" : "eJinn\\eJinnException",
        		"buildpath" : false
        	]
    ],
    //...
```

All eJinn excptions will impliment `\eJinn\eJinnException`.  Rememeber we can impliment as many as we want and this way you can always find the exceptions created by _eJinn_.

**Other Config Examples**
Minimal Config 
_(interface is not required, but shown for completeness)_
```php
[
    "version" => "1.0.0",
    "buildpath" => "Exception",
    "exception" => [
		[
			"class" => "eJinnException",
			"code" => 0
		],[
       		"class" => "JsonError",
			"code" => 1
       ]
	],
    "interface" => [
		[
			"class" => "eJinnException"
		]
	]
]
```

This is roughly equivalnt to above 
_(you can orginze your exceptions by interface)_
```php
[
    "interfaces" => [
		[
			"class" => "eJinnException",
			"version" => "1.0.0",
			"buildpath" => "Exception",
			"exceptions" => [
				[
					"class" => "eJinnException",
					"code" => 0
				],[
					"class" => "JsonError",
					"code" => 1
			   ]
			]
		]
	]
]
```
This is roughly equivalnt to above 
_(you can have multiple interfaces)_
```php
[	
	"version" => "1.0.0",
	"buildpath" => "Exception",
	"exceptions" => [
		[
			"class" => "eJinnException",
			"code" => 0,
			"interfaces" => [
				[
					"class" => "eJinnException",
				]
			]
		],[
			"class" => "JsonError",
			"code" => 1,
			"interfaces" => [
				[
					"class" => "eJinnException",
				]
			]
	   ]
	]   
]
```

**Other config types:** 

JSON
```json
{
   "author" : "ArtisticPhoenix",
   "description" : "eJinn The Exception Genie",
   "package" : "eJinn",
   "subpackage" : "Exception",
   "support" : "https://github.com/ArtisticPhoenix/eJinn/issues",
   "version" : "1.0.0",
   "buildpath" : "Exception",
   "namespace" : "eJinn\\Exception",
   "extends" : "Exception",
   "interfaces" : [{
       "class" : "eJinnException"
   }],
   "exceptions" : [{
		"class" : "UnknownError",
		"code" : 0
    },{
       	"class":"JsonError",
		"code" : 1
    }]
}
```

XML
```xml
<?xml version="1.0"?>
<eJinn>
	<author>ArtisticPhoenix</author>
	<description>eJinn The Exception Genie</description>
	<package>eJinn</package>
	<subpackage>Exception</subpackage>
	<support>https://github.com/ArtisticPhoenix/eJinn/issues</support>
	<version>1.0.0</version>
	<buildpath>Exception</buildpath>
	<namespace>eJinn\\Exception</namespace>
	<extends>Exception</extends>
	<interfaces>
		<interface>
			<class>eJinnException</class>
		<interface>
	</interfaces>
	<exceptions>
		<exception code="0" >
			<class>UnknownError</class>
		</exception>
		<exception code="1" >
			<class>JsonError</class>
		</exception>
	</exceptions>	
</eJinn>
```

