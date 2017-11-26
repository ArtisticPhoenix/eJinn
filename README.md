# eJinn 
_(pronunced eeGin)_

**The Exception Genie**

 - Do you like having 1 exception per exception class? 
 - Are you tired of creating those boring boliler plate classes?
 - Do you like having error codes that are unique in you're whole library, or project?
 - Are you tired of keeping track of all those pesky exception codes?
 
Then this little framework may be just for you!
 
**eJin** builds exception classes for you! **eJin** keeps track of changes made to the configuration of each exception. This lets you bootstrap it in your project and it will only build the exceptions it needs to at run time.  Of course you can precompile your exceptions too. **eJin** employees a _Multiple Reader_ concept so with a bit of work you can use various types of config files, such as Json, XML, and even a stored PHP array.  The default type is a stored PHP array.
 
 
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
   "interface" => [
		[
			"class" => "eJinnException"
		]
   ],
   "exception" => [
		[
			"class" => "eJinnException",
			"code" => 0,
		],[
       		"class" => "JsonError",
			"code" => 1,
       ]
   ],
]
```

 Then with a simple command you can generate exception classes bassed on the `"exception"` object. _@todo: give example_
 
 
**eJinn Config Properties**

 - **author:** Your name, included in the Doc Block `@author` for the Excpetion class.
 
 - **description:** Short discription placed in the Doc Block.
 
 - **package:** Your package, included in the Doc Block `@package` for the Excpetion class.

 - **subpackage:** Your sub package, included in the Doc Block `@subpackage` for the Excpetion class.

 - **support:** Link or email for support or documention, included in the Doc Block `@link` for the Excpetion class.

 - **version**_(required)_**:** Your exception version.  Changing it will force rebuilding already created classes the next time the generator is ran. Included in the Doc Block `@version`.
 
 - **buildpath**_(required)_**:** Location to place the generated files _(Exceptions and Interfaces)_.
 
 - **interface:** List of Interfaces to impliment. These should be an "empty" interface, useful for catching excptions with diffrent namespaces.
     - **class**_(required)_**:** Relative Class name of the interface _(without the namespace).

     -  _(Overwrite):_ You can use any of the top level tags in a nested `interface` array, this includes the _"exception"_ properties. If an exception is nested in the _"interface"_ property, then it will inherit first from the interface item it's nested in and then from the top level ( global ) properties.
 
 - **namespace:** Namespace for the exception classes.
 
 - **extends:** Base exception class to extend. _Default: \Exception_
 
 - **exception**_(required)_**:** A list of Excptions.  All exceptions created by a given config _must_ have unique error codes within that config. **eJinn** will throw an Execption.
 
    - **class**_(required)_**:** Relative Class name of the exception.
	
	- **code**_(required)_**:** 

    - _(Overwrite):_ You can use any of the top level properties in a nested `exception` object to override the default value. This includes the _"interface"_ propertie.  If an interface is nested in the _"exception"_ property, then it will inherit first from the exception item its in, then from the top level ( global ) properties.	
	
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

But the problem with this, is if its an error we can't handle here, in the catch block. Then what do we do with it?  Typically you would issue another exception with the third argument _($previous)_ being `$e`. Which is less then ideal, beacause it's going to muck up your statcktrace. And generally, make things misserable to read.  This is true anytime you check exceptions based on their _errorCode_

Ok so we covered catching a specific exception, so you might be wondering how would I catch a subset of Excptions.  For this purpose we can use an Interface. It's possible to use Inheritance ( extending a parent exception ) for this.

Interfaces actually have a big advantage over inheritance. In _PHP_ your classes can only have one ancestor _(each)_. They can an only `extend` one parent class and for exceptions to work correctly, they already have to extend `\Excption`.  However, a class can impliment as many interfaces as you want.  So with interfaces its possible to overlap subsets.  For example:

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
        "code0" => [
        		"class":"UnknownError"
        ],"code1" => [
        		"class":"JsonError"
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
    "interface" => [
		[
			"class" => "eJinnException",
			"version" => "1.0.0",
			"buildpath" => "Exception",
			"exception" => [
				"code0" => [
						"class":"UnknownError"
				],"code1" => [
						"class":"JsonError"
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
   "interface" : [{
       "class" : "eJinnException"
   }],
   "exception" : {
       	"code0" : {
       		"class":"UnknownError"
       	},"code1" : {
       		"class":"JsonError"
       	}
   }
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

