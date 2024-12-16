<?php

$GLOBALS["webroot"]=dirname(__FILE__);

include_once $GLOBALS["webroot"] . "/core/core.php";
include_once $GLOBALS["webroot"] . "/API/auth/redirectIfNotLoggedIn.php";

header("Location: " .  getPathClientWebRoot() . "/cpanel");

