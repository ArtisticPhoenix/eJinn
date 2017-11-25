# Exception
Auto Create Child Exceptions 

 - Do you like having 1 exception per exception class? 
 - Are you tired of creating those boring boliler plate classes?
 - Do you like having error codes that are uniuqe in you library, or project?
 - Are you tired of keeping tack of all those pesky exception codes?
 
 Then this little framework may be just for you!
 
 It uses a simple `.json` file to configure your exceptions. 
 
 ```json
{
    "author" : "ArtisticPhoenix",
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
