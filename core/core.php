<?php


$CONFIG_FILE_PATH=APP_ROOT . "/config/config.php";
if(file_exists($CONFIG_FILE_PATH)){
    include_once $CONFIG_FILE_PATH;
}else{
    header("HTTP/1.1 500 Internal server error!");
    echo "<h1>Is the API misconfigured?</h1><br/>";
    echo $CONFIG_FILE_PATH;
    exit(0);
}

include_once APP_ROOT . "/config/config.php";

//sanitize paths from configuration

$CONFIG["ZoneConfigFile"] = preg_replace("/\/{1}$/", "", $CONFIG["ZoneConfigFile"]);
$CONFIG["dbDirectory"] = preg_replace("/\/{1}$/", "", $CONFIG["dbDirectory"]);

$GLOBALS["config"]=$CONFIG;

function LBBAPI_errorOutOnNoConfigKey($key, $config){
    if(!array_key_exists($key, $config)){
        header("HTTP/1.1 500 Internal server error!");
        echo "<h1>Is the API misconfigured?</h1><br/>";
        echo "key: $key was missing from configuration";
        exit(0);
    }
}

// verify all necessary config is present
LBBAPI_errorOutOnNoConfigKey("EnableMaxUsers", $CONFIG);
LBBAPI_errorOutOnNoConfigKey("AllowedUserCount", $CONFIG);
LBBAPI_errorOutOnNoConfigKey("AllowedCharacters", $CONFIG);
LBBAPI_errorOutOnNoConfigKey("ZoneConfigFile", $CONFIG);
LBBAPI_errorOutOnNoConfigKey("dbDirectory", $CONFIG);


function filterForIllegalChars($in){
    return preg_replace($GLOBALS["config"]["AllowedCharacters"], "_", $in);
}

function getPathClientWebRoot(){
/*    
    if(!isset(APP_ROOT)){
        $path=__FILE__;
        while(!file_exists("$path/.stop")){
            $path=dirname($path);
        }
        APP_ROOT=$path;
    
    }
    return str_replace($_SERVER["DOCUMENT_ROOT"], "", APP_ROOT);
    */
    return str_replace($_SERVER["DOCUMENT_ROOT"], "", APP_ROOT);
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
    include_once APP_ROOT . "/API/auth/redirectIfNotLoggedIn.php";
}

function getCacheDir(){
    return APP_ROOT . "/cache";
}

//I just realized I am over engineering this and making an md5sum of the file might be kinda pointless because it means reading the file twice.
//I'm not doing it for the database because of this fact.

function reparseZones(){
    include_once APP_ROOT . "/core/parser.php";
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
    
    $selectedZone = null;
    foreach($_ZONES["zones"] as $zone){
        // echo $zone;
        // echo $domain;
        if(str_contains($domain, $zone)){
            if(strlen($zone) > strlen($selectedZone)){
                $selectedZone = $zone;
            }
        }
    }
    if($selectedZone != null){
        return $_ZONES[$selectedZone];
    }
    return null;
}

function LBBAPI_is_integer($number){
    $number = filter_var($number, FILTER_VALIDATE_INT);
    return ($number !== FALSE);
}