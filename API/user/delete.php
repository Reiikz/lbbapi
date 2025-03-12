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

if(!isset($_POST["username"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>username not given</h1>";
    exit(0);
}

$token=null;
if(isset($_POST["token"])){
    $token=$_POST["token"];
}

$updatedUser=false;
include_once $GLOBALS["webroot"] . "/core/permissions.php";
if(session_status() != PHP_SESSION_ACTIVE){
    session_start();
} 
if(userHasAnyOfThesePermissions(array("deleteUsers", "admin"), $token)){
    include_once $GLOBALS["webroot"] . "/users.php";

    $username=$_POST["username"];
    $id=$USERIDS[$username];
    unset($USERIDS[$username]);
    unset($USERS[$id]);

    $permissionsFile = $GLOBALS["webroot"] . "/userPermissions/" . md5($username) . ".php";

    if(file_exists($permissionsFile)){
        unlink($permissionsFile);
    }
    $updatedUser=true;
}

if($updatedUser){
    $USERS_FILE_PATH = $GLOBALS["webroot"] . "/users.php";
    $text = "<?php\n\n";
    $text .= "\$USERS = " . var_export($USERS, TRUE) . ";";
    $text .= "\n\n";
    $text .= "\$USERIDS = " . var_export($USERIDS, TRUE) . ";";

    file_put_contents($USERS_FILE_PATH, $text, LOCK_EX);
    chmod($USERS_FILE_PATH, 0700);
}

if(isset($_POST["returnTo"])){
    header("Location: " . $_POST["returnTo"]);
}