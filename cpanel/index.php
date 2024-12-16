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

            <div>

                <a href="./?p=show">Show</a>

            </div>

            <?php

                if(isset($_GET["p"])){
                    switch($_GET["p"]){
                        case "show":
                            include_once $GLOBALS["webroot"] . "/cpanel/cpanelSections/show.php";
                    }
                }else{
                    include_once $GLOBALS["webroot"] . "/cpanel/cpanelSections/show.php";
                }

            ?>

        </div>

    </body>


</html>