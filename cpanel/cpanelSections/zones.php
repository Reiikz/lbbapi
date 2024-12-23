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


$available_zones = bind9_zoneconfig_decode($CONFIG["ZoneConfigFile"]);

?>


<div class="content">

    <?php


        foreach($available_zones as $zone){

        }

        foreach($available_zones["zones"] as $zoneName){
            $zone = $available_zones[$zoneName];
            echo "<pre>";
            print_r($zone);
            echo "</pre>";

        }

    ?>

</div>