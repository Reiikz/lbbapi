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
redirectIfNotLoggedIn();
/*
    **************************
*/

$token=null;
if(!isset($_POST["token"])){
    if(session_status() != PHP_SESSION_ACTIVE){
        session_start();   
    }
    if(!isset($_SESSION["username"])){
        header("HTTP/1.1 403 Bad request");
        echo "<h1>No token!</h1>";
        exit(0);
    }
}else{
    $token=$_POST["token"];
}



$zone = filterForIllegalChars($_POST["zone"]);
$zonePermissions = "$zone.manage";


include_once $GLOBALS["webroot"] . "/core/permissions.php";
if(!userHasAnyOfThesePermissions(array($zonePermissions, "admin"), $token)){
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>>:|</h1>";
    exit(0);
}

include_once $GLOBALS["webroot"] . "/core/parser.php";

$available_zones = bind9_zoneconfig_decode($GLOBALS["config"]["ZoneConfigFile"]);

if (!is_array($available_zones)){
    header("HTTP/1.1 500 Internal Server Error");
    echo "<h1>Could not decode zone configuration file, is it empty?</h1>";
    exit(0);
}

$zone_config = null;

if(!isset($available_zones[$zone])){
    header("HTTP/1.1 500 Internal Server Error");
    echo "<h1>Did not find zone $zone?</h1>";
    exit(0);
}else{
    $zone_config = $available_zones[$zone];
}

unlink($zone_config["file"]);

unset($available_zones[$zone]);

file_put_contents($GLOBALS["config"]["ZoneConfigFile"], bind9_zoneconfig_encode($available_zones), LOCK_EX);

$ret = null;
exec("sudo systemctl reload bind9", $ret);

if(isset($_POST["returnTo"])){
    header("Location: " . $_POST["returnTo"]);
}



