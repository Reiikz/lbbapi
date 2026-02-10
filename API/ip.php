<?php

if(isset($_GET["t"])){
    switch($_GET["t"]){
        case "plain":
            echo getClientIP();
            break;
        case "json":
            echo json_encode(array("ip" => getClientIP()));
            break;
        default:
            header('HTTP/1.0 400 Bad request');
            echo "Unsupported IP mode";
            break;
    }
}else{
    echo getClientIP();
}