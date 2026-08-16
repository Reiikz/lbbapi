<?php

include_once APP_ROOT . "/core/core.php";
redirectIfNotLoggedIn();


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


include_once APP_ROOT . "/core/permissions.php";
if(!userHasAnyOfThesePermissions(array("restartBind9", "admin"), $token, $secret)){
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>>:|</h1>";
    exit(0);
}

exec("sudo systemctl restart bind9", $ret);

header("Location: " . getPathClientWebRoot() . "/");