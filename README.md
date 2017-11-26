# eJinn 
_(pronunced eeGin)_

**The Exception Genie**

 - Do you like having 1 exception per exception class? 
 - Are you tired of creating those boring boliler plate classes?
 - Do you like having error codes that are unique in you're whole library, or project?
 - Are you tired of keeping track of all those pesky exception codes?
 
 Then this little framework may be just for you!
 
 It uses a simple `.json` file to configure your exceptions. 
 
 ```json
{
    "author" : "ArtisticPhoenix",
    "description" : "eJinn The Error Genie",
    "package" : "eJinn",
    "subpackage" : "Exception",
    "support" : "https://github.com/ArtisticPhoenix/eJinn/issues",
    "license" : "GPL-3.0",
    "version" : "1.0.0",
    "buildPath" : "Exception",
    "namespace" : "eJinn\\Exception",
    "interface" : [
        "eJinn\\ExceptionInterface"
    ],
    "exception" : {
        "0" : {
        	    "class":"UnknownError"
        }
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
 
 - **buildPath** : Location to place the Generated Exception files.
 
 - **interface** : List of "fully qualified" interfaces to impliment. These should be an "empty" interface, useful for catching excptions with diffrent namespaces.
 
 - **namespace** : Namespace for the exception classes.
 
 - **exception** : A list of Excptions, this is an Object because then we force you to use a numeric 'property'  ( which is the error code ) and this also forces it to be unique for each config file its imporatant that this is a `string` that's probably the only thing you need to be careful about with _eJinn_. 
 
    - **class** :  Error code -> Class pair

    - _(Overwrite)_ : You can use any of the top level tags in a nested `exception` object to override the default value
    
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

Ok so we covered catching a specific Exception, so you might be wondering how would a catch a couple Excptions.  I you want to catch a subset of Exceptions, we could do this with inheritance. And maybe that's something we'll impliment latter, but for now just use an Interface.  It will work fine, really.

Interfaces actually have an advantage over inheritance, that's why we use them instead.

In _PHP_ your classes can only have one ancestor _(each)_. They can an only `extend` one parent, and they already have to extend `\Excption`, so were not going to make it any harder then we need to.

However your classes impliment as many interfaces as you want them to. This allows us to overlap Excption subsets if we want.

```php

class ExceptionA extends Exception




```


At this time you have to create the interface yourself, this is mainly beause of namespacing and auto loading them.  So it's something we may impliment in the future, but not to worry it's supper simple to create one (I know this goes against our second bullet point).

```php 
namespace MyProject;

interface MyInterface
{
}
```

Then after you add it in the **interface** entry in your `exception.json` file, you can make use of it like this when you want to catch a whole subset of Exceptions.

```php  
   //Catch a range of excptions ( based on interface )
   try{  
   
   }catch(\eJinn\ExceptionInterface $e ){
       //catches only Exception that impliment \eJinn\ExceptionInterface
        echo $e->getCode(); //prints "0" or mabyee "1"
   }
```

All eJinn excptions will impliment `\eJinn\ExceptionInterface`.  Rememeber we can impliment as many as we want so might as well right, it could come in handy.

I'm sure this isn't the end of the story.


