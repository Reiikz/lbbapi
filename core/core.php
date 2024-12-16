<?php

function getPathClientWebRoot(){
    if(!isset($GLOBALS["webroot"])){
        $path=__FILE__;
        while(!file_exists("$path/.stop")){
            $path=dirname($path);
        }
        $GLOBALS["webroot"]=$path;
    
    }
    return str_replace($_SERVER["DOCUMENT_ROOT"], "", $GLOBALS["webroot"]);
}

$CONFIG_FILE_PATH=$GLOBALS["webroot"] . "/config/config.php";
if(file_exists($CONFIG_FILE_PATH)){
    include_once $CONFIG_FILE_PATH;
}else{
    header("HTTP/1.1 500 Internal server error!");
    echo "<h1>Is the API misconfigured?</h1>";
    exit(0);
}

function redirectIfNotLoggedIn(){
    include_once $GLOBALS["webroot"] . "/API/auth/redirectIfNotLoggedIn.php";
}