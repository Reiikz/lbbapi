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

    <body>
        

        <div class="wrapper">
            <form method="POST" action="../../API/user/new.php" >
                    Username:<br/> <input type="text" placeholder="Username" name="user" /><br/>
                    Password:<br/> <input type="password" placeholder="Password" name="password" /><br/>
                    Repeat Password:<br/> <input type="password" placeholder="Password" name="password2" /><br/>
                    <input type="submit" value="Register"/>
            </form>

            <a href="./index.php">Register</a>
        </div>

        <div class="bg-image"></div>
    </body>

</html>