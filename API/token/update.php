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

$token = null;
if(isset($_POST["token"])){
    $token = $_POST["token"];
    if(!userHasAnyOfThesePermissions(array("token.update"), $token)){
        header("HTTP/1.1 403 Forbidden");
        echo "<h1>This token can't update other other tokens token.update!</h1>";
        exit(0);
    }
}


if(!isset($_POST["updateToken"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>no updateToken value!</h1>";
    exit(0);
}

$tokens = gatherTokens(true, $token);

$editting_token = null;
if(isset($tokens[$_POST["updateToken"]])){
    $editting_token = $tokens[$_POST["updateToken"]];
}

$newPermissions = array();
$description = null;
$maxPermissions=1000;
$x;
foreach($_POST as $key => $value){
    if($x >= $maxPermissions){
        header("HTTP/1.1 400 Bad request");
        echo "<h1>You've reached $x permissions, it's too many!</h1>";
        exit(0);
    }
    switch($key){
        case "description":
            $description = $value;
            continue 2;
        case "returnTo":
            continue 2;
        case "updateToken":
            continue 2;
        case "token":
            continue 2;
    }
    if(empty($value)){
        continue;
    }
    array_push($newPermissions, $value);
    $x++;
}

if((!userHasAllThesePermissions($newPermissions, $token)) && (!userHasAnyOfThesePermissions(array("admin"), $token)) ){
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>This user/token doesn't have this permissions and therefore can't delegate them!</h1>";
    exit(0);
}

$editting_token["permissions"] = $newPermissions;
$editting_token["description"] = $description;

saveToken($editting_token);

if(isset($_POST["returnTo"])){
    header("Location: " . $_POST["returnTo"]);
}