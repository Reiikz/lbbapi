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

$token=null;
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

if(!isset($_POST["ttl"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No ttl!</h1>";
    exit(0);
}

// echo "AB";
include_once $GLOBALS["webroot"] . "/core/permissions.php";
// echo "CD";
include_once $GLOBALS["webroot"] . "/core/manageDomainDatabase.php";

$authority="";
if(isset($_POST["authority"])){
    $authority = $_POST["authority"];
}

$domain=$_POST["record"];

echo "$domain\n";

$domainPermission = null;
if(str_ends_with($domain, ".")){
    $domainPermission = "$domain" . "new";
}else{
    $domainPermission = "$domain.new";
}

if(!userHasAnyOfThesePermissions(array($domainPermission, "admin"), $token)){
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>>:|</h1>";
    exit(0);
}

// echo "<pre>";
// print_r($_POST);
// echo "</pre>";

// echo "<pre>";

newRecord($domain, $_POST["type"], $_POST["value"], $_POST["ttl"]);
// deleteRecord($domain, $_POST["type"], $_POST["value"]);
// echo "</pre>";

if(isset($_POST["returnTo"])){
    header("Location: " . $_POST["returnTo"]);
}

$ret = null;
exec("sudo systemctl reload bind9", $ret);



