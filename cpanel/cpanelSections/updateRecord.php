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

if(!isset($_GET["record"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No record!</h1>";
    exit(0);
}

if(!isset($_GET["type"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No type!</h1>";
    exit(0);
}

if(!isset($_GET["value"])){
    header("HTTP/1.1 400 Bad request");
    echo "<h1>No value!</h1>";
    exit(0);
}

$authority="";
if(isset($_GET["authority"])){
    $authority = $_GET["authority"];
}

$domain=$_GET["record"];

?>

<div class="content">
    <div class="cpanel-section-title">
            Update DNS record
    </div>

    <div class="cpanel-section">

    <?php

        include_once $GLOBALS["webroot"] . "/core/manageDomainDatabase.php";
        
        echo "<div class='DNSZone_Tittle'>\n";
        echo "Zone: $authority\n";
        echo "</div>\n";
    ?>

    <div class='UpdateForm'>
        <form method="POST" action="<?php echo getPathClientWebRoot(); ?>/API/record/updateRecord.php">
            <div>
                Editting record: <?php echo "$domain\n"; ?>
            </div>

            <div>
                Type: <?php echo $_GET["type"] ?>
            </div>

            <input type="hidden" name="record" value="<?php echo $_GET["record"]; ?>" />
            <input type="hidden" name="type" value="<?php echo $_GET["type"]; ?>" />
            <input type="hidden" name="value" value="<?php echo $_GET["value"]; ?>" />
            Value: <input type="text" name="newValue" value="<?php echo $_GET["value"]; ?>" />
            <input type="hidden" name="newTTL" value="<?php echo $_GET["ttl"]; ?>" />
            TTL: <input type="text" name="newTTL" value="<?php echo $_GET["ttl"]; ?>" />

            <input type="hidden" name="returnTo" value="<?php echo $_GET["returnTo"]; ?>" />
            <input type="hidden" name="authority" value="<?php echo $_GET["authority"]; ?>" />
            <input type="submit" value="Update" />
        </form>

    </div>


    </div>
</div>