<?php

include_once APP_ROOT . "/core/core.php";
redirectIfNotLoggedIn();
include_once APP_ROOT . "/core/parser.php";

$available_zones = bind9_zoneconfig_decode($CONFIG["ZoneConfigFile"]);

?>

<div class="content">
    <div class="cpanel-section-title">
            Add new API token
    </div>

    <div class="cpanel-section">

    <form Action="<?php echo getPathClientWebRoot(); ?>/API/token/new.php" method="POST" >
        
        <div class="description">Description:<textarea name="description" placeholder="Description" ></textarea></div>

        <?php
            $token = bin2hex(random_bytes(32));
            $token = strtoupper($token);
        ?>

        <input type="hidden" name="newToken" value="<?php echo $token?>" />
        <input type="hidden" name="returnTo" value="<?php echo getPathClientWebRoot();?>/cpanel/?p=tokens" />
        
        <div class="token">
        Token:
            <div OnClick='copyInnerText(this);' OnDBClick='copyInnerText(this);' class="tokenValue">
            <?php
                echo $token;
            ?>
            </div>
            <br/>
            Use this token to interact witht the API after saving it!
        </div>

        <?php

            include_once APP_ROOT . "/core/permissions.php"; 

            $zonePerms = generateZonePermissions();

            echo "<div class='tokenPermissionSection'>\n";
            foreach($zonePerms as $permission){
                if(!userHasAnyOfThesePermissions(array("admin", $permission))){
                    continue;
                }
                echo "<div><input type='checkbox' name='$permission' value='$permission' >$permission</input></div>\n";
            }
            echo "</div>\n";
            
            echo "<div><input type='text' placeholder='algo.dns' name='userDefined.delete' value='' >Custom delete</input></div>\n";
            echo "<div><input type='text' placeholder='algo.dns' name='userDefined.new' value='' >Custom new</input></div>\n";
            echo "<div><input type='text' placeholder='algo.dns' name='userDefined.update' value='' >Custom Update</input></div>\n";
            
        ?>

    
        <div class="submit"><input type="submit" value="Save"/></div>

    </form>

    </div>

</div>