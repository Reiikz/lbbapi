<?php

include_once APP_ROOT . "/core/core.php";

if(!isset($_POST["username"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>username not given</h1>";
    exit(0);
}

$token=null;
if(isset($_POST["token"])){
    $token=$_POST["token"];
}

$secret=null;
if($token != null){
    if(session_status() != PHP_SESSION_ACTIVE){
        session_start();   
    }
    if(!isset($_POST["secret"])){
        header("HTTP/1.1 403 Bad request");
        echo "<h1>No secret!</h1>";
        exit(0);
    }else{
        $secret=$_POST["secret"];
    }
}

$updatedUser=false;
include_once APP_ROOT . "/core/permissions.php";
if(session_status() != PHP_SESSION_ACTIVE){
    session_start();
} 
if(userHasAnyOfThesePermissions(array("deleteUsers", "admin"), $token, $secret)){
    include_once APP_ROOT . "/users.php";

    $username=$_POST["username"];
    $id=$USERIDS[$username];
    unset($USERIDS[$username]);
    unset($USERS[$id]);

    $permissionsFile = APP_ROOT . "/userPermissions/" . md5($username) . ".php";

    if(file_exists($permissionsFile)){
        unlink($permissionsFile);
    }
    $updatedUser=true;
}

if($updatedUser){
    $USERS_FILE_PATH = APP_ROOT . "/users.php";
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