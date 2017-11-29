<?php
use eJinn\eJinnGenerator;

require_once __DIR__.'/vendor/autoload.php';

echo "<pre>";

$conf = require __DIR__.'/examples/config/eJinn.php';

//print_r($conf);

$Generator = new eJinnGenerator($conf);


echo "\nComplete in ".__FILE__." on ".__LINE__;