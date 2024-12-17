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
redirectIfNotLoggedIn();
/*
    **************************
*/
include_once $GLOBALS["webroot"] . "/core/parser.php";

echo "<pre>";
$available_zones = bind9_zoneconfig_decode($CONFIG["ZoneConfigFile"]);
print_r($available_zones);
$database = bind9_zonedb_decode($available_zones[$available_zones["zones"][0]]["file"]);
print_r($database);
echo "\n\n";
echo bind9_zoneconfig_encode($available_zones);
echo "\n\n";
echo bind9_zonedb_encode($database);

echo "</pre>";

?>

<div class="content">
    <div class="DNSzone">
        <div class="DNSZone Title">
        </div>
    <?php
    ?>
    </div>
</div>