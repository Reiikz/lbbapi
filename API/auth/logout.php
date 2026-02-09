<?php

include_once APP_ROOT . "/core/core.php";

session_start();
session_destroy();
header("Location: " . getPathClientWebRoot());
