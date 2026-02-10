<?php

include_once APP_ROOT . "/core/core.php";
redirectIfNotLoggedIn();
include_once APP_ROOT . "/core/parser.php";

$available_zones = bind9_zoneconfig_decode($CONFIG["ZoneConfigFile"]);

?>

<div class="content">
    <div class="cpanel-section-title">
            Add new DNS record
    </div>

    <div class="cpanel-section">

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
            echo "<input type=\"hidden\" name=\"returnTo\" value=\"" . getPathClientWebRoot() . "/\"/>";

            echo "</form>\n";
        }

    ?>
    
    </div>
</div>