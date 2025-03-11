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
?>

<div class="content">

    <div class="cpanel-section-title">
            Update password
    </div>

    <div class="cpanel-section">
        <form Action="<?php echo getPathClientWebRoot(); ?>/API/user/update.php" Method="POST">
            <input type="hidden" name="username" value="<?php echo $_SESSION["username"]; ?>" />
            <input type="hidden" name="returnTo" value="<?php echo getPathClientWebRoot(); ?>" />
            <input type="password" name="originalPassword" Placeholder="Old password" class="largerTextBox" />    
            <input type="password" name="password" Placeholder="New password" class="largerTextBox" />
            <input type="password" name="password2" Placeholder="Repeat new password" class="largerTextBox" />
            <input type="submit" Value="Save"/>
        </form>
    </div>

</div>