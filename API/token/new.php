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

/*
    **************************
*/

if(!session_status() != PHP_SESSION_ACTIVE){
    session_start();
}


if(!isset($_POST["newToken"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>no newToken value!</h1>";
    exit(0);
}

$token = null;
if(isset($_POST["token"])){
    $token = $_POST["token"];
    if(!userHasAnyOfThesePermissions(array("token.create"), $token)){
        header("HTTP/1.1 403 Forbidden");
        echo "<h1>This token can't create other other tokens token.create!</h1>";
        exit(0);
    }
}

echo "<pre>";

$permissions=array();
$newToken = null;
$returnTo = null;
$description = null;

$x = 0;
$maxPerms = 1000;
foreach($_POST as $key => $value){
    if($x >= $maxPerms){
        header("HTTP/1.1 400 Bad request");
        echo "<h1>Max permissions per request $maxPerms!</h1>";
        exit(0);
    }
    switch($key){
        case "token":
            continue 2;
        case "newToken":
            $newToken = $value;
            continue 2;
        case "description":
            $description = $value;
            continue 2;
        case "returnTo":
            $returnTo = $value;
            continue 2;
    }
    if(empty($value)){
        continue;
    }
    array_push($permissions, $value);
    $x++;
}

if((!userHasAllThesePermissions($permissions, $token)) && (!userHasAnyOfThesePermissions(array("admin"), $token))){
    header("HTTP/1.1 403 Forbidden!");
    echo "<h1>>:|!</h1>";
    exit(0);
}

$accessToken = null;
if($token != null){
    $accessToken = readToken($token);   
}

$user = null;
if(isset($_SESSION["username"])){
    $user =  $_SESSION["username"];
}else{
    $user = $accessToken["username"];
}

$usermd5 = null;
if(isset($_SESSION["username_md5"])){
    $usermd5 =  $_SESSION["username_md5"];
}else{
    $usermd5 = $accessToken["username_md5"];
}

$_TOKEN=array(
    "id" => $newToken,
    "permissions" => $permissions,
    "description" => $description,
    "username" => $user,
    "username_md5" => $usermd5,
    "createdAt" => time(),
);

// echo "<pre>";
// print_r($_TOKEN);
// echo "</pre>";

saveToken($_TOKEN);

if($returnTo != null){
    header("Location: $returnTo");
}