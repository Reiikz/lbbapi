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

if(!isset($_POST["zone"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>Missing field zone!</h1>";
    exit(0);
}

if(!isset($_POST["zoneServer"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>Missing field zone server!</h1>";
    exit(0);
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

if(!isset($available_zones[$zone])){
    array_push($available_zones["zones"], $zone);
}

$available_zones[$zone] = array(
    "type" => "master",
    "file" => $GLOBALS["config"]["dbDirectory"] . "/$zone.db",
    "allow-transfer" => array(
        "none",
    ),
);

file_put_contents($GLOBALS["config"]["ZoneConfigFile"], bind9_zoneconfig_encode($available_zones), LOCK_EX);

if(!file_exists($available_zones[$zone]["file"])){
    file_put_contents( $available_zones[$zone]["file"],  bind9_zonedb_encode(bind9_zonedb_generate_default($zone, filterForIllegalChars($_POST["zoneServer"]))), LOCK_EX );
}
