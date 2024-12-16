<?php


$BIND9_CONFIG_SYMBOLS = array(
    "zone",
    "type",
    "file",
);

function parseconfig($file){
    $config=array();
    $handle = fopen($file, "r");
    $expecting = "";
    $collected = "";
    $key = "";
    if ($handle) {
        while (($x = fgetc($handle)) !== false) {
            
            $collected .= $x;

            if(empty($expecting) && empty($key)){
                
            }
            
            switch($collected){
                case "zone":
                    $expecting="ZoneName";
                    break;

            }
        }
        fclose($handle);
    }
    if(count($config) == 0){
        return null;
    }
    return $config;
}