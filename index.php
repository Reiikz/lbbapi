<?php

define('APP_ROOT', __DIR__);

//include app logic here
include_once __DIR__ . "/core/core.php";

//register all allowed paths to be executed here
include_once APP_ROOT . "/core/pathRegistry.php";

include_once APP_ROOT . "/core/core.php";


/*

    define("PAGES", array(
        "api/v1/geoip/query.php" => "api/v1/geoip/query.php",
        "api/v1/geoip/" => "api/v1/geoip/query.php"
    ));

*/

if (key_exists($_GET["requestedPath"], PAGES)){
    include_once APP_ROOT . "/" . PAGES[$_GET["requestedPath"]];
} elseif (key_exists($_GET["requestedPath"], RAW_FILES)){
    $path = APP_ROOT . "/" . RAW_FILES[$_GET["requestedPath"]];
    if(str_ends_with(strtolower($path), ".css")){
        header("Content-Type: text/css");
    }elseif(str_ends_with(strtolower($path), ".js")){
        header("Content-Type: text/javascript");
    }else{
        header("Content-Type: " . mime_content_type($path));
    }
    header("Content-Length: " . filesize($path));
    readfile($path);
    exit(0);
} else{
    header("Location: " .  getPathClientWebRoot() . "/cpanel/");
}