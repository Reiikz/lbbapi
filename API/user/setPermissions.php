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

$token = null;
if(isset($_POST["token"])){
    $token = $_POST["token"];
}

if(session_status() != PHP_SESSION_ACTIVE){
    session_start();
}

if(($token == null) && (!isset($_SESSION["username"]))){
    header("HTTP/1.1 400 Bad request!");
    echo "<h1>No session and no token given</h1>";
    exit(0);
}

include_once $GLOBALS["webroot"] . "/core/permissions.php";

if(!userHasAnyOfThesePermissions(array("admin"), $token)){
    header("HTTP/1.1 403 Forbidden!");
    echo "<h1>>:|!</h1>";
    exit(0);
}

$username="";
$_PERMISSIONS=array();
$maxPerms=1000;
$x=0;
foreach($_POST as $postKey => $postValue){
    if($x >  $maxPerms){
        header("HTTP/1.1 500 Internal server error!");
        echo "<h1>Somehow you've reached the maximum permissions per user of $maxPerms on this request!</h1>";
        exit(0);
        break;
    }
    if($postKey == "_username_"){
        $username = $postValue;
        continue;
    }
    if($postKey == "_returnTo_"){
        continue;
    }
    if(empty($postValue)){
        continue;
    }
    array_push($_PERMISSIONS, $postValue);
    $x++;
}

if(empty($username)){
    header("HTTP/1.1 400 Bad request!");
    echo "<h1>No username to give permissions to was given!</h1>";
    exit(0);
}

$permissionFile = $GLOBALS["webroot"] . "/userPermissions/" . md5($username) . ".php";
$text = "<?php\n\n";
$test .= "unset(\$_PERMISSIONS);\n\n";
$text .= "\$_PERMISSIONS = " . var_export($_PERMISSIONS, TRUE) . ";";
file_put_contents($permissionFile, $text, LOCK_EX);
chmod($permissionFile, 0700);


if(isset($_POST["_returnTo_"])){
    header("Location: " .  $_POST["_returnTo_"]);
}