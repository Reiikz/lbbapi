<?php

include_once APP_ROOT . "/core/core.php";

if(!isset($_POST["username"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>username not given</h1>";
    exit(0);
}

$token = null;
if(isset($_POST["token"])){
    $token = $_POST["token"];
}

//if we got password we chaning it
if(isset($_POST["password"])){
    if(!empty($_POST["password"])){
        if(strlen($_POST["password"]) > 64){
            header("HTTP/1.1 400 Bad request");
            echo "<h1>No password</h1>";
            exit(0);
        }

        if(!isset($_POST["password2"])){
            header("HTTP/1.1 400 Bad request");
            echo "<h1>password2 not given</h1>";
            exit(0);
        }
    
        if($_POST["password"] != $_POST["password2"]){
            header("HTTP/1.1 400 Bad request");
            echo "<h1>password2 not match password</h1>";
            exit(0);
        }

        if(!isset($_POST["originalPassword"])){
            header("HTTP/1.1 400 Bad request");
            echo "<h1>originalPassword not match password</h1>";
            exit(0);
        }
    
        //check weather we're admin or we're chaning our own password
        $updatedUser=false;
        if(session_status() != PHP_SESSION_ACTIVE){
            session_start();
        } 
        // no session, then exit if max user reached
        if(isset($_SESSION["username"])){
            if($_SESSION["username"] == $_POST["username"]){

                include_once APP_ROOT . "/users.php";

                if(password_verify($_POST["originalPassword"], $USERS[$USERIDS[$_SESSION["username"]]]["password"])){
                    $USERS[$USERIDS[$_SESSION["username"]]]["password"] = password_hash($_POST["password"], PASSWORD_DEFAULT);
                }

                $updatedUser = true;
            }else{
                include_once APP_ROOT . "/users.php";
                include_once APP_ROOT . "/core/permissions.php";
                if(userHasAnyOfThesePermissions(array("admin"), $token)){
                    if(password_verify($_POST["originalPassword"], $USERS[$USERIDS[$_SESSION["username"]]]["password"])){
                        $USERS[$USERIDS[$_POST["username"]]]["password"] = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    }
                    $updatedUser = true;
                }else{
                    header("HTTP/1.1 403 Forbidden");
                    echo "<h1>>:|</h1>";
                    exit(0);
                }
            }
        }
    }
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