<?php

include_once APP_ROOT . "/core/core.php";
redirectIfNotLoggedIn();
include_once APP_ROOT . "/core/parser.php";

$available_zones = bind9_zoneconfig_decode($CONFIG["ZoneConfigFile"]);



?>

<div class="content">
        
    <form Action="<?php echo getPathClientWebRoot(); ?>/API/zone/new.php" Method="POST" >
        <div>
            Zone: <input type="text" Placeholder="zone.net" name="zone" />
        </div>
        <div>
            Zone server: <input type="text" Placeholder="zone.net" name="zoneServer" />
        </div>
        <?php
            echo "<input type=\"hidden\" name=\"returnTo\" value=\"" . getPathClientWebRoot() . '/' . "\"/>";
        ?>
        <div>
            <input type="submit" value="Save" />
        </div>
    </form>

</div>