<?php
/*
    We want this to be usable anywhere in the web server so we must find our root path!
*/

if(!isset($GLOBALS["webroot"])){
    $path=__FILE__;
    while(!file_exists("$path/.stop")){
        $path=dirname($path);
    }
    $GLOBALS["webroot"]=$path;

}
include_once $GLOBALS["webroot"] . "/core/core.php";
include_once $GLOBALS["webroot"] . "/core/permissions.php";
include_once $GLOBALS["webroot"] . "/core/token/common.php";
redirectIfNotLoggedIn();
/*
    **************************
*/


if(!userHasAnyOfThesePermissions(array("admin"))){
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>Can't create token!</h1>";
    exit(0);
}

if(!isset($_POST["token"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No token!</h1>";
    exit(0);
}

$tokenPath = getTokenPath($_POST["token"]);
if(file_exists($tokenPath)){
    unlink($tokenPath);
}

if(isset($_POST["returnTo"])){
    header("Location: " . $_POST["returnTo"]);
}