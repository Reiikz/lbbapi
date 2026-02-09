<?php

function validateRecordTypeQuit($type, $value){
    switch($type){
        case "A":
            $ip = $value;
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                header("HTTP/1.1 400 Bad request");
                echo "<h1>$value is not a valid IPv4 record!</h1>";
                exit(0);
            }
            if(str_contains($ip, ":")){
                header("HTTP/1.1 400 Bad request");
                echo "<h1>$value is not a valid IPv4 record!</h1>";
                exit(0);
            }
            break;
        case "AAAA":
            $ip = $value;
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                header("HTTP/1.1 400 Bad request");
                echo "<h1>$value is not a valid IPv6 record!</h1>";
                exit(0);
            }
            if(preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)){
                header("HTTP/1.1 400 Bad request");
                echo "<h1>$value is not a valid IPv6 record!</h1>";
                exit(0);
            }
            break;
    }
}