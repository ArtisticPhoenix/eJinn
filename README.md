# Exception
Auto Create Child Exceptions 

 - Do you like having 1 exception per exception class? 
 - Are you tired of creating those boring boliler plate classes?
 - Do you like having error codes that are uniuqe in you library, or project?
 - Are you tired of keeping tack of all those pesky exception codes?
 
 Then this little framework may be just for you!
 
 It uses a simple `.json` file to configure your exceptions. 
 
 `{
    "packageAuthor" : "Yourname"
 	"package" : "eJinn",
 	"subPackage" : "",
 	"support" : "",
 	"license" : "GPL-3.0",
	"version" : "1.0.0",
	"buildPath" : "Exception",
	"namespace" : "",
	"interface" : [],
	"codes" : {
		"0" : "UnknownError"
	}
}`
 
 Then with a simple command you can generate Exception classes bassed on the `"codes"` object.

 - packageAuthor : (optional) Your name, included in the Doc Block for the Excpetion class
 
 - package : (optional) Your package, included in the Doc Block for the Excpetion class

 - subPackage : (optional) Your sub package, included in the Doc Block for the Excpetion class

 - support : (optional) a link or email for support or documention, included in the Doc Block for the Excpetion class
 
 - license : license 

 - version : Your exception version.  Changing it will force rebuilding already created classes the next time the generator is ran
 
 - buildPath : location to place the Generated Exception files
 
 - interface : this should be an "empty" interface, useful for catching excptions with diffrent namespaces
 
 - namespace : Namespace for the exception classes
 
 - codes :  Error code -> Class pair