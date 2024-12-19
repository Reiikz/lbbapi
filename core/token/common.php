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
    $tokenPath = $GLOBALS["webroot"] . "/tokens/$token.php";
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

function getTokenPath($t){
    $token = preg_replace("/[^0-9,A,B,C,D,E,F]/", "", $t);
    return $GLOBALS["webroot"] . "/tokens/$token.php";
}

function saveToken($token){
    $tokenPath = $GLOBALS["webroot"] . "/tokens/" . $token["id"] . ".php";
    if(!is_dir(dirname($tokenPath))){
        mkdir(dirname($tokenPath));
        chmod(dirname($tokenPath), 0750);
    }
    // echo $tokenPath;
    return saveVariable($token, "_TOKEN", $tokenPath);
}

function gatherTokens(){
    $tokenPath = $GLOBALS["webroot"] . "/tokens";
    if(!is_dir($tokenPath)){
        return null;
    }
    $files = scandir($tokenPath);
    $files = array_diff($files, array('.', '..'));
    $tokens = array();
    foreach($files as $file){
        $path = "$tokenPath/$file";
        // echo "$path\n";
        // var_dump(file_exists($path));
        // echo "\n";
        if(preg_match("/\.php$/", $path)){
            include $path;
            // echo "including";
            $tokens[$_TOKEN["id"]] = $_TOKEN;
        }
    }
    if(count($tokens) == 0){
        return null;
    }
    return $tokens;
}