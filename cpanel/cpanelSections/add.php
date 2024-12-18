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

        foreach($available_zones["zones"] as $zone){

            echo "<div class=\"DNSZoneTitle\">\n";
            echo "Add record for zone: " . $zone . "\n";
            echo "</div>\n";
            
            echo "<form Action=\"" . getPathClientWebRoot() . "/API/record/new.php\" Method=\"POST\">\n";

            echo "<input type='text' name='record' placeholder='Record name'/>\n";
            echo "<input type='text' name='ttl' placeholder='TTL' value='60'/>\n";
            echo "<select name='type'>\n";
            echo "<option value='A'>A</option>\n";
            echo "<option value='AAAA'>AAAA</option>\n";
            echo "<option value='CNAME'>CNAME</option>\n";
            echo "<option value='TXT'>TXT</option>\n";
            echo "<option value='NS'>NS</option>\n";
            echo "</select>\n";
            echo "<input type='text' name='value' placeholder='Record value'/>\n";
            echo "<input type='hidden' name='authority' Value='$zone'/>";
            echo "<input type='submit' Value='Add'/>";
            echo "<input type=\"hidden\" name=\"returnTo\" value=\"" . strtok($_SERVER['REQUEST_URI'], '?') . "\"/>";

            echo "</form>\n";
        }

    ?>

</div>