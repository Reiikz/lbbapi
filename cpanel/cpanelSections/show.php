<?php

include_once APP_ROOT . "/core/core.php";
redirectIfNotLoggedIn();

include_once APP_ROOT . "/core/parser.php";

$available_zones = bind9_zoneconfig_decode($CONFIG["ZoneConfigFile"]);

?>

<div class="content">

    <div class="cpanel-section-title">
            DNS Records
    </div>

    <div class="cpanel-section">

    <?php
        $zoneFilter = null;
        if(isset($_GET["zone"])){
            $zoneFilter = $_GET["zone"];
        }
        foreach($available_zones["zones"] as $zoneName){
            if($zoneFilter !== null){
                if($zoneFilter != $zoneName) continue;
            }
            echo "<div class=\"DNSzone\">\n";
                echo "<div class=\"DNSZone_Title\">DNS Start Of Authority: <tag class='dnsZoneName'>$zoneName</tag></div>\n";
                
                $zonedb = bind9_zonedb_decode($available_zones[$zoneName]["file"]);

                $authority = preg_replace("/\.$/", "", $zonedb["SOA"]);
                // neatDump($zonedb);
                foreach($zonedb["recordset"] as $recordName => $set){
                    // neatDump($set);
                    foreach($set["types"] as $recordType){
                        foreach($set[$recordType] as $valueSet){
                            $ttl = $valueSet["ttl"];
                            $name = $recordName;
                            if(!str_ends_with($name, ".")){
                                $name = "$name.$authority";
                            }

                            if(!userHasAnyOfThesePermissions(array("$name.show", "admin"))){
                                continue;
                            }

                            echo "
                                <div class=\"DNSrecord_list\">
                                    <div class='DNSRecord_description'>
                                        <div class='DNSrecord_list_section'>
                                            <div class='DNSrecord_list_value_title'>
                                                NAME    
                                            </div>
                                            <div class='DNSrecord_list_value'>
                                                $recordName
                                            </div>
                                        </div>
                                        <div class='DNSrecord_list_section'>
                                            <div class='DNSrecord_list_value_title'>
                                                TTL    
                                            </div>
                                            <div class='DNSrecord_list_value'>
                                                $ttl
                                            </div>
                                        </div>
                                        <div class='DNSrecord_list_section'>
                                            <div class='DNSrecord_list_value_title'>
                                                TYPE    
                                            </div>
                                            <div class='DNSrecord_list_value'>
                                                $recordType
                                            </div>
                                        </div>
                                        <div class='DNSrecord_list_section'>
                                            <div class='DNSrecord_list_value_title'>
                                                VALUE    
                                            </div>
                                            <div class='DNSrecord_list_value'>
                                                " . $valueSet["value"] . "
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <form method=\"POST\" Action=\"" . getPathClientWebRoot() . "/API/record/delete.php\" >
                                            <input type=\"submit\" value=\"Delete\" />
                                            <input type=\"hidden\" name=\"record\" value=\"$name\"/>
                                            <input type=\"hidden\" name=\"type\" value=\"$recordType\"/>
                                            <input type=\"hidden\" name=\"value\" value=\"" . $valueSet["value"] . "\"/>
                                            <input type=\"hidden\" name=\"returnTo\" value=\"" . getPathClientWebRoot() . "/\"/>
                                            <input type=\"hidden\" name=\"authority\" value=\"$authority\"/>
                                        </form>
                                    </div>
                                    <div>
                                        <form method=\"get\" Action=\"" . getPathClientWebRoot() . "/cpanel/" . "\" >
                                            <input type=\"hidden\" name=\"p\" value=\"update\"/>
                                            <input type=\"hidden\" name=\"record\" value=\"$name\"/>
                                            <input type=\"hidden\" name=\"authority\" value=\"$authority\"/>
                                            <input type=\"hidden\" name=\"type\" value=\"$recordType\"/>
                                            <input type=\"hidden\" name=\"ttl\" value=\"$ttl\"/>
                                            <input type=\"hidden\" name=\"value\" value=\"" . $valueSet["value"] . "\"/>
                                            <input type=\"hidden\" name=\"returnTo\" value=\"" . getPathClientWebRoot() . "/\"/>
                                            <input type=\"submit\" value=\"Edit\" />
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
// echo bind9_zonedb_encode($database);


// echo "</pre>";

?>

