<?php
use evo\ejinn\eJinnParser;

error_reporting(-1);
ini_set('display_errors', 1);

require_once __DIR__.'/vendor/autoload.php';

if (isset($_GET['rebuild'])) {
    require_once __DIR__.'/src/eJinnBuild.php';
} else {
    $url =  "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    $escaped_url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    echo '<a href="' . $escaped_url . '?rebuild=true" >Rebuild Core Exceptions</a>';
}

