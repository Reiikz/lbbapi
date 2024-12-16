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
            
    </head>

    <body>

        <div class="wrapper">
            <form method="POST" action="../../API/user/auth.php" >
                    Username:<br/> <input type="text" placeholder="Username" name="user" /><br/>
                    Password:<br/> <input type="password" placeholder="Password" name="password" /><br/>
                    <input type="submit" value="Login"/>
            </form>

            <!--a href="./register.php">Register</a>
                You can't register silly
            -->
        </div>
        <div class="bg-image"></div>

    </body>

</html>