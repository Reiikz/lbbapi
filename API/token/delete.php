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
    if(!userHasAnyOfThesePermissions(array("token.delete", "admin"), $token, $secret)){
        header("HTTP/1.1 403 Forbidden");
        echo "<h1>This token can't delete other other tokens token.delete!</h1>";
        exit(0);
    }
}

$secret=null;
if($token != null){
    if(session_status() != PHP_SESSION_ACTIVE){
        session_start();   
    }
    if(!isset($_POST["secret"])){
        header("HTTP/1.1 403 Bad request");
        echo "<h1>No secret!</h1>";
        exit(0);
    }else{
        $secret=$_POST["secret"];
    }
}

if(!isset($_POST["deleteToken"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>no newToken value!</h1>";
    exit(0);
}

if((!userHasAnyOfThesePermissions(array("admin", "token.delete"), $token, $secret)) ){
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>You can't delete your own tokens!</h1>";
    exit(0);
}

$tokens = gatherTokens(true, $token);

if(isset($tokens[$_POST["deleteToken"]])){
    $token = $tokens[$_POST["deleteToken"]];

    unlink($token["path"]);

    try {
        rmdir(dirname($token["path"]));
    }catch(Exception $e){}
}

if(isset($_POST["returnTo"])){
    header("Location: " . $_POST["returnTo"]);
}