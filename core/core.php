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

if($GLOBALS["config"]["ErrorsOn"]){
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
}

function LBBAPI_errorOutOnNoConfigKey($key, $config){
    if(!array_key_exists($key, $config)){
        ini_set('error_prepend_string', '<pre style="color: #d9534f; background: #f8f9fa; padding: 12px; border-radius: 4px;">');
        ini_set('error_append_string', '</pre>');
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

function getClientIP(){
    if(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)){
        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    return $_SERVER['REMOTE_ADDR'];
}

function filterForIllegalChars($in){
    return preg_replace($GLOBALS["config"]["AllowedCharacters"], "_", $in);
}

function getProto(){
    if(isset($_SERVER['HTTPS'])){
        if($_SERVER['HTTPS'] == "on" || $_SERVER['HTTPS'] == 1 || $_SERVER['HTTPS'] == true){
            return "https";
        }else{
            return "http";
        }
    }else{
        if(isset($_SERVER["HTTP_X_FORWARDED_PROTO"])){
            if($_SERVER["HTTP_X_FORWARDED_PROTO"] == "https"){
                return "https";
            }else{
                return "http";
            }
        }
    }
    return null;
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
    $prefix = $_SERVER['HTTP_X_FORWARDED_PREFIX'] ?? "";
    $proto = getProto();
    $host = $_SERVER["HTTP_HOST"];
    $path = $prefix . str_replace($_SERVER["DOCUMENT_ROOT"], "", APP_ROOT);

    return "$proto://$host$path";
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

function neatDump($in){
    echo("<pre>");
    print_r($in);
    echo("</pre>");
}

function echoln($in){
    echo("<pre>$in</pre><br/>");
}