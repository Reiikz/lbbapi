<?php

include_once APP_ROOT . "/core/core.php";




if(session_status() != PHP_SESSION_ACTIVE){
    session_start();
} 

if(!isset($_SESSION["username"]))
{
    header("Location: " . getPathClientWebRoot() . "/cpanel/auth/");
    exit(0);
}
