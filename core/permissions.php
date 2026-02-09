<?php

include_once APP_ROOT . "/core/core.php";
include_once APP_ROOT . "/core/parser.php";
include_once APP_ROOT . "/core/token/common.php";


$GLOBALS["PERMISSIONS_LOCATION"]=APP_ROOT . "/userPermissions";

function getUserPermissionFilePath($username = null){
    $destFileName = null;
    if($username == null){
        if(!isset($_SESSION["username_md5"])){
            $_SESSION["username_md5"]=md5($_SESSION["username"]);
        }
        $destFileName = $_SESSION["username_md5"] . ".php";
    }else{
        $destFileName = md5($username) . ".php";
    }
    
    $permFile = $GLOBALS["PERMISSIONS_LOCATION"] . "/" . $destFileName;
    return $permFile;
}

function saveUserPermissions($array, $userPermFile = null){
    if($userPermFile == null){
        $userPermFile = getUserPermissionFilePath(); 
    }
    saveVariable($array, "_PERMISSIONS", $userPermFile);
}

function userHasPermission($permission, $token = null){
    $perms = null;
    if($token == null){
        $userPermFile = getUserPermissionFilePath();
        if(!file_exists($userPermFile)){
            return false;
        }
        
        include $userPermFile; 

        if(!isset($_SESSION["permissions"])){       
            $_SESSION["permissions"] = $_PERMISSIONS;
        }

        $perms = $_PERMISSIONS;
    }else{
        if(is_array($token)){
            $perms = $token["permissions"];
            if(session_status() == PHP_SESSION_ACTIVE){
                $_SESSION["permissions"] = $perms;
            }
        }else{
            $token = readToken($token);
            if($token == null){
                echo "no token found!<br/>\n";
                return false;
            }
            $perms = $token["permissions"];
            if(session_status() == PHP_SESSION_ACTIVE){
                $_SESSION["permissions"] = $perms;
            }
        }
    }
    
    // print_r($perms);
    // print_r($permission);

    foreach($perms as $perm){
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

function userHasAllThesePermissions($array, $token = null){
    $hasEm = true;
    foreach($array as $permission){
        $hasEm = $hasEm && userHasPermission($permission, $token);
    }
    return $hasEm;
}

function userHasAnyOfThesePermissions($array, $token = null){
    foreach($array as $permission){
        if(userHasPermission($permission, $token)){
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
        
        $db = bind9_zonedb_decode($ZoneConfig[$zoneName]["file"]);
        foreach($db["recordset"] as $recordName => $records){
            if(str_ends_with($recordName, ".")) continue;
            array_push($permissions, "$recordName.$zoneName.delete");
            array_push($permissions, "$recordName.$zoneName.update");
            array_push($permissions, "$recordName.$zoneName.new");
            array_push($permissions, "*.$recordName.$zoneName.delete");
            array_push($permissions, "*.$recordName.$zoneName.update");
            array_push($permissions, "*.$recordName.$zoneName.new");
        }
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