<?php

return [
    "author"        => "ArtisticPhoenix",
    "description"   => "eJinn The Exception Genie",
    "package"       => "Evo",
    "subpackage"    => "eJinn",
    "_buildpath"     => ["psr"=>4],
    "support"       => "https://github.com/ArtisticPhoenix/eJinn/issues",
    "version"       => "1.0.0",
    "reserved"       => [1,2,[8,20]],
    "namespaces"     => [
        "evo\\ejinn\\exception"  => [
            "subpackage"    => "Exception",
            "buildpath"     =>  __DIR__.'/exception/',
            "interfaces"    => [
                "eJinnExceptionInterface"
            ],
            'implements' =>[
                    'evo\\ejinn\\exception\\eJinnExceptionInterface'
            ],
            "exceptions" => [
                "0"     => "UnknownError",
                "1001"  => "UnknownOption",
                "1002"  => "UnknownInterface",
                "1003"  => "UnknownClass",
                "1004"  => "UnknownKey",
                "1005"  => "ProcessLocked",
                "1006"  => "ParseError",
                "1007"  => "InvalidDataType",
                "1008"  => "MissingRequired",
                "1009"  => "ReservedKeyword",
                "10010" => "ReservedExceptionCode",
                "1011"  => "DuplicateExceptionCode",
                "1012"  => "ProcessLocked",
                "1013"  => "PathNotFound",
                "1014"  => "PathNotWritable",
                "1015"  => "CouldNotCreateFile",
                "1016"  => "InvalidConfigFile"
                /*
                "1100"  => [
                    "Name" => "JsonParseError",
                    "reserved" => [[1101,1108]],
                ]*/
            ]
        ]
    ]
];
