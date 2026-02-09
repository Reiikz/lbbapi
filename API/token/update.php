<?php

include_once APP_ROOT . "/core/core.php";
include_once APP_ROOT . "/core/permissions.php";
include_once APP_ROOT . "/core/token/common.php";

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
$x = 0;
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

if((!userHasAllThesePermissions($newPermissions + array("token.update"), $token)) && (!userHasAnyOfThesePermissions(array("admin"), $token))){
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>Can't update token, you don't have permission!</h1>";
    exit(0);
}

$editting_token["permissions"] = $newPermissions;
$editting_token["description"] = $description;

saveToken($editting_token);

if(isset($_POST["returnTo"])){
    header("Location: " . $_POST["returnTo"]);
}