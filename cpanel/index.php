<!DOCTYPE html>
<htmL>

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
    ?>

    <head>
            <title>
                LBBAPI: CPANEL
            </title>
            
            <link rel="stylesheet" type="text/css" href="<?php echo getPathClientWebRoot(); ?>/resources/stylesheets/main.css"/>
    </head>

    <body>

        <div class="wrapper">

            <div class="header">
                Username: 
                <?php
                    echo $_SESSION["username"];
                ?>

                <form method="POST" action="<?php echo getPathClientWebRoot(); ?>/API/auth/logout.php">
                    <input type="submit" value="Logout"/>
                </form>
            </div>

            <div class="Menu">

                <a href="./?p=show">DNS Records</a>
                <a href="./?p=add">Add Record</a>
                <a href="./?p=tokens">Manage API Tokens</a>
                <a href="./?p=newtoken">Add new API Token</a>
                <a href="./?p=newZone">Add new DNS authority</a>
                <a href="./?p=zones">Manage DNS authorities</a>

            </div>

            <?php

                if(isset($_GET["p"])){
                    switch($_GET["p"]){
                        case "show":
                            include_once $GLOBALS["webroot"] . "/cpanel/cpanelSections/show.php";
                            break;
                        case "add":
                            include_once $GLOBALS["webroot"] . "/cpanel/cpanelSections/add.php";
                            break;
                        case "update":
                            include_once $GLOBALS["webroot"] . "/cpanel/cpanelSections/updateRecord.php";
                            break;
                        case "tokens":
                            include_once $GLOBALS["webroot"] . "/cpanel/cpanelSections/tokens.php";
                            break;
                        case "newtoken":
                            include_once $GLOBALS["webroot"] . "/cpanel/cpanelSections/newToken.php";
                            break;
                        case "newZone":
                            include_once $GLOBALS["webroot"] . "/cpanel/cpanelSections/newZone.php";
                            break;
                        case "zones":
                            include_once $GLOBALS["webroot"] . "/cpanel/cpanelSections/zones.php";
                            break;
                        default:
                            include_once $GLOBALS["webroot"] . "/cpanel/cpanelSections/show.php";
                            break;
                    }
                }else{
                    include_once $GLOBALS["webroot"] . "/cpanel/cpanelSections/show.php";
                }

            ?>

        </div>

    </body>


</html>