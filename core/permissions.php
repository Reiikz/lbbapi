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
redirectIfNotLoggedIn();
/*
    **************************
*/

$GLOBALS["PERMISSIONS_LOCATION"]=$GLOBALS["webroot"] . "/userPermissions";

function getUserPermissionFilePath(){
    if(!isset($_SESSION["md5"])){
        $_SESSION["md5"]=md5($_SESSION["username"]);
    }
    $permFile = $GLOBALS["PERMISSIONS_LOCATION"] . "/" . $_SESSION["md5"] . ".php";
    return $permFile;
}

function saveUserPermissions($array, $userPermFile = null){
    if($userPermFile == null){
        $userPermFile = getUserPermissionFilePath(); 
    }
    saveVariable($array, "_PERMISSIONS", $userPermFile);
}

function userHasPermission($permission){
    $userPermFile = getUserPermissionFilePath();
    if(!file_exists($userPermFile)){
        return false;
    }

    if(!isset($_SESSION["permissions"])){
        include $userPermFile;    
        $_SESSION["permissions"] = $_PERMISSIONS;
    }
    
    return in_array($permission, $_SESSION["permissions"]);
}

function userHasAllThesePermissions($array){
    $hasEm = true;
    foreach($array as $permission){
        $hasEm = $hasEm && userHasPermission($permission);
    }
    return $hasEm;
}

function userHasAnyOfThesePermissions($array){
    foreach($array as $permission){
        if(userHasPermission($permission)){
            return true;
        }
    }
    return false;
}