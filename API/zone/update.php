<?php

include_once APP_ROOT . "/core/core.php";

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


if(!isset($_POST["zone"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No zone!</h1>";
    exit(0);
}

$check="defaultTTL";
if(!isset($_POST[$check])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No $check!</h1>";
    exit(0);
}
if(!LBBAPI_is_integer($_POST[$check])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>$check is Not a integer!</h1>";
    exit(0);
}
if($_POST[$check] < 0){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>$check < 0!</h1>";
    exit(0);
}

// $check="startOfAuthority";
// if(!isset($_POST[$check])){
//     header("HTTP/1.1 400 Bad request");
//     echo "<h1>No $check!</h1>";
//     exit(0);
// }
// if(!str_ends_with($_POST[$check], ".")){
//     header("HTTP/1.1 400 Bad request");
//     echo "<h1>$check must end with a dot (.)!</h1>";
//     exit(0);
// }

$check="authorityServer";
if(!isset($_POST[$check])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No $check!</h1>";
    exit(0);
}

$check="serial";
if(!isset($_POST[$check])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No $check!</h1>";
    exit(0);
}
if(!LBBAPI_is_integer($_POST[$check])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>$check is Not a integer!</h1>";
    exit(0);
}
if($_POST[$check] < 0){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>$check < 0!</h1>";
    exit(0);
}

$check="refresh";
if(!isset($_POST[$check])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No $check!</h1>";
    exit(0);
}
if(!LBBAPI_is_integer($_POST[$check])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>$check is Not a integer!</h1>";
    exit(0);
}
if($_POST[$check] < 0){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>$check < 0!</h1>";
    exit(0);
}

$check="retryTransfer";
if(!isset($_POST[$check])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No $check!</h1>";
    exit(0);
}
if(!LBBAPI_is_integer($_POST[$check])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>$check is Not a integer!</h1>";
    exit(0);
}
if($_POST[$check] < 0){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>$check < 0!</h1>";
    exit(0);
}

$check="expiresIn";
if(!isset($_POST[$check])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No $check!</h1>";
    exit(0);
}
if(!LBBAPI_is_integer($_POST[$check])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>$check is Not a integer!</h1>";
    exit(0);
}
if($_POST[$check] < 0){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>$check < 0!</h1>";
    exit(0);
}

$check="negativeCacheTTL";
if(!isset($_POST[$check])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No $check!</h1>";
    exit(0);
}
if(!LBBAPI_is_integer($_POST[$check])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>$check is Not a integer!</h1>";
    exit(0);
}
if($_POST[$check] < 0){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>$check < 0!</h1>";
    exit(0);
}

$zone = filterForIllegalChars($_POST["zone"]);
$zonePermissions = "$zone.manage";

include_once APP_ROOT . "/core/permissions.php";
if(!userHasAnyOfThesePermissions(array($zonePermissions, "admin"), $token)){
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>>:|</h1>";
    exit(0);
}

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

$db = bind9_zonedb_decode($zone_config["file"]);

if(!is_array($db)){
    header("HTTP/1.1 500 Internal Server Error");
    echo "<h1>Did not decode database at " . $zone_config["file"] . "?</h1>";
    exit(0);
}

$db["DEFAULT_TTL"]=$_POST["defaultTTL"];
// $db["SOA"]=filterForIllegalChars($_POST["startOfAuthority"]);
$db["SOA_SERVER"]=$_POST["authorityServer"];
$db["SERIAL"]=$_POST["serial"];
$db["REFRESH"]=$_POST["refresh"];
$db["RETRY"]=$_POST["retryTransfer"];
$db["EXPIRE"]=$_POST["expiresIn"];
$db["NEGATIVE_CACHE_TTL"]=$_POST["negativeCacheTTL"];

file_put_contents($zone_config["file"], bind9_zonedb_encode($db), LOCK_EX);

$ret = null;
exec("sudo systemctl reload bind9", $ret);

if(isset($_POST["returnTo"])){
    header("Location: " . $_POST["returnTo"]);
}