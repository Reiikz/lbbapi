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

/*
    **************************
*/

$token = null;
if(!isset($_POST["token"])){
    session_start();
    if(!isset($_SESSION["username"])){
        header("HTTP/1.1 403 Bad request");
        echo "<h1>No token!</h1>";
        exit(0);
    }
}else{
    $token=$_POST["token"];
}

if(!isset($_POST["record"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No record!</h1>";
    exit(0);
}

if(!isset($_POST["type"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No type!</h1>";
    exit(0);
}

if(!isset($_POST["value"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No value!</h1>";
    exit(0);
}

if(!isset($_POST["authority"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No authority!</h1>";
    exit(0);
}

include_once $GLOBALS["webroot"] . "/core/permissions.php";
include_once $GLOBALS["webroot"] . "/core/manageDomainDatabase.php";

$record = preg_replace("/[^A-Za-z0-9-.]/", "OwO", $_POST["record"]);
$authority = preg_replace("/[^A-Za-z0-9-.]/", "OwO", $_POST["authority"]);
$type = preg_replace("/[^A-Za-z0-9-.]/", "OwO", $_POST["type"]);
$value = preg_replace("/[^A-Za-z0-9-.]/", "OwO", $_POST["value"]);

$domainPermission = null;
if(str_ends_with($record, ".")){
    $domainPermission = $record . "delete";
}else{
    $domainPermission = $record . ".delete";
}
if(!userHasAnyOfThesePermissions(array($domainPermission, "admin"), $token)){
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>>:|!</h1>";
    exit(0);
}

deleteRecord($record, $authority, $type, $value);

if(isset($_POST["returnTo"])){
    header("Location: " . $_POST["returnTo"]);
}

$ret = null;
exec("sudo systemctl reload bind9", $ret);




