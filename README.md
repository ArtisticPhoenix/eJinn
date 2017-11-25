# eJinn 
_(pronunced eeGin)_

**The Exception Genie**

 - Do you like having 1 exception per exception class? 
 - Are you tired of creating those boring boliler plate classes?
 - Do you like having error codes that are uniuqe in you're library, or project?
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

 - **support** : Link or email for support or documention, included in the Doc Block `@see` for the Excpetion class.
 
 - **license** : License included in the Doc Block `@license`.

 - **version** : Your exception version.  Changing it will force rebuilding already created classes the next time the generator is ran. Included in the Doc Block `@version`.
 
 - **buildPath** : Location to place the Generated Exception files.
 
 - **interface** : List of "fully qualified" interfaces to impliment. These should be an "empty" interface, useful for catching excptions with diffrent namespaces.
 
 - **namespace** : Namespace for the exception classes.
 
 - **exception** : A list of Excptions, this is an Object because then we force yo to use a numeric 'key'  ( which is the error code ) and this also forces it to be unique for each config file. 
 
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

The above code we are catching one single Exception based on the class. Sure you could do something like this:

```php
   //Catch a single excption ( based on class )
   try{  
   
   }catch(\Exception $e ){
       //catches all exceptions
       if( $e->getCode() == 0){
       	echo $e->getCode(); //prints "0"
       }      
   }
```

But the problem with this, is if its an error we can't handle here ( in the catch block ) what do we do with it.  Typically you would issue another Exception with the third argument _($previous)_ being `$e`, which is really not ideal.

Imagine now, you just want to catch a subset of Exceptions.  We could do this inheritance, and maybe that's something we'll impliment latter, but for now you can use an Interface.

Interfaces are not limited to one per Exception class, you can only have one ancestor in _PHP_. However you can impliment as many interfaces as you want, and for our purposes they will work just fine.

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


