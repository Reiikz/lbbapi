<!DOCTYPE html>
<htmL>

    <?php

include_once APP_ROOT . "/core/core.php";
redirectIfNotLoggedIn();
include_once APP_ROOT . "/core/permissions.php";
include_once APP_ROOT . "/core/bind9/status.php";

    ?>

    <head>
            <meta charset="UTF-8">
            <meta http-equiv="Cache-Control" content="no-cache">

            
            <title>
                LBBAPI: CPANEL
            </title>
            
            <link rel="stylesheet" type="text/css" href="<?php echo getPathClientWebRoot(); ?>/resources/stylesheets/main.css"/>
            <link rel="icon" type="image/x-icon" href="<?php echo getPathClientWebRoot(); ?>/resources/images/Logo.ico">

            <script src="<?php echo getPathClientWebRoot() ?>/resources/js/utilities.js" ></script>
            
    </head>

    <body>

        <div class="wrapper">

            <div class="header">
                <div class='imageLogo'>
                    <a href="<?php echo getPathClientWebRoot(); ?>/cpanel/?p=listZones">
                        <img src="<?php echo getPathClientWebRoot(); ?>/resources/images/Logo.ico"/><br/>
                    </a>
                </div>

                <div class="userSection">
                    <?php
                        echo $_SESSION["username"];
                    ?>

                    <form method="POST" action="<?php echo getPathClientWebRoot(); ?>/API/auth/logout.php">
                        <input type="submit" value="Logout"/>
                    </form>
                </div>
                <div class="serverStatus">
                    <?php $bind9State=bind9_shortStatus(); ?>
                    DNS Server status: <tag class="bind9ServerStatus_<?php echo $bind9State;?>" ><?php echo $bind9State;?></tag>
                    <br/>
                    <tag class="smallDisclaimer">This state indicator is not representative of the DNS zone health, just weather bind9 is crashed acording to systemd</tag>
                </div>
            </div>

            <div class="Menu">

                <a href="./?p=updatePassword">Update Password</a>
                <a href="./?p=show">DNS Records</a>
                <a href="./?p=add">Add Record</a>
                <?php
                    if(userHasAnyOfThesePermissions(array("admin", "token.delete", "token.update"))){
                        echo "<a href='./?p=tokens'>Manage API Tokens</a>";
                    }
                    if(userHasAnyOfThesePermissions(array("admin", "token.new"))){
                        echo "<a href='./?p=newtoken'>Add new API Token</a>";
                    }


                    //ADMIN ONLY
                    if(userHasAnyOfThesePermissions(array("admin"))){
                        echo "<a href='./?p=newZone'>Add new DNS authority</a>";
                        echo "<a href='./?p=zones'>Manage DNS authorities</a>";
                        echo "<a href='./?p=userPermissions'>Set user permissions</a>";
                        echo "<a href='./?p=newUser'>Add user</a>";
                        echo "<a href='./?p=manageUsers'>Manage Users</a>";
                        echo "<form Action='" . getPathClientWebRoot() . "/API/bind9/restart.php' Method='POST'>
                                    <input type='submit' value='Restart Bind9'/>
                             </form>";
                    }
                ?>

            </div>

            <?php

                if(isset($_GET["p"])){
                    switch($_GET["p"]){
                        case "show":
                            include_once APP_ROOT . "/cpanel/cpanelSections/show.php";
                            break;
                        case "add":
                            include_once APP_ROOT . "/cpanel/cpanelSections/add.php";
                            break;
                        case "update":
                            include_once APP_ROOT . "/cpanel/cpanelSections/updateRecord.php";
                            break;
                        case "tokens":
                            include_once APP_ROOT . "/cpanel/cpanelSections/tokens.php";
                            break;
                        case "newtoken":
                            include_once APP_ROOT . "/cpanel/cpanelSections/newToken.php";
                            break;
                        case "newZone":
                            include_once APP_ROOT . "/cpanel/cpanelSections/newZone.php";
                            break;
                        case "zones":
                            include_once APP_ROOT . "/cpanel/cpanelSections/zones.php";
                            break;
                        case "userPermissions":
                            include_once APP_ROOT . "/cpanel/cpanelSections/userPermissions.php";
                            break;
                        case "manageUsers":
                            include_once APP_ROOT . "/cpanel/cpanelSections/manageUsers.php";
                            break;
                        case "newUser":
                            include_once APP_ROOT . "/cpanel/cpanelSections/newUser.php";
                            break;
                        case "updatePassword":
                            include_once APP_ROOT . "/cpanel/cpanelSections/updatePassword.php";
                            break;
                        case "listZones":
                            include_once APP_ROOT . "/cpanel/cpanelSections/listZones.php";
                            break;
                        default:
                            include_once APP_ROOT . "/cpanel/cpanelSections/show.php";
                            break;
                    }
                }else{
                    include_once APP_ROOT . "/cpanel/cpanelSections/listZones.php";
                }

            ?>

        </div>

    </body>


</html>