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

/*
    **************************
*/
    ?>

    <head>
        <link rel="icon" type="image/x-icon" href="<?php echo getPathClientWebRoot(); ?>/resources/images/Logo.ico">
        <link rel="stylesheet" type="text/css" href="<?php echo getPathClientWebRoot(); ?>/resources/stylesheets/main.css"/>
        <title>
            LBBAPI: Login
        </title>
    </head>

    <body>

        <div class="singleFormWrapper">

            <div class="loginForm">
                <div class="formTitle">
                    Login to LBBAPI Control Panel
                </div>
                <div class='imageLogo'>
                    <img src="<?php echo getPathClientWebRoot(); ?>/resources/images/Logo.ico"/><br/>
                    "EL BBAPI"
                </div>
                <form method="POST" action="../../API/user/auth.php" >
                    <div>
                        <div class="formSection">
                            Username:<br/> <input type="text" placeholder="Username" name="user" /><br/>
                        </div>
                        <div class="formSection">
                            Password:<br/> <input type="password" placeholder="Password" name="password" /><br/>
                        </div>
                        <div class="formSection">
                            <input type="submit" value="Login"/>
                        </div>
                    </div>
                </form>
            <div>

            <!--a href="./register.php">Register</a>
                You can't register silly
            -->
        </div>
        <div class="bg-image"></div>

    </body>

</html>