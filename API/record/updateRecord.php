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

/****************************/

$token = null;
if(!isset($_POST["token"])){
    session_start();
    if(!isset($_SESSION["username"])){
        header("HTTP/1.1 403 Bad request");
        echo "<h1>No token!</h1>";
        exit(0);
    }
}else{
    $token=$_POST["token"];
}

if(!isset($_POST["record"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No record!</h1>";
    exit(0);
}

if(!isset($_POST["type"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No type!</h1>";
    exit(0);
}

if(!isset($_POST["value"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No value!</h1>";
    exit(0);
}

if(!isset($_POST["newValue"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No newValue!</h1>";
    exit(0);
}

if(!isset($_POST["newTTL"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No newTTL!</h1>";
    exit(0);
}

if(!isset($_POST["authority"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No authority!</h1>";
    exit(0);
}

//validate request!
//make sure ttl is numeric
if(preg_match("/[^0-9]/", $_POST["newTTL"])) {
    header("HTTP/1.1 400 Bad request");
    echo "<h1>Invalid field ttl!</h1>";
    exit(0);
}

//record must only allowed characters dots and dashes
if(preg_match($GLOBALS["config"]["AllowedCharacters"], $_POST["record"])) {
    header("HTTP/1.1 400 Bad request");
    echo "<h1>Invalid field record!</h1>";
    exit(0);
}

//record type must be a valid DNS record type
if(!preg_match("/^(A|AAAA|AFSDB|APL|CAA|CDNSKEY|CDS|CERT|CNAME|CSYNC|DHCID|DLV|DNAME|DNSKEY|DS|EUI48|EUI64|HINFO|HIP|HTTPS|IPSECKEY|KEY|KX|LOC|MX|NAPTR|NS|NSEC|NSEC3|NSEC3PARAM|OPENPGPKEY|PTR|RP|RRSIG|SIG|SMIMEA|SOA|SRV|SSHFP|SVCB|TA|TKEY|TLSA|TSIG|TXT|URI|ZONEMD|){1}$/", $_POST["type"])) {
    header("HTTP/1.1 400 Bad request");
    echo "<h1>Invalid field type!</h1>";
    exit(0);
}

include_once $GLOBALS["webroot"] . "/core/records/validate.php";

validateRecordTypeQuit($_POST["type"], $_POST["newValue"]);

include_once $GLOBALS["webroot"] . "/core/permissions.php";
include_once $GLOBALS["webroot"] . "/core/manageDomainDatabase.php";


$domainPermission = null;
if(str_ends_with($_POST["record"], ".")){
    $domainPermission = $_POST["record"] . "update";
}else{
    $domainPermission = $_POST["record"] . ".update";
}
if(!userHasAnyOfThesePermissions(array($domainPermission, "admin"), $token)){
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>>:|!</h1>";
    exit(0);
}

$record = preg_replace("/[^A-Za-z0-9-.]/", "OwO", $_POST["record"]);
$authority = preg_replace("/[^A-Za-z0-9-.]/", "OwO", $_POST["authority"]);
$type = preg_replace("/[^A-Za-z0-9-.]/", "OwO", $_POST["type"]);
$value = $_POST["value"];
$newValue = $_POST["newValue"];
$newTtl = preg_replace("/[^0-9]/", "OwO", $_POST["newTTL"]);

if($_POST["type"] == "AAAA"){
    if(!recordUpdate($record, $authority, $type, $value, $newValue, $newTtl)){
        include_once $GLOBALS["webroot"] . "/ThirdParty/php-pear/Net_IPv6/IPv6.php";
        if(! recordUpdate($record, $authority, $type, Net_IPv6::compress($value), Net_IPv6::compress($newValue), $newTtl))
        //if(! recordUpdate($_POST["record"], $_POST["type"], Net_IPv6::compress($_POST["value"], true), Net_IPv6::compress($_POST["newValue"], true), $_POST["newTTL"]) ) {
            //if(!recordUpdate($_POST["record"], $_POST["type"], Net_IPv6::uncompress($_POST["value"], true), Net_IPv6::uncompress($_POST["newValue"], true), $_POST["newTTL"]) ){
            if(! recordUpdate($record, $authority, $type, Net_IPv6::uncompress($value), Net_IPv6::uncompress($newValue), $newTtl)){
                header("HTTP/1.1 500 Internal server error");
                echo "<h1>Could not update record!!</h1>";
                exit(0);
            }
        }
}else{
    recordUpdate($record, $authority, $type, $value, $newValue, $newTtl);
}

if(isset($_POST["returnTo"])){
    header("Location: " . $_POST["returnTo"]);
}

$ret = null;
exec("sudo systemctl reload bind9", $ret);


