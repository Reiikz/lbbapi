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




if(session_status() != PHP_SESSION_ACTIVE){
    session_start();
} 

if(!isset($_SESSION["username"]))
{
    header("Location: " . getPathClientWebRoot() . "/cpanel/auth");
    exit(0);
}
