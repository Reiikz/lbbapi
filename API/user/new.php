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

if(!isset($_POST["user"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No username</h1>";
    exit(0);
}

if(!isset($_POST["password"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No password</h1>";
    exit(0);
}

if(!isset($_POST["password2"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>Please repeat your password</h1>";
    exit(0);
}

if($_POST["password2"] != $_POST["password"]){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>Passwords didn't match</h1>";
    exit(0);
}

// save user to file

$USERS_FILE_PATH = $GLOBALS["webroot"] . "/users.php";

if(file_exists($USERS_FILE_PATH)){
    include_once $USERS_FILE_PATH;
}

if($CONFIG["EnableMaxUsers"]){
    if(isset($USERS)){
        if(count($USERS) >= $CONFIG["AllowedUserCount"]){
            header("HTTP/1.1 500 Server error!");
            echo "<h1>Max user</h1>";
            exit(0);
        }
    }
}

if(!isset($USERS)){
    $USERS = array();
}

if(!isset($USERIDS)){
    $USERIDS = array();
    if(count($USERS) > 0){
        foreach($USERS as $key => $value){
            $USERIDS[$value["username"]]=$key;
        }
    }
}

if(isset($USERIDS[$_POST["user"]])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>Username already exists!</h1>";
    exit(0);
}

array_push($USERS, array(
    "username" => $_POST["user"],
    "password" => password_hash($_POST["password"], PASSWORD_DEFAULT),
));

$USERIDS[$_POST["user"]]=count($USERIDS);

$text = "<?php\n\n";
$text .= "\$USERS = " . var_export($USERS, TRUE) . ";";
$text .= "\n\n";
$text .= "\$USERIDS = " . var_export($USERIDS, TRUE) . ";";

file_put_contents($USERS_FILE_PATH, $text, LOCK_EX);
chmod($USERS_FILE_PATH, 0700);

session_start();
$_SESSION["username"]=$_POST["user"];

if(count($USERIDS) == 1){
    include_once $GLOBALS["webroot"] . "/core/permissions.php";
    $_PERMISSIONS = array("admin");
    saveUserPermissions($_PERMISSIONS);
}

header("Location: " . getPathClientWebRoot());