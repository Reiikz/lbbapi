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
/*
    **************************
*/

if(!isset($_POST["user"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No username</h1>";
    exit(0);
}

if(!isset($_POST["password"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No password</h1>";
    exit(0);
}

$USERS_FILE_PATH = $GLOBALS["webroot"] . "/users.php";

if(file_exists($USERS_FILE_PATH)){
    include_once $USERS_FILE_PATH;
}else{
    header("HTTP/1.1 500 Internal server error!");
    echo "<h1>No username</h1>";
    exit(0);
}

if(!isset($USERIDS[$_POST["user"]])){
    header("Location: " . getPathClientWebRoot() . "/cpanel/auth");
    exit(0);
}


if(!password_verify($_POST["password"], $USERS[$USERIDS[$_POST["user"]]]["password"]) ) {
    header("Location: " . getPathClientWebRoot() . "/cpanel/auth");
    exit(0);
}


session_start();
$_SESSION["username"]=$_POST["user"];

header("Location: " . getPathClientWebRoot());

