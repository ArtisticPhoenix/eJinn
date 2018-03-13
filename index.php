<?php


error_reporting(-1);
ini_set('display_errors', 1);

require_once __DIR__.'/vendor/autoload.php';

if (isset($_GET['rebuild'])) {
    require_once __DIR__.'/src/evo/ejinn/run.php';
} else {
    $url =  "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    $escaped_url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    echo '<a href="' . $escaped_url . '?rebuild=true" >Rebuild Core Exceptions</a>';
    echo"<div> Or include the path to a config as '?config={path}</div>";
    $command = "php -f ".str_replace("\\","/", __DIR__."/src/evo/ejinn/run.php");
    echo <<<HTML
<div>
    Or include the path to a config on the command line call
<br>
&nbsp;&nbsp;&nbsp;cmd> {$command} {config}
</div>
HTML;
}

