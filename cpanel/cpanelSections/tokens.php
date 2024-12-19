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

<div class="content">
    <div class="cpanel-section-title">
            Manage tokens
    </div>

    <div class="cpanel-section">

    <!-- <pre> -->

        <?php
            include_once $GLOBALS["webroot"] . "/core/token/common.php";
            include_once $GLOBALS["webroot"] . "/core/parser.php";
            include_once $GLOBALS["webroot"] . "/core/permissions.php";
            $tokens = gatherTokens();

            $generatedPermissions = generateZonePermissions();
            
            if($tokens != null){
                foreach($tokens as $tokenid => $token){
                    echo "\n";
    
                    echo "<div class='manageTokenSection'>\n\n";
    
                    echo "<form Action='" . getPathClientWebRoot() . "/core/token/new.php' method='POST' >\n\n";
                    
                    echo "<div class='token'>\n";
                    echo "Token:<div class='tokenValue'>$tokenid</div>\n";
                    echo "</div>\n";
    
                    if(!isset($token["description"])){
                        $token["description"] = "";
                    }
                    echo "\n<textarea name='description' Placeholder='description' >" . $token["description"] . "</textarea>\n";
                    
                    $generatedPermissionHTML = array();
                    
                    echo "\n<div class='tokenPermissionSection'>\n";
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
                    echo "</div>\n\n";
    
                    echo "<div><input type='text' placeholder='algo.dns' name='userDefined.delete' />Custom delete</div>\n";
                    echo "<div><input type='text' placeholder='algo.dns' name='userDefined.new' />Custom new</div>\n";
                    echo "<div><input type='text' placeholder='algo.dns' name='userDefined.update' />Custom Update</div>\n\n";
    
                    echo "<div class='send'>\n";
                    echo "<input type='submit' value='save'/>\n";
                    echo "</div>\n\n";
    
                    
    
                    echo "<input type='hidden' name='returnTo' value='" . $_SERVER['REQUEST_URI'] . "'/>\n";
                    echo "<input type='hidden' name='token' value='" . $token['id'] . "'/>\n";
    
                    echo "</form>\n";
    
                    echo "<form Action='" . getPathClientWebRoot() . "/core/token/delete.php' Method='POST'>\n";
    
                    echo "<input type='submit' value='Delete'/>\n";
                    echo "<input type='hidden' name='token' value='" . $token['id'] . "'/>\n";
                    echo "<input type='hidden' name='returnTo' value='" . $_SERVER['REQUEST_URI'] . "'/>\n";
    
                    echo "</form>\n";
    
    
                    echo "</div>\n";
    
                }
            }else{
                echo "No tokens yet";
            }
            

        ?>
        <pre>
        <?php
            // echo var_export($tokens);
        ?>
        </pre>

    <!-- </pre> -->
    </div>
</div>