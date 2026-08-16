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

$query="short";

if(isset($_GET["q"])){
    $query=$_GET["q"];
}

include_once APP_ROOT . "/core/bind9/status.php";

include_once APP_ROOT . "/core/permissions.php";
if(!userHasAnyOfThesePermissions(array("statusBind9", "admin"), $token, $secret)){
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>>:|</h1>";
    exit(0);
}

switch($query){
    case "short":
        echo bind9_shortStatus();
    break;
}
