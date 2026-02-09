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

$query="short";

if(isset($_GET["q"])){
    $query=$_GET["q"];
}

include_once APP_ROOT . "/core/bind9/status.php";

switch($query){
    case "short":
        echo bind9_shortStatus();
    break;
}
