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

<style>

.updateSection {
  display: grid;
  padding: 1rem;
  border: 1px solid;
  margin: 10px;
}

.token {
    display: inline-block;
    width: fit-content;
    padding: 10px;
    margin: 5px;
    background-color: lightgray; 
}

.permissionSection {
  display: grid;
  grid-template-columns: 1fr 1fr;
}

</style>

<div class="content">

    <!-- <pre> -->

        <?php
            include_once $GLOBALS["webroot"] . "/core/token/common.php";
            include_once $GLOBALS["webroot"] . "/core/parser.php";
            include_once $GLOBALS["webroot"] . "/core/permissions.php";
            $tokens = gatherTokens();

            $generatedPermissions = generateZonePermissions();
        

            foreach($tokens as $tokenid => $token){
                echo "\n";
                echo "<form class='updateSection' Action='" . getPathClientWebRoot() . "/core/token/new.php' method='POST' >\n";
                
                echo "<div>\n";
                echo "Token:<div class='token'>$tokenid</div>\n";
                echo "</div>\n";

                if(!isset($token["description"])){
                    $token["description"] = "";
                }
                echo "<textarea name='description' Placeholder='description' >" . $token["description"] . "</textarea>\n";
                
                $generatedPermissionHTML = array();
                
                echo "<div class='permissionSection'>\n";
                $enabledPermissions = array();
                foreach($token["permissions"] as $permission){
                    array_push($enabledPermissions, $permission);
                    echo "<div><input class='checkbox' type='checkbox' name='" . htmlspecialchars($permission) . "' value='" . htmlspecialchars($permission) . "' checked />" . htmlspecialchars($permission) . "</div>\n";
                }

                foreach($generatedPermissions as $permission){
                    $checked = "";
                    if(in_array($permission, $token["permissions"])){
                        $checked = "checked";
                    }
                    if(in_array($permission, $enabledPermissions)){
                        continue;
                    }
                    echo "<div><input class='checkbox' type='checkbox' name='" . htmlspecialchars($permission) . "' value='" . htmlspecialchars($permission) . "' $checked />" . htmlspecialchars($permission) . "</div>\n";

                    array_push($generatedPermissionHTML, $permission);
                }
                echo "</div>\n";

                echo "<div class='send'>";
                echo "<input type='submit' value='save'/>";
                echo "</div>\n";

                echo "<input type='hidden' name='returnTo' value='" . $_SERVER['REQUEST_URI'] . "'/>";
                echo "<input type='hidden' name='token' value='" . $token['id'] . "'/>";

                echo "</form>\n";

            }

        ?>
        <pre>
        <?php
            echo var_export($tokens);
        ?>
        </pre>

    <!-- </pre> -->

</div>