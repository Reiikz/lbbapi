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
include_once $GLOBALS["webroot"] . "/core/parser.php";

/*
    **************************
*/

function isRecord($domain, $type, $value, $db = null){
    $ZoneConfig = getCFG($domain);
    // echo "</pre>";

    if($ZoneConfig == null){
        header("HTTP/1.1 400 Bad request");
        echo "<h1>I do not own the requested domain $domain</h1>";
        exit(0);
    }

    if($db == null){
        $db = bind9_zonedb_decode($ZoneConfig["file"]);
    }

    $authority = preg_replace("/\.$/", "", $db["SOA"]);

    $targetName=$domain;
    if($db["SOA"] != $targetName){
        $targetName = preg_replace("/\.$authority\$/", "", $targetName);
        // $targetName = str_replace($authority, "", $targetName);
        $targetName = preg_replace("/\.$/", "", $targetName);
    }

    if(!isset($db["recordset"][$targetName])){
        return false;
    }

    if(!isset($db["recordset"][$targetName][$type])){
        return false;
    }

    foreach ($db["recordset"][$targetName][$type] as $x => $set){
        if($set["value"] == $value){
            return true;
        }
    }

    return false;
    
}

function recordUpdate($domain, $type, $value, $newValue, $newTTL,  $db = null){
    

    // echo "<pre>";

    $ZoneConfig = getCFG($domain);

    // echo "</pre>";

    if($ZoneConfig == null){
        header("HTTP/1.1 400 Bad request");
        echo "<h1>I do not own the requested domain $domain</h1>";
        exit(0);
    }

    if($db == null){
        $db = bind9_zonedb_decode($ZoneConfig["file"]);
    }

    $authority = preg_replace("/\.$/", "", $db["SOA"]);

    $targetName=$domain;
    if($db["SOA"] != $targetName){
        $targetName = preg_replace("/\.$authority\$/", "", $targetName);
        // $targetName = str_replace($authority, "", $targetName);
        $targetName = preg_replace("/\.$/", "", $targetName);
    }

    if(!isRecord($domain, $type, $value, $db)){
        newRecord($domain, $type, $value, $db);
    }

    foreach($db["recordset"][$targetName][$type] as &$set){
        if($set["value"] == $value){
            $set["value"] = $newValue;
            $set["ttl"] = $newTTL;
            break;
        }
    }

    $db["SERIAL"]++;

    $rencodedDB = bind9_zonedb_encode($db);
    
    file_put_contents($ZoneConfig["file"], $rencodedDB, LOCK_EX);
    chmod($ZoneConfig["file"], 0750);
}

function newRecord($domain, $type, $value, $ttl, $db = null){
    

    $ZoneConfig = getCFG($domain);
    // echo "</pre>";

    if($ZoneConfig == null){
        header("HTTP/1.1 400 Bad request");
        echo "<h1>I do not own the requested domain $domain</h1>";
        exit(0);
    }

    if($db == null){
        $db = bind9_zonedb_decode($ZoneConfig["file"]);
    }

    $authority = preg_replace("/\.$/", "", $db["SOA"]);

    $targetName=$domain;
    if($db["SOA"] != $targetName){
        $targetName = preg_replace("/\.$authority\$/", "", $targetName);
        // $targetName = str_replace($authority, "", $targetName);
        $targetName = preg_replace("/\.$/", "", $targetName);
    }

    echo $domain;

    if(isRecord($domain, $type, $value, $db)){
        return;
    }

    if(!isset($type, $db["recordset"][$targetName])){
        $db["recordset"][$targetName] = array();
    }

    if(!isset($db["recordset"][$targetName]["types"])){
        $db["recordset"][$targetName]["types"] = array();
    }

    if(!in_array($type, $db["recordset"][$targetName]["types"])){
        array_push($db["recordset"][$targetName]["types"], $type);
    }

    if(!isset($db["recordset"][$targetName][$type])){
        $db["recordset"][$targetName][$type] = array();
    }

    array_push($db["recordset"][$targetName][$type], array(
        "value" => $value,
        "ttl" => $ttl,
    ));

    // echo "<pre>";
    
    // print_r($db);

    // echo "</pre>";

    $db["SERIAL"]++;

    $rencodedDB = bind9_zonedb_encode($db);

    
    file_put_contents($ZoneConfig["file"], $rencodedDB, LOCK_EX);
    chmod($ZoneConfig["file"], 0750);
}

function deleteRecord($domain, $type, $value, $db = null){
    if($db == null){
        
        // echo "<pre>";

        $ZoneConfig = getCFG($domain);

        // echo "</pre>";

        if($ZoneConfig == null){
            header("HTTP/1.1 400 Bad request");
            echo "<h1>I do not own the requested domain $domain</h1>";
            exit(0);
        }

        if($db == null){
            $db = bind9_zonedb_decode($ZoneConfig["file"]);
        }

        if(!isRecord($domain, $type, $value, $db)){
            return;
        }

        $authority = preg_replace("/\.$/", "", $db["SOA"]);

        $targetName=$domain;
        if($db["SOA"] != $targetName){
            $targetName = preg_replace("/\.$authority\$/", "", $targetName);
            // $targetName = str_replace($authority, "", $targetName);
            $targetName = preg_replace("/\.$/", "", $targetName);
        }
        // echo "<br/>delete target: $targetName";
        
        foreach ($db["recordset"][$targetName][$type] as $x => $set){
            if($set["value"] == $value){
                unset($db["recordset"][$targetName][$type][$x]);
            }
        }

        foreach($db["recordset"][$targetName]["types"] as $x => $type){
            if(count($db["recordset"][$targetName][$type]) == 0){
                unset($db["recordset"][$targetName][$type]);
                unset($db["recordset"][$targetName]["types"][$x]);
            }
        }

        if(count($db["recordset"][$targetName]["types"]) == 0){
            unset($db["recordset"][$targetName]);
        }

        $db["SERIAL"]++;

        $rencodedDB = bind9_zonedb_encode($db);

        file_put_contents($ZoneConfig["file"], $rencodedDB, LOCK_EX);
        chmod($ZoneConfig["file"], 0750);
        // echo "<pre>";
        // print_r($db);
        // echo "</pre>";       

        
    }
    
}