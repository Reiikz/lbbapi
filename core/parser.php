<?php


$BIND9_CONFIG_SYMBOLS = array(
    "zone",
    "type",
    "file",
);

function parseconfig($file){
    $config=array(
        "raw" => "",
    );
    $handle = fopen($file, "r");
    $expecting = "";
    $reading = "";
    $read = false;
    $collected = "";
    $key = "";
    $scope = "";
    if ($handle) {
        while (($x = fgetc($handle)) !== false) {
            $config["raw"].=$x;
            
            $collected .= $x;
            
            switch(trim ($collected)){
                case "//":
                    $reading="comment";
                    $expecting="lineJump";
                    break;

                case "zone":
                    $expecting="OpenQuote";
                    $reading="zoneName";
                    $read = false;
                    $collected="";
                    break;
            }

            switch($expecting){
                case "lineJump":
                    if($x == "\n"){
                        $reading="";
                        $expecting="";
                    }
                    $collected="";
                    break;
                
                case "OpenQuote":
                    if($collected == "\""){
                        $expecting="CloseQuote";
                    }
                    $collected="";
                    break;

                case "CloseQuote":
                    if($x == "\""){
                        $collected = substr($collected, 0, strlen($collected) - 2);
                    }
                    $read = true;
                    break;
                
                case "OpenBracket":
                    if($collected == "{"){
                        $expecting="CloseBracket";
                    }
                    $collected="";
                    break;
                
                // case "CloseBracket": {
                //     if($)
                // }
            }

            if($read){
                switch($reading){
                    case "zoneName":
                        if(!isset($config["zones"])){
                            $config["zones"] = array();
                        }
                        array_push($config["zones"], $collected);
                        $scope = $collected;
                        $collected = "";
                        $reading = "ZoneConfig";
                        $expecting = "OpenBracket";
                        $read = false;
                        $expecting = "";
                        break;
                }
            }
        }
        fclose($handle);
    }
    if(count($config) == 0){
        return null;
    }
    return $config;
}