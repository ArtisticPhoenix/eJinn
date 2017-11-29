<?php

return [
    "author"        => "ArtisticPhoenix",
    "description"   => "eJinn The Exception Genie",
    "package"       => "eJinn",
    "subpackage"    => "",
    "support"       => "https://github.com/ArtisticPhoenix/eJinn/issues",
    "version"       => "1.0.0",
    "reserved"       => [1,2,[8,20]],
    "namespaces"     => [
        ""                  => [
            "exceptions" => [
                0 => "Test"
             ]
        ],
        "eJinn"             => [
            "buildpath"     => "eJinn",
            "interfaces"    => [
                [
                    "name" => "ExceptionInterface",
                    "Parse" => false
                ]
            ]
        ],
        "eJinn\\Exception"  => [
            "subpackage"    => "Exception",
            "buildpath"     => "Exception",
            "interfaces"    => [
                "eJinnException",
                "eJinnInterface"
            ],
            "exceptions" => [
                0     => "UnknownError",
                1001  => "ResservedCode",
                1002  => "UnknownConfKey",
                1003  => "DuplateCode",
                1004  => "MissingRequired",
                1005  => "KeyNotAllowed",
                1006  => "KeyNotAllowed",
                1007  => "ConfCollision",
                "1100"  => [
                    "name" => "JsonParseError",
                    "reserved" => [[1101,1108], ""],
                ]
            ]
        ]
    ]
];
