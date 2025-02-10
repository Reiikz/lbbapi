<?php


$CONFIG_FILE_PATH=$GLOBALS["webroot"] . "/config/config.php";
if(file_exists($CONFIG_FILE_PATH)){
    include_once $CONFIG_FILE_PATH;
}else{
    header("HTTP/1.1 500 Internal server error!");
    echo "<h1>Is the API misconfigured?</h1>";
    exit(0);
}

include_once $GLOBALS["webroot"] . "/config/config.php";

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
    $text = "\$$subjectName = " . var_export($subject, TRUE) . ";";
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

function redirectIfNotLoggedIn(){
    include_once $GLOBALS["webroot"] . "/API/auth/redirectIfNotLoggedIn.php";
}

function getCacheDir(){
    return $GLOBALS["webroot"] . "/cache";
}

//I just realized I am over engineering this and making an md5sum of the file might be kinda pointless because it means reading the file twice.
//I'm not doing it for the database because of this fact.

function reparseZones(){
    include_once $GLOBALS["webroot"] . "/core/parser.php";
    $cacheDir = getCacheDir();
    if(!is_dir($cacheDir)){
        mkdir($cacheDir, 0700, true);
    }
    $cachedCFGPath = $cacheDir . "/cachedcfg.php";
    $cachedCFGSumPath = $cacheDir . "/cachedcfgmd5.php";
    $zoneConfigArray = bind9_zoneconfig_decode($GLOBALS["config"]["ZoneConfigFile"]);
    saveVariable($zoneConfigArray, "_ZONES", $cachedCFGPath);
    $sum = md5_file($GLOBALS["config"]["ZoneConfigFile"]);
    saveVariable($sum, "_ZONES_SUM", $cachedCFGSumPath);
}

function getCFG($domain = null){
    $cacheDir = getCacheDir();
    $cachedCFGPath = $cacheDir . "/cachedcfg.php";
    $cachedCFGSumPath = $cacheDir . "/cachedcfgmd5.php";
    if(file_exists($cachedCFGPath) && file_exists($cachedCFGSumPath)){
        include $cachedCFGSumPath;
        if($_ZONES_SUM != md5_file($GLOBALS["config"]["ZoneConfigFile"])){
            reparseZones();
        }
    }else{
        reparseZones();
    }
    include $cachedCFGPath;

    if($domain == null){
        return $_ZONES;
    }
    
    foreach($_ZONES["zones"] as $zone){
        // echo $zone;
        // echo $domain;
        if(str_contains($domain, $zone)){
            return $_ZONES[$zone];
        }
    }
    return null;
}

function sanitizeDNS($in){
    $val=preg_replace($GLOBALS["config"]["AllowedCharacters"], "", $in);
    if(is_array($val)){
        return $val[0];
    }else{
        return $val;
    }
}

function getIPv6RecordFromBind9Server($domain){
    $safeDomain = sanitizeDNS($domain);
    $output=null;
    exec("dig -t AAAA +short @" . $GLOBALS["config"]["DNS_SERVER"] . " $safeDomain", $output);
    if(isset($output[0])){
        return $output[0];
    }else{
        return false;
    }
}