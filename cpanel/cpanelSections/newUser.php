<?php


include_once APP_ROOT . "/core/core.php";
redirectIfNotLoggedIn();

include_once APP_ROOT . "/core/permissions.php";

if(!userHasAnyOfThesePermissions(array("admin"))){
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>;|!</h1>";
    exit(0);
}

include_once APP_ROOT . "/users.php";

?>

<div class="content">
    <div class="cpanel-section-title">
            Add new cpanel user
    </div>

    <div class="cpanel-section">

        <form Action="<?php echo getPathClientWebRoot() ?>/API/user/new.php" Method="POST"/>

            <input type="text" name="user" Placeholder="Username"/>
            <input type="password" name="password" Placeholder="Password"/>
            <input type="password" name="password2" Placeholder="Repeat password"/>
            <input type="submit" value="Save"/>

        </form>

    </div>
</div>
