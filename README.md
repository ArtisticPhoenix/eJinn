# eJinn 
_(pronunced eeGin)_

**The Exception Genie**

 - Do you like having one exception code per exception class? 
 - Are you tired of creating those boring boiler plate classes?
 - Do you like having error codes that are unique in your whole library, or project?
 - Are you tired of keeping track of all those pesky exception codes?
 
Then this little framework may be just for you!
 
**eJinn** builds exception classes for you! **eJinn** builds exception interfaces for you! **eJinn** keeps track of changes made to the configuration of each exception. This lets you bootstrap **eJinn** in your project and it will only build the exceptions it needs to at run time.  Of course you can precompile your exceptions too. **eJinn** employees a _Multiple Reader_ concept so with a bit of work you can use various types of config files, such as Json, XML. The default type is a PHP array.  **eJinn** exceptions work just like normal exceptions, so you can still throw them with multiple error codes if you want.
 
