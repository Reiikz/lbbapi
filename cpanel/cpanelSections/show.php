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
<style>
    .DNSrecord {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr min-content min-content;
        margin: 1rem;
        background-color: rgba(0,0,0,0.1);
        padding: 1rem;
        border-radius: 3px;
    }
    
input[type="submit"]{
    margin-left: 5px;
}
</style>

<div class="content">
    <?php
        foreach($available_zones["zones"] as $zoneName){
            echo "<div class=\"DNSzone\">\n";
                echo "<div class=\"DNSZone_Title\">$zoneName</div>\n";
                
                $zonedb = bind9_zonedb_decode($available_zones[$zoneName]["file"]);

                foreach($zonedb["recordset"] as $recordName => $set){
                    foreach($set["types"] as $recordType){
                        // echo "<pre>";
                        // print_r($set);
                        // print_r($recordType);
                        // echo "</pre>";
                        foreach($set[$recordType] as $valueSet){
                            $ttl = $valueSet["ttl"];
                            echo "
                                <div class=\"DNSrecord\">
                                    <div>
                                        $recordName
                                    </div>
                                    <div>
                                        $ttl
                                    </div>
                                    <div>
                                        $recordType
                                    </div>
                                    <div>
                                        " . $valueSet["value"] . "
                                    </div>
                                    <div>
                                        <form method=\"POST\" Action=\"" . getPathClientWebRoot() . "/API/record/delete.php\" >
                                            <input type=\"submit\" value=\"Delete\" />
                                            <input type=\"hidden\" name=\"record\" value=\"$recordName\"/>
                                        </form>
                                    </div>
                                    <div>
                                        <form method=\"POST\" Action=\"" . getPathClientWebRoot() . "cpanel/cpanelSections/updateRecord.php\" >
                                            <input type=\"submit\" value=\"Edit\" />
                                            <input type=\"hidden\" name=\"record\" value=\"$recordName\"/>
                                        </form>
                                    </div>
                                </div>
                            ";
                        }
                    }
                }

            echo "</div>\n";
        }
        
    ?>
</div>

<?php

// echo "<pre>";
// $available_zones = bind9_zoneconfig_decode($CONFIG["ZoneConfigFile"]);
// print_r($available_zones);
// $database = bind9_zonedb_decode($available_zones[$available_zones["zones"][0]]["file"]);
// print_r($database);
// // echo "\n\n";
// // echo bind9_zoneconfig_encode($available_zones);
// // echo "\n\n";
// // echo bind9_zonedb_encode($database);


// echo "</pre>";

?>