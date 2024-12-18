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

    <pre>

        <?php
            include_once $GLOBALS["webroot"] . "/core/token/common.php";
            $tokens = gatherTokens();
            echo var_export($tokens);
        ?>

    </pre>

</div>