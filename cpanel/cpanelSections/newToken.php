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
include_once $GLOBALS["webroot"] . "/core/parser.php";

$available_zones = bind9_zoneconfig_decode($CONFIG["ZoneConfigFile"]);

?>

<style>

textarea {
        width: calc(100% - 2rem);
        margin: 1rem;
        height: 6rem;
    }
.description {
    grid-area: description;
}

.token {
    grid-area: token;
}

.tokenValue{
    display: inline-block;
    width: fit-content;
    padding: 10px;
    margin: 5px;
    background-color: lightgray; 
}

.submit{
    grid-area: submit;
}
.content form {
    display: grid;
    grid-auto-columns: 1fr 1fr;
    grid-template: "description description" "token token"  "submit submit";
}
</style>

<div class="content">
    <div class="cpanel-section-title">
            Add new API token
    </div>

    <div class="cpanel-section">

    <form Action="<?php echo getPathClientWebRoot(); ?>/core/token/new.php" method="POST" >
        
        <div class="description">Description:<textarea name="description" placeholder="Description" ></textarea></div>

        <?php
            $token = bin2hex(random_bytes(32));
            $token = strtoupper($token);
        ?>

        <input type="hidden" name="token" value="<?php echo $token?>" />
        <input type="hidden" name="returnTo" value="<?php echo getPathClientWebRoot();?>/cpanel/?p=tokens" />
        
        <div class="token">
        Token:
            <div class="tokenValue">
            <?php
                echo $token;
            ?>
            </div>
            <br/>
            Use this token to interact witht the API after saving it!
        </div>

        <?php

            include_once $GLOBALS["webroot"] . "/core/permissions.php"; 

            $zonePerms = generateZonePermissions();

            foreach($zonePerms as $permission){
                echo "<div><input type='checkbox' name='$permission' value='$permission' >$permission</input></div>";
            }
            
            echo "<div><input type='text' name='userDefined.delete' value='' >Custom delete</input></div>";
            echo "<div><input type='text' name='userDefined.new' value='' >Custom new</input></div>";
            echo "<div><input type='text' name='userDefined.update' value='' >Custom Update</input></div>";
            
        ?>

    
        <div class="submit"><input type="submit" value="Save"/></div>

    </form>

    </div>

</div>