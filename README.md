# eJinn 
_(pronunced eeGin)_

**The Exception Genie**

 - Do you like having 1 exception per exception class? 
 - Are you tired of creating those boring boliler plate classes?
 - Do you like having error codes that are unique in you're whole library, or project?
 - Are you tired of keeping track of all those pesky exception codes?
 
 Then this little framework may be just for you!
 
 It uses a simple `eJinn.json` file to configure your exceptions. 
 
 ```json
{
    "author" : "ArtisticPhoenix",
    "description" : "eJinn The Exception Genie",
    "package" : "eJinn",
    "subpackage" : "Exception",
    "support" : "https://github.com/ArtisticPhoenix/eJinn/issues",
    "license" : "GPL-3.0",
    "version" : "1.0.0",
    "buildpath" : "Exception",
    "namespace" : "eJinn\\Exception",
    "extends" : "Exception",
    "exception" : {
        "code0" : {
        		"class":"UnknownError"
        },"code1" : {
        		"class":"JsonError"
        }
    },
    "interface" : [{
        "class" : "eJinn\\eJinnException"
    }]
}
}
```

 Then with a simple command you can generate Exception classes bassed on the `"exception"` object.

 - **author**  : Your name, included in the Doc Block `@author` for the Excpetion class.
 
 - **description** : Short discription placed in the Doc Block
 
 - **package** : Your package, included in the Doc Block `@package` for the Excpetion class.

 - **subpackage** : Your sub package, included in the Doc Block `@subpackage` for the Excpetion class

 - **support** : Link or email for support or documention, included in the Doc Block `@link` for the Excpetion class.
 
 - **license** : License included in the Doc Block `@license`.

 - **version** : Your exception version.  Changing it will force rebuilding already created classes the next time the generator is ran. Included in the Doc Block `@version`.
 
 - **buildpath** : Location to place the Generated Exception files.
 
 - **interface** : List of Interfaces to impliment. These should be an "empty" interface, useful for catching excptions with diffrent namespaces.
     - **name** :  Name of Interface

     -  _(Overwrite)_ : You can use any of the top level tags in a nested `interface` array, this includes the _"exception"_ properties. If an exception is nested in the _"interface"_ property, then it will inherit first from the interface item it's nested in and then from the top level ( global ) properties.
 
 - **namespace** : Namespace for the exception classes.
 
 - **exception** : A list of Excptions. The _key_ or property of the _exception_ object has to be in this format `code[0-9]+`, basically the error code, prefixed by the word _code_. This insures that all exceptions created by this `ejinn.json` file have unique error codes. If you nest _exceptions_ within the _interface_ objects, then the _code_ still must be uinque in the context of the `ejinn.json` file.
 
    - **class** :  Error code -> Class pair

    - _(Overwrite)_ : You can use any of the top level properties in a nested `exception` object to override the default value. This includes the _"interface"_ propertie.  If an interface is nested in the _"exception"_ property, then it will inherit first from the exception item its in, then from the top level ( global ) properties.
    
General principals and benifits of using "these" types of Exceptions:
    
```php
   //Catch a single excption ( based on class )
   try{  
   
   }catch(\eJinn\Exception\UnknownError $e ){
       //catches only this specific error
       echo $e->getCode(); //prints "0"
   }
```

In the above code we are catching one single Exception based on the class. Sure you could do something like this:

```php

   try{  
   
   }catch(\Exception $e ){
       //catches all exceptions
       if( $e->getCode() == 0){
       	echo $e->getCode(); //prints "0"
       }      
   }
```

But the problem with this, is if its an error we can't handle here, in the catch block. Then what do we do with it?  Typically you would issue another Exception with the third argument _($previous)_ being `$e`. Which is less then ideal, beacause it's going to muck up your statcktrace. And generally, make things misserable to read.   

Ok so we covered catching a specific Exception, so you might be wondering how would a catch a subset of Excptions.  For this purpose we can use an Interface. It's possible to use Inheritance ( extending a parent Exception ) for this.

Interfaces actually have a big advantage over inheritance. In _PHP_ your classes can only have one ancestor _(each)_. They can an only `extend` one parent class and for exceptions to work correctly, they already have to extend `\Excption`.  However, a class can impliment as many interfaces as you want.  So with interfaces its possible to overlap subsets.  For example:

```php

class ExcepptionA extends \Exception impliments InterfaceA{

}

class ExcepptionB extends \Exception impliments InterfaceA, InterfaceB{

}

class ExcepptionC extends \Exception impliments InterfaceA, InterfaceB, InterfaceC{

}
```

As you can see above, we can catch all three Exceptions by using `InterfaceA` in our catch block.  We can catch _ExcepptionB_ and _ExcepptionC_ by using `InterfaceB`, and by using `InterfaceC` you can catch only _ExcepptionC_.

You can include an existing interface by setting the buildpath for the interface to boolean `false`.

```json
 	...
	"interface" : [{
        "class" : "eJinn\\eJinnException",
        "buildpath" : false
    }],
    ...

```
Interfaces are very simpe to build and you can create them yourself like this:

```php 
namespace MyProject;

interface MyInterface
{
}
```

All eJinn excptions will impliment `\eJinn\eJinnException`.  Rememeber we can impliment as many as we want so might as well, right? It could come in handy.

Other `eJinn.json` examples:

 ```json
{
	...
    "interface" : [{
        "class" : "eJinn\\eJinnException",
        "exception" : {
	        "0" : {
	        		"class":"UnknownError"
	        },"1" : {
	        		"class":"JsonError"
	        }
	    }
    }],
--    "exception" : {
--        "0" : {
--        		"class":"UnknownError"
--        },"1" : {
--        		"class":"JsonError"
--        }
--    }
}
```

This is equivalant to the orginal one 

