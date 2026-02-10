<?php

include_once APP_ROOT . "/core/core.php";
redirectIfNotLoggedIn();
include_once APP_ROOT . "/core/parser.php";


$available_zones = bind9_zoneconfig_decode($CONFIG["ZoneConfigFile"]);

?>


<div class="content">

    <?php


        foreach($available_zones["zones"] as $zoneName){
            $zone = $available_zones[$zoneName];

            $zoneDB = bind9_zonedb_decode($zone["file"]);

            echo "<div class='DNS_AuthoritySection'>\n";
                
                echo "<div class='DNS_AuthorityTitle'>\n";

                    echo "$zoneName";

                echo "</div>\n";

                    echo "<div class='DNS_AuthorityUpdateForm'>\n";
                        echo "<form Action='" . getPathClientWebRoot() . "/API/zone/update.php' Method='POST'>\n";
                            echo "<div class='DNS_AuthorityInfoBox'>\n";
                                echo "<div class='DNS_AuthorityInfoBoxTitle'>\n";
                                    echo "Subdomain Count\n";
                                echo "</div>\n";
                                echo "<div class='DNS_AuthorityInfoBoxData'>\n";
                                    echo count($zoneDB["recordset"]) . "\n";
                                echo "</div>\n";
                            echo "</div>\n";

                            //zone name
                            echo "<input type='hidden' name='zone' value='" . $zoneName . "' > \n";

                            // $Text="Start of Authority";
                            // echo "<div class='DNS_AuthorityInfoBox'>\n";
                            //     echo "<div class='DNS_AuthorityInfoBoxTitle'>\n";
                            //         echo "$Text\n";
                            //     echo "</div>\n";
                            //     echo "<div class='DNS_AuthorityInfoBoxData'>\n";
                            //         echo "<input type='text' name='startOfAuthority' placeholder='$Text' value='" . $zoneDB["SOA"] . "' > \n";
                            //     echo "</div>\n";
                            // echo "</div>\n";

                            $Text="Default TTL";
                            echo "<div class='DNS_AuthorityInfoBox'>\n";
                                echo "<div class='DNS_AuthorityInfoBoxTitle'>\n";
                                    echo "$Text\n";
                                echo "</div>\n";
                                echo "<div class='DNS_AuthorityInfoBoxData'>\n";
                                    echo "<input type='text' name='defaultTTL' placeholder='$Text' value='" . $zoneDB["DEFAULT_TTL"] . "' > \n";
                                echo "</div>\n";
                            echo "</div>\n";

                            $Text="Authority server";
                            echo "<div class='DNS_AuthorityInfoBox'>\n";
                                echo "<div class='DNS_AuthorityInfoBoxTitle'>\n";
                                    echo "$Text\n";
                                echo "</div>\n";
                                echo "<div class='DNS_AuthorityInfoBoxData'>\n";
                                    echo "<input type='text' name='authorityServer' placeholder='$Text' value='" . $zoneDB["SOA_SERVER"] . "' > \n";
                                echo "</div>\n";
                            echo "</div>\n";

                            $Text="Serial";
                            echo "<div class='DNS_AuthorityInfoBox'>\n";
                                echo "<div class='DNS_AuthorityInfoBoxTitle'>\n";
                                    echo "$Text\n";
                                echo "</div>\n";
                                echo "<div class='DNS_AuthorityInfoBoxData'>\n";
                                    echo "<input type='text' name='serial' placeholder='$Text' value='" . $zoneDB["SERIAL"] . "' > \n";
                                echo "</div>\n";
                            echo "</div>\n";

                            $Text="Refresh";
                            echo "<div class='DNS_AuthorityInfoBox'>\n";
                                echo "<div class='DNS_AuthorityInfoBoxTitle'>\n";
                                    echo "$Text\n";
                                echo "</div>\n";
                                echo "<div class='DNS_AuthorityInfoBoxData'>\n";
                                    echo "<input type='text' name='refresh' placeholder='$Text' value='" . $zoneDB["REFRESH"] . "' > \n";
                                echo "</div>\n";
                            echo "</div>\n";

                            $Text="Retry transfer";
                            echo "<div class='DNS_AuthorityInfoBox'>\n";
                                echo "<div class='DNS_AuthorityInfoBoxTitle'>\n";
                                    echo "$Text\n";
                                echo "</div>\n";
                                echo "<div class='DNS_AuthorityInfoBoxData'>\n";
                                    echo "<input type='text' name='retryTransfer' placeholder='$Text' value='" . $zoneDB["RETRY"] . "' > \n";
                                echo "</div>\n";
                            echo "</div>\n";

                            $Text="Expires in";
                            echo "<div class='DNS_AuthorityInfoBox'>\n";
                                echo "<div class='DNS_AuthorityInfoBoxTitle'>\n";
                                    echo "$Text\n";
                                echo "</div>\n";
                                echo "<div class='DNS_AuthorityInfoBoxData'>\n";
                                    echo "<input type='text' name='expiresIn' placeholder='$Text' value='" . $zoneDB["EXPIRE"] . "' > \n";
                                echo "</div>\n";
                            echo "</div>\n";

                            $Text="Cache negative response for (s)";
                            echo "<div class='DNS_AuthorityInfoBox'>\n";
                                echo "<div class='DNS_AuthorityInfoBoxTitle'>\n";
                                    echo "$Text\n";
                                echo "</div>\n";
                                echo "<div class='DNS_AuthorityInfoBoxData'>\n";
                                    echo "<input type='text' name='negativeCacheTTL' placeholder='$Text' value='" . $zoneDB["NEGATIVE_CACHE_TTL"] . "' > \n";
                                echo "</div>\n";
                            echo "</div>\n";
                            
                            
                            echo "<input type='submit' value='Save' > \n";

                            echo "<input type=\"hidden\" name=\"returnTo\" value=\"" . getPathClientWebRoot() . "?p=zones\"/>";
                        echo "</form>\n";
                    echo "</div>\n";

                    echo "<div class='DNS_AuthorityDeleteForm'>\n";
                        echo "<form Action='" . getPathClientWebRoot() . "/API/zone/delete.php' Method='POST'>\n";
                            echo "<input type='hidden' name='zone' value='" . $zoneName . "' > \n";
                            echo "<input type='submit' value='Delete' > \n";
                            echo "<input type=\"hidden\" name=\"returnTo\" value=\"" . getPathClientWebRoot() . "/?p=zones\"/>";
                        echo "</form>\n";
                    echo "</div>\n";

            echo "</div>\n";

        }

        // foreach($available_zones["zones"] as $zoneName){
        //     $zone = $available_zones[$zoneName];
        //     echo "<pre>";
        //     print_r($zone);
        //     echo "</pre>";

        // }

    ?>

</div>
