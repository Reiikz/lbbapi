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
                $permissionFile = $GLOBALS["webroot"] . "/userPermissions/" . md5($value["username"]) .".php";
                if(file_exists($permissionFile)){
                    include "$permissionFile";
                }
                echo "<div class='manageTokenSection'>\n\n";
                    echo "user: " . $value["username"] . "<br/>\n";
                    echo "<br/>\n";

                        echo "<form Action='" . getPathClientWebRoot() . "/API/user/setPermissions.php" . "' Method='POST'>\n";
                            echo "<input type='hidden' name='_username_' value='" . $value["username"] . "' />";
                            if(isset($_PERMISSIONS)){
                                foreach($_PERMISSIONS as $permissionID => $permission){
                                    echo "<div><input class='checkbox' type='checkbox' name='" . htmlspecialchars($permission) . "' value='" . htmlspecialchars($permission) . "' checked />" . htmlspecialchars($permission) . "</div>\n";
                                }
                            }
                            for($x = 0; $x <= 5; $x++){
                                echo "<input type='text' name='permission_$x' Placeholder='Custom permission' />\n";
                            }
                            echo "<input type='submit' value='Save'/>\n";
                            echo "<input type='hidden' name='_returnTo_' value='" . getPathClientWebRoot() . "/cpanel/?p=userPermissions'/>\n";
                        echo "</form>\n";
                echo "</div>";
                unset($_PERMISSIONS);
            }

        ?>

    </div>
</div>
