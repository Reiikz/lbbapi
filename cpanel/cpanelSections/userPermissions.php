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

include_once $GLOBALS["webroot"] . "/core/permissions.php";

if(!userHasAnyOfThesePermissions(array("admin"))){
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>;|!</h1>";
    exit(0);
}

include_once $GLOBALS["webroot"] . "/users.php";

?>

<div class="content">
    <div class="cpanel-section-title">
            Set user Permissions
    </div>

    <div class="cpanel-section">

        <?php   

            foreach($USERS as $key => &$value){
                echo $value["username"];
                echo "<br/>";
            }

        ?>

    </div>
</div>
