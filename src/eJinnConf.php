<?php

return [
    "author"        => "ArtisticPhoenix",
    "description"   => "eJinn The Exception Genie",
    "package"       => "Evo",
    "subpackage"    => "eJinn",
    "buildpath"     => ["psr"=>4],
    "support"       => "https://github.com/ArtisticPhoenix/eJinn/issues",
    "version"       => "1.0.0",
    "reserved"       => [1,2,[8,20]],
    "namespaces"     => [
        "Evo\\eJinn\\Exception"  => [
            "subpackage"    => "Exception",
            "interfaces"    => [
                "eJinnExceptionInterface"
            ],
        	'implements' =>[
        			'eJinn\\ArtisticPhoenix\\Exception\\eJinnExceptionInterface'
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
                    "Name" => "JsonParseError",
                    "reserved" => [[1101,1108]],
                ]
            ]
        ]
    ]
];
