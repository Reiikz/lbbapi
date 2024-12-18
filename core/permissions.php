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

    foreach($_SESSION["permissions"] as $perm){
        if($perm == $permission){
            return true;
        }

        if(str_contains($perm, "*")){
            $wildcardPerm = substr($perm, 2, strlen($perm));
            if(str_ends_with($permission, $wildcardPerm)){
                return true;
            }
        }
    }
    return false;
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

function generateZonePermissions(){
    $permissions = array();
    $ZoneConfig = getCFG();
    foreach($ZoneConfig["zones"] as $zoneName){
        array_push($permissions, "$zoneName.delete");
        array_push($permissions, "$zoneName.update");
        array_push($permissions, "$zoneName.new");
        array_push($permissions, "*.$zoneName.delete");
        array_push($permissions, "*.$zoneName.update");
        array_push($permissions, "*.$zoneName.new");
    }
    return $permissions;
}

function arePermissionWithinCurrentDNSZones($permissions, $zones = null){
    if($zones == null){
        $zones = getCFG()["zones"];
    }
    $out = true;
    foreach($permissions as $permission){
        $withinZone = false;
        foreach($zones as $zone){
            if(str_contains($permission, $zone)){
                $withinZone = true;
                break;
            }
        }
        $out = $out && $withinZone;
    }
    return $out;
}