<?php

function bind9_shortStatus(){
    
    $output = shell_exec("sudo systemctl status bind9");

    $matches = "";

    preg_match("/\s+Process:(.)*/", $output, $matches);
    $process = $matches;
    if(count($matches) > 0){
        preg_match("/FAILURE/", $process[0], $matches);
        if(count($matches) > 0){
            return "FAILED";
        }
        preg_match("/SUCCESS/", $process[0], $matches);
        if(count($matches) > 0){
            return "OK";
        }
    }
    
    preg_match("/\s+Active: active \(running\)(.)*/", $output, $matches);
    if(count($matches) > 0){
        return "OK";
    }

    return "UNKNOWN";
}

