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

function readToken($token){
    $tokenPath = $GLOBALS["webroot"] . "/tokens";

    $it = new RecursiveDirectoryIterator($tokenPath);
    foreach(new RecursiveIteratorIterator($it) as $file){
        if(!str_ends_with($file, ".php")){
            continue;
        }
        if(explode('.', basename($file))[0] == $token){
            $tokenPath = $file;
        }
    }
    $_TOKEN = null;
    if(!file_exists($tokenPath)){
        if(!is_dir(dirname($tokenPath))){
            mkdir(dirname($tokenPath));
            chmod(dirname($tokenPath), 0750);
        }
        return null;
    }else{
        include $tokenPath;
    }
    return $_TOKEN;
}

function getTokenPath($t, $user = null){
    $token = preg_replace("/[^0-9,A,B,C,D,E,F]/", "", $t);
    if($user != null){
        return $GLOBALS["webroot"] . "/tokens/$user/$token.php";    
    }
    return $GLOBALS["webroot"] . "/tokens/$token.php";
}

function saveToken(&$token){
    $tokenPath = $GLOBALS["webroot"] . "/tokens/" . $token["username_md5"] . "/" . $token["id"] . ".php";
    if(!is_dir(dirname($tokenPath))){
        mkdir(dirname($tokenPath));
        chmod(dirname($tokenPath), 0750);
    }
    // echo $tokenPath;
    $token["path"]=$tokenPath;
    return saveVariable($token, "_TOKEN", $tokenPath);
}

function _gatherTokensInPath($tokenPath){
    if(!is_dir($tokenPath)){
        return null;
    }
    $files = scandir($tokenPath);
    $files = array_diff($files, array('.', '..'));
    $tokens = array();
    foreach($files as $file){
        $path = "$tokenPath/$file";
        if(preg_match("/\.php$/", $path)){
            include $path;
            $tokens[$_TOKEN["id"]] = $_TOKEN;
        }
    }
    if(count($tokens) == 0){
        return null;
    }
    return $tokens;
}

function gatherTokens($gatherAll = true, $token = null){
    //Make sure we can do the necessary checks
    include_once $GLOBALS["webroot"] . "/core/permissions.php";

    if(session_status() != PHP_SESSION_ACTIVE){
        session_start();
    }

    $willGatherAll=false;
    if($gatherAll){
        if(userHasAnyOfThesePermissions(array("admin"), $token)){
            $willGatherAll = true;
        }
    }
    //*****/////

    $tokens = array();
    $tokenPath = $GLOBALS["webroot"] . "/tokens";

    if($willGatherAll){
        $files = scandir($tokenPath);
        $files = array_diff($files, array('.', '..'));
        foreach($files as $file){
            $path = realpath("$tokenPath/$file");
            if(($ts = _gatherTokensInPath($path)) != null){
                $tokens += $ts;
            }
        }
    }else{
        $user = null;
        if($token != null){
            $user = $token["username_md5"];
        }else{
            $user = $_SESSION["username_md5"];
        }
        $tokenPath .= "/$user";
        return _gatherTokensInPath($tokenPath);
    }
    return $tokens;
}