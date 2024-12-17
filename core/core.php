<?php

function getPathClientWebRoot(){
    if(!isset($GLOBALS["webroot"])){
        $path=__FILE__;
        while(!file_exists("$path/.stop")){
            $path=dirname($path);
        }
        $GLOBALS["webroot"]=$path;
    
    }
    return str_replace($_SERVER["DOCUMENT_ROOT"], "", $GLOBALS["webroot"]);
}

function saveVariable($subject, $subjectName, $file = null){
    $text .= "\$$subjectName = " . var_export($subject, TRUE) . ";";
    $text .= "\n\n";
    if($file != null){
        if(!is_dir(dirname($file))){
            mkdir(dirname($file), 0700, true);
        }
        $text = "<?php\n\n" . "unset(\$$subjectName);\n\n"  . $text;
        file_put_contents($file, $text, LOCK_EX);
        chmod($file, 0700);
    }
    return $text;
}

$CONFIG_FILE_PATH=$GLOBALS["webroot"] . "/config/config.php";
if(file_exists($CONFIG_FILE_PATH)){
    include_once $CONFIG_FILE_PATH;
}else{
    header("HTTP/1.1 500 Internal server error!");
    echo "<h1>Is the API misconfigured?</h1>";
    exit(0);
}

function redirectIfNotLoggedIn(){
    include_once $GLOBALS["webroot"] . "/API/auth/redirectIfNotLoggedIn.php";
}