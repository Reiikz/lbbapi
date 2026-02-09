<!DOCTYPE html>
<htmL>

<?php

include_once APP_ROOT . "/core/core.php";

//why are you here if you're logged in?
if(session_status() != PHP_SESSION_ACTIVE){
    session_start();
}
if(isset($_SESSION["username"])){
    header("Location: " . getPathClientWebRoot() . "/cpanel");
    exit(0);
}

    ?>

    <head>
        <link rel="icon" type="image/x-icon" href="<?php echo getPathClientWebRoot(); ?>/resources/images/Logo.ico">
        <link rel="stylesheet" type="text/css" href="<?php echo getPathClientWebRoot(); ?>/resources/stylesheets/main.css"/>
        <title>
            LBBAPI: Registration
        </title>
    </head>

    <body>
        

        <div class="singleFormWrapper">
            <div class="loginForm">
                <div class="formTitle">
                    Register to LBBAPI
                </div>
                <div class='imageLogo'>
                    <img src="<?php echo getPathClientWebRoot(); ?>/resources/images/Logo.ico"/><br/>
                    "EL BBAPI"
                </div>
                <form method="POST" action="../../API/user/new.php" >
                    <div>
                        <div class="formSection">
                            Username:<br/> <input type="text" placeholder="Username" name="user" /><br/>
                        </div>
                        <div class="formSection">
                            Password:<br/> <input type="password" placeholder="Password" name="password" /><br/>
                        </div>
                        <div class="formSection">
                            Repeat Password:<br/> <input type="password" placeholder="Password" name="password2" /><br/>
                        </div>
                        <input type="submit" value="Register"/>
                    </div>
                </form>
                <a class="simpleA loginButtton" href="./index.php">Login</a>
            </div>
        </div>

        <div class="bg-image"></div>
    </body>

</html>