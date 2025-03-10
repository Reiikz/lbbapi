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
        
    <form Action="<?php echo getPathClientWebRoot(); ?>/API/zone/new.php" Method="POST" >
        <div>
            Zone: <input type="text" Placeholder="zone.net" name="zone" />
        </div>
        <div>
            Zone server: <input type="text" Placeholder="zone.net" name="zoneServer" />
        </div>
        <?php
            echo "<input type=\"hidden\" name=\"returnTo\" value=\"" . strtok($_SERVER['REQUEST_URI'], '?') . "\"/>";
        ?>
        <div>
            <input type="submit" value="Save" />
        </div>
    </form>

</div>