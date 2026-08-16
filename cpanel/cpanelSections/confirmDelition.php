<?php

include_once APP_ROOT . "/core/core.php";
redirectIfNotLoggedIn();

include_once APP_ROOT . "/core/parser.php";

$name=$_POST["record"];
$recordType=$_POST["type"];
$value=$_POST["value"];
$returnTo=$_POST["returnTo"];
$authority=$_POST["authority"];

?>


<div class="content">

    <div class="cpanel-section">

    <div>
        Delete <?php echo "$name"; ?>?
    </div>
    <?php

        echo "<form method=\"POST\" Action=\"" . getPathClientWebRoot() . "/API/record/delete.php\" >
            <input type=\"submit\" value=\"Delete\" />
            <input type=\"hidden\" name=\"record\" value=\"$name\"/>
            <input type=\"hidden\" name=\"type\" value=\"$recordType\"/>
            <input type=\"hidden\" name=\"value\" value=\"" . $value . "\"/>
            <input type=\"hidden\" name=\"returnTo\" value=\"$returnTo\"/>
            <input type=\"hidden\" name=\"authority\" value=\"$authority\"/>
        </form>";

    ?>

    </div>
</div>