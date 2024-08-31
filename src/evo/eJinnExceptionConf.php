<?php
// http://localhost/evo/ejinn?config=C:\UniserverZ\www\evo\eJinn\src\evo\eJinnConf.php
return [
    "author"        => "ArtisticPhoenix",
    "description"   => "eJinn The Exception Genie!!",
	"license" 		=> "GPL-3.0",
    "package"       => "Evo",
    "subpackage"    => "eJinn",
    "buildpath"     => str_replace('\\','/',realpath(__DIR__.'/../')),
    "support"       => "https://github.com/ArtisticPhoenix/eJinn/issues",
    "version"       => "2.0.0",
    "reserved"       => [[400,499],[500,599]], //for HTTP errors
    "namespaces"     => [
          "evo\\exception"  => [
                "subpackage"    => "Evo",
                "buildpath"     =>  ["psr"=>4],
                "interfaces"    => [
                    [
                        "name"            => "EvoExceptionInterface",
                        "description"     => "Base Interface for all EVO Exceptions. All exceptions should implement it.
                          All Interfaces should extend it.",
                        "extends"         => "\\Throwable"
                    ]/*, [
                        "name"            => "HttpClientExceptionInterface", //400 series
                        "description"     => "Base Interface for all Client HTTP exceptions.",
                        "extends"         => "\\Throwable"
                    ], [
                        "name"            => "HttpServerExceptionInterface", //500 series
                        "description"     => "Base Interface for all Server HTTP exceptions.",
                        "extends"         => "\\Throwable"
                    ]*/
                ],
                "implements" => [
                    /* All EVO exceptions should implement the EvoThrowable Interface. */
                    "EvoExceptionInterface"
                ],
                "exceptions" => [
                    "0"       => [
                        "name"          => "EvoException",
                        "description"   => "Base Exception class. May be used as a placeholder or generic exceptions."
                    ],
                    "5"       => [
                        "name"          => "EvoError",
                        "description"   => "Base Error class, May be used as a placeholder or generic errors.",
						"severity"	    => E_ERROR,
                        "extends"       => "\\Error"
                    ],
                    "10"     => [
                        "name"          => "EvoErrorException",
                        "severity"      => E_ERROR,
                        "description"   => "Base ErrorExceptions class, with a severity level. May be used as a
                          placeholder or generic error exceptions.",
                        "extends"       => "\\ErrorException"
                    ],
                    "15"       => [
                        "name"          => "UncaughtException",
                        "severity"      => E_ERROR,
                        "description"   => "Exception thrown during runtime that was not caught.",
                        "extends"       => "ErrorException"
                    ],
                    "20"       => [
                        "name"          => "NotImplementedException",
                        "description"   => "Exception thrown when the functionality is not fully implemented yet. Useful
                          as a trackable placeholder for development",
                        "extends"       => "EvoException"
                    ],
                    "25"    => [
                        "name"          => "ShutdownErrorException",
                        "severity"      => E_ERROR,
                        "description"   => "ErrorException thrown during shutdown by an uncaught Error.",
                        "extends"       => "ErrorException"
                    ],

                    //400 block Http Client Errors
                    //"400"=> "Http400BadRequest",
                    //"401"=> "Http401Unauthorized",
                    //"402"=> "Http402PaymentRequired",
                    //"403"=> "Http403Forbidden",
                    //"404"=> "Http404NotFound",
                    //"405"=> "Http405MethodNotAllowed",
                    //"406"=> "Http406NotAcceptable",
                    //"407"=> "Http407ProxyAuthenticationRequired",
                    //"408"=> "Http408RequestTimeout",
                    //"409"=> "Http409Conflict",
                    //"410"=> "Http410Gone",
                    //"411"=> "Http411LengthRequired",
                    //"412"=> "Http412PreconditionFailed",
                    //"413"=> "Http413PayloadTooLarge",
                    //"414"=> "Http414URITooLong",
                    //"415"=> "Http415UnsupportedMediaType",
                    //"416"=> "Http416RangeNotSatisfiable",
                    //"417"=> "Http417ExpectationFailed",
                    //"418"=> "Http418Teapot",
                    //"421"=> "Http421MisdirectedRequest",
                    //"422"=> "Http422UnprocessableEntity",
                    //"423"=> "Http423Locked",
                    //"424"=> "Http424FailedDependency",
                    //"425"=> "Http425TooEarly",
                    //"426"=> "Http426UpgradeRequired",
                    //"428"=> "Http428PreconditionRequired",
                    //"429"=> "Http429TooManyRequests",
                    //"431"=> "Http431RequestHeaderFieldsTooLarge",
                    //"451"=> "Http451UnavailableForLegalReasons",
                    //500 block Http Server Errors
                    //"500"=> "Http500InternalServerError",
                    //"501"=> "Http501NotImplemented",
                    //"502"=> "Http502BadGateway",
                    //"503"=> "Http503ServiceUnavailable",
                    //"504"=> "Http504GatewayTimeout",
                    //"505"=> "Http505HTTPVersionNotSupported",
                    //"506"=> "Http506VariantAlsoNegotiates",
                    //"507"=> "Http507InsufficientStorage",
                    //"508"=> "Http508LoopDetected",
                    //"510"=> "Http510NotExtended",
                    //"511"=> "Http511NetworkAuthenticationRequired",

                    //900 block extend PHP default Exception class, some of these may never be used in user land, but are included for consistency
                    "900" => [
                        "name"          => "BadFunctionCallException",
                        "description"   => "Exception thrown if a callback refers to an undefined function or if some
                          arguments are missing.",
                        "extends"       => "\\BadFunctionCallException"
                    ],
                    "903" => [
                        /*
                         * this is not a base exception class in PHP, but for consistency it goes here.
                         * This is useful when using dynamic classes
                        */
                        "name"          => "BadClassCallException",
                        "description"   => "Exception thrown if a callback refers to an undefined class or if some
                          arguments are missing from classes constructor.",
                        "extends"       => "LogicException"
                    ],
                    "906"       => [
                        "name"          => "BadMethodCallException",
                        "description"   => "Exception thrown if a callback refers to an undefined method or if some
                          arguments are missing.",
                        "extends"       => "\\BadMethodCallException",
                    ],
                    "909" => [
                        /*
                         * this is not a base exception class in PHP, but for consistency it goes here.
                         * This is useful when using __get() and __set()
                         */
                        "name"          => "BadPropertyCallException",
                        "description"   => "Exception thrown if a callback refers to an undefined class property.",
                        "extends"       => "LogicException"
                    ],
                    "921"       => [
                        "name"          => "DomainException",
                        "description"   => "Exception thrown if a value does not adhere to a defined valid data domain.
                          This represents errors that should be detected at compile time.",
                        "extends"       => "\\DomainException"
                    ],
                    "924"       => [
                        "name"          => "InvalidArgumentException",
                        "description"   => "Exception thrown if an function or method argument is not of the expected type.",
                        "extends"       => "\\InvalidArgumentException"
                    ],
                    "927"       => [
                        "name"          => "LengthException",
                        "description"   => "Exception thrown if a length is invalid.",
                        "extends"       => "\\LengthException"
                    ],
                    "930"       => [
                        "name"          => "LogicException",
                        "description"   => "Exception that represents error in the program logic. This kind of exception
                          should lead directly to a fix in your code",
                        "extends"       => "\\LogicException"
                    ],
                    "933"       => [
                        "name"          => "OutOfBoundsException",
                        "description"   => "Exception thrown if a value is not a valid key. This represents errors
                          that should be detected at run time.",
                        "extends"       => "\\OutOfBoundsException"
                    ],
                    "936"       => [
                        "name"          => "OutOfRangeException",
                        "description"   => "Exception thrown when an illegal index was requested. This represents errors
                          that should be detected at compile time. This is the runtime version of DomainException",
                        "extends"       => "\\OutOfRangeException"
                    ],
                    "939"       => [
                        "name"          => "OverflowException",
                        "description"   => "Exception thrown when adding an element to a full container.",
                        "extends"       => "\\OverflowException"
                    ],
                    "942"       => [
                        "name"          => "RangeException",
                        "description"   => "Exception thrown to indicate range errors during program execution. Normally
                          this means there was an arithmetic error other than under/overflow. This represents errors that
                          should be detected at run time. This is the runtime version of DomainException.",
                        "extends"       => "\\RangeException"
                    ],
                    "945"       => [
                        "name"          => "RuntimeException",
                        "description"   => "Exception thrown if an error which can only be found on runtime occurs.",
                        "extends"       => "\\RuntimeException"
                    ],
                    "948"       => [
                        "name"          => "UnderflowException",
                        "description"   => "Exception thrown when performing an invalid operation on an empty container,
                          such as removing an element.",
                        "extends"       => "\\UnderflowException"
                    ],
                    "951"       => [
                        "name"          => "UnexpectedValueException",
                        "description"   => "Exception thrown if a value does not match with a set of values.
                          Typically, this happens when a function calls another function and expects the return value to
                          be of a certain type or value, not including arithmetic or buffer related errors.",
                        "extends"       => "\\UnexpectedValueException"
                    ],
                    "954"       => [
                        "name"          => "ArgumentCountError",
                        "description"   => "Error is thrown when too few arguments are passed to a user-defined function or method.",
                        "extends"       => "\\ArgumentCountError"
                    ],
                    "957"       => [
                        "name"          => "ArithmeticError",
                        "description"   => "Error is thrown when an error occurs while performing mathematical operations.
                          These errors include attempting to perform a bitshift by a negative amount, and any call to intdiv() that would result in a value outside the possible bounds of an int. ",
                        "extends"       => "\\ArithmeticError"
                    ],
                    "960"       => [
                        "name"          => "AssertionError",
                        "description"   => "Error is thrown when an assertion made via assert() fails.",
                        "extends"       => "\\AssertionError"
                    ],
                    "963"       => [
                        "name"          => "DivisionByZeroError",
                        "description"   => "Error is thrown when an attempt is made to divide a number by zero.",
                        "extends"       => "\\DivisionByZeroError"
                    ],

                    "966"       => [
                        "name"          => "CompileError",
                        "description"   => "Error is thrown for some compilation errors, which formerly issued a fatal error. ",
                        "extends"       => "\\CompileError"
                    ],
                    "969"       => [
                        "name"          => "ParseError",
                        "description"   => "Error is thrown when an error occurs while parsing.",
                        "extends"       => "\\ParseError"
                    ],
                    "972"       => [
                        "name"          => "TypeError",
                        "description"   => "Error is thrown when the value being set for a class property does not match
                          the property's corresponding declared type Or the argument type being passed to a function does
                          not match its corresponding declared parameter type Or a value being returned from a function 
                          does not match the declared function return type.",
                        "extends"       => "\\TypeError"
                    ],
                    "975"       => [
                        "name"          => "ValueError",
                        "description"   => "Error is thrown when the type of an argument is correct but the value of it
                          is incorrect. For example, passing a negative integer when the function expects a positive one, 
                          or passing an empty string/array when the function expects it to not be empty.",
                        "extends"       => "\\ValueError"
                    ],
                    "978"       => [
                        "name"          => "UnhandledMatchError",
                        "description"   => "",
                        "extends"       => "\\UnhandledMatchError"
                    ],
                    "981"       => [
                        "name"          => "FiberError",
                        "description"   => "",
                        "extends"       => "\\FiberError"
                    ],
                    //1000 block general development errors


                    //2000 block resource errors
                    "2000"=> [
                        "name"          => "ResourceException",
                        "description"   => "Exception thrown for a resource.",
                        "extends"       => "RuntimeException"
                    ],
                    "2005"=> [
                        "name"          => "LockoutException",
                        "description"   => "Exception thrown if a resource is locked.",
                        "extends"       => "ResourceException"
                    ],
                    "2010"=> [
                        "name"          => "TimeoutException",
                        "description"   => "Exception thrown if a resource takes to long to respoond.",
                        "extends"       => "ResourceException"
                    ],
                    "2015" => [
                        "name"          => "PermissionException",
                        "description"   => "Exception thrown if a resource cannot be accessed by the current user.",
                        "extends"       => "RuntimeException"
                    ],

                    //2500 block filesystem errors
                    "2500"=> [
                        "name"          => "FilesystemException",
                        "description"   => "Exception thrown related to the file system.",
                        "extends"       => "RuntimeException"
                    ],
                    "2505"=> [
                        "name"          => "BadDirException",
                        "description"   => "Exception thrown if a directory cannot be found.",
                        "extends"       => "FilesystemException"
                    ],
                    "2515"=> [
                        "name"          => "WritableDirException",
                        "description"   => "Exception thrown when trying to write to a directory that is not writable.",
                        "extends"       => "FilesystemException"
                    ],
                    "2520"=> [
                        "name"          => "BadFileException",
                        "description"   => "Exception thrown if a file cannot be found.",
                        "extends"       => "FilesystemException"
                    ],
                    "2525"=> [
                        "name"          => "WritableFileException",
                        "description"   => "Exception thrown when trying to write to a file that is not writable.",
                        "extends"       => "FilesystemException"
                    ],
                    "2530"=> [
                        "name"          => "BadFileExtensionException",
                        "description"   => "Exception thrown if a file has an invalid extension.",
                        "extends"       => "FilesystemException"
                    ]
              ]
          ]
      ]
  ];

