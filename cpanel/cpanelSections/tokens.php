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

textarea {
  width: 100%;
  height: 5rem;
}

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
            
            if($tokens != null){
                foreach($tokens as $tokenid => $token){
                    echo "\n";
    
                    echo "<div class='updateSection'>\n\n";
    
                    echo "<form Action='" . getPathClientWebRoot() . "/core/token/new.php' method='POST' >\n\n";
                    
                    echo "<div>\n";
                    echo "Token:<div class='token'>$tokenid</div>\n";
                    echo "</div>\n";
    
                    if(!isset($token["description"])){
                        $token["description"] = "";
                    }
                    echo "\n<textarea name='description' Placeholder='description' >" . $token["description"] . "</textarea>\n";
                    
                    $generatedPermissionHTML = array();
                    
                    echo "\n<div class='permissionSection'>\n";
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
    
                    echo "<div><input type='text' name='userDefined.delete' />Custom delete</div>\n";
                    echo "<div><input type='text' name='userDefined.new' />Custom new</div>\n";
                    echo "<div><input type='text' name='userDefined.update' />Custom Update</div>\n\n";
    
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