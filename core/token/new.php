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

if(!isset($_POST["description"])){
    $_POST["description"]="";
}

if(!isset($_POST["token"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No token!</h1>";
    exit(0);
}

include_once $GLOBALS["webroot"] . "/core/permissions.php";

if(!userHasAnyOfThesePermissions(array("admin"))){
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>Can't create token!</h1>";
    exit(0);
}

$zones = getCFG()["zones"];

$zonePerms = generateZonePermissions();

$checkedGeneratedPermissions = array();

foreach($zonePerms as $permission){
    $key = str_replace(".", "_", $permission);
    if(isset($_POST[$key])){
        if(in_array($_POST[$key], $zonePerms)){
            array_push($checkedGeneratedPermissions, $permission);
        }
    }
}

$userDefinedPermissions = array();

if(isset($_POST["userDefined_delete"])){
    if(!empty($_POST["userDefined_delete"])){
        array_push($userDefinedPermissions, $_POST["userDefined_delete"] . ".delete");
    }
}

if(isset($_POST["userDefined_new"])){
    if(!empty($_POST["userDefined_new"])){
        array_push($userDefinedPermissions, $_POST["userDefined_new"] . ".new");
    }
}

if(isset($_POST["userDefined_update"])){
    if(!empty($_POST["userDefined_update"])){
        array_push($userDefinedPermissions, $_POST["userDefined_update"] . ".update");
    }
}

if(!arePermissionWithinCurrentDNSZones($userDefinedPermissions, $zones)){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>Permission is not within current zones!</h1>";
    exit(0);
}

$permissions = array_merge($userDefinedPermissions, $checkedGeneratedPermissions);

$token = array(
    "id" => preg_replace("/[^0-9,A,B,C,D;E,F]/", "", $_POST["token"]),
    "permissions" => $permissions,
    "description" => $_POST["description"],
);


include_once $GLOBALS["webroot"] . "/core/token/common.php";

echo saveToken($token);

echo "<pre>";
echo "GENERATED PERMS: " . var_export($zonePerms) . "\n\n";
echo "POST: " . var_export($_POST) . "\n\n";
echo "GENERATED ENABLED PERMS: " .  var_export($checkedGeneratedPermissions) . "\n\n";
echo "USER DEFINED: " . var_export($userDefinedPermissions) . "\n\n";
echo "zones: " . var_export($zones) . "\n\n";
echo "combined: " . var_export($permissions) . "\n\n";
echo "FINAL TOKEN: " . var_export($token) . "\n\n";
echo "</pre>";

if(isset($_POST["returnTo"])){
    header("Location: " . $_POST["returnTo"]);
}




