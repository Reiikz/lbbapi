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

if(!isset($_POST["token"])){
    session_start();
    if(!isset($_SESSION["username"])){
        header("HTTP/1.1 403 Bad request");
        echo "<h1>No token!</h1>";
        exit(0);
    }
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

if(!isset($_POST["newValue"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No newValue!</h1>";
    exit(0);
}

if(!isset($_POST["newTTL"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No newTTL!</h1>";
    exit(0);
}

include_once $GLOBALS["webroot"] . "/core/permissions.php";
include_once $GLOBALS["webroot"] . "/core/manageDomainDatabase.php";

recordUpdate($_POST["record"], $_POST["type"], $_POST["value"], $_POST["newValue"], $_POST["newTTL"]);

if(isset($_POST["returnTo"])){
    header("Location: " . $_POST["returnTo"]);
}




