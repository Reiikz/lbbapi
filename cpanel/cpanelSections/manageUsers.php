<?php

include_once APP_ROOT . "/core/core.php";
redirectIfNotLoggedIn();

include_once APP_ROOT . "/core/permissions.php";

if(!userHasAnyOfThesePermissions(array("admin"))){
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>>:|!</h1>";
    exit(0);
}

include_once APP_ROOT . "/users.php";
?>


<div class="content">
    <div class="cpanel-section-title">
            Manage users
    </div>

    <div class="cpanel-section">

        <?php   

            foreach($USERS as $key => &$value){
                
                echo "<div class='manageTokenSection'>\n\n";
                    echo "user: " . $value["username"] . "<br/>\n";
                    echo "<br/>\n";

                    echo "<form Action='" . getPathClientWebRoot() . "/API/user/update.php' Method='POST'>\n"; 
                        echo "Password:<br/>\n";
                        echo "<input class='largerTextBox' type='password' name='originalPassword' Placeholder='" . $_SESSION["username"] . " password'/>\n";
                        echo "<input class='largerTextBox' type='password' name='password' Placeholder='New password for " . $value["username"] . "'/>\n";
                        echo "<input class='largerTextBox' type='password' name='password2' Placeholder='Repeat new password for " . $value["username"] . "'/>\n";
                        echo "<input type='submit' value='save'/>\n";
                        echo "<input type='hidden' name='username' value='" . $value["username"] . "'/>\n";
                        echo "<input type='hidden' name='returnTo' value='" . getPathClientWebRoot() . "'/>\n";
                        
                    echo "</form>\n";
                    echo "<form Action='" . getPathClientWebRoot() . "/API/user/delete.php' Method='POST'>\n";
                        echo "<input type='hidden' name='username' value='" . $value["username"] . "'/>\n";
                        echo "<input type='hidden' name='returnTo' value='" . getPathClientWebRoot() . "'/>\n";
                        echo "<input type='submit' value='Delete user'/>";
                    echo "</form>\n";
                echo "</div>";
            }

        ?>

    </div>
</div>