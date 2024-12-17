<?php


$GLOBALS["BIND9_DATABLOCK_VALUES"] = array(
    "allow-transfer",
);

function bind9_zoneconfig_decode($file){
    $config=array(
        "raw" => "",
        "log" => "",
    );
    $handle = fopen($file, "r");
    $currentZone = "";
    $currentBlock = "";
    $readingDataBlock = false;
    if ($handle) {
        while (($x = fgets($handle)) !== false) {
            
            if(str_starts_with($x, "//")){
                continue;
            }

            $config["raw"].=$x;

            if(str_starts_with($x, "zone")){
                $match="";
                preg_match("/\".*\"/", $x, $match);
                $match = str_replace("\"", "", $match);
                $currentZone = $match[0];
                if(!isset($config["zones"])){
                    $config["zones"] = array();
                }
                array_push($config["zones"], $currentZone);
                if(!isset($config[$currentZone])){
                    $config[$currentZone] = array();
                }
                continue;
            }

            
            foreach($GLOBALS["BIND9_DATABLOCK_VALUES"] as $key => $value){
                if(str_contains($x, $value)){
                    // echo "X: $x contiene $value";
                    $readingDataBlock = true;
                    $currentBlock = $x;
                }
            }

            if($readingDataBlock){
                $match="";
                $pattern="/\}(\s|\n|\r|\t|\f)*(;)/";
                preg_match($pattern, $currentBlock,$match);
                if(count($match) == 0){
                    if($currentBlock != $x){
                        $currentBlock .= $x;
                    }
                    
                    preg_match($pattern, $currentBlock,$match);
                    if(count($match) == 0) continue;
                }
                echo "Data block:" . $currentBlock . "\n";
                

                $match="";
                $currentBlock = str_replace("\n", "", $currentBlock);
                preg_match("/\{.*\}/", $currentBlock, $match);
                $value = str_replace("{", "", $match[0]);
                $value = str_replace("}", "", $value);

                preg_match("/^\s*(\w+|-)+/", $currentBlock, $match);
                $key = trim($match[0]);
                
                $values = explode(";", $value);
                // var_dump($value);
                // var_dump($currentBlock);
                foreach($values as $k => $val){
                    $val = trim($val);
                    if(empty($val)){
                        unset($values[$k]);
                    }
                }

                $config[$currentZone][$key] = $values;
                $readingDataBlock = false;
                continue;
            }

            $pattern="/\}(\s|\n|\r|\t|\f)*(;)/";
            $match = "";
            preg_match($pattern, $x, $match);
            if(count($match) > 0){
                continue;
            }

            if(empty(trim($x))) continue;

            $c = explode(" ", $x);
            // var_dump($c);
            foreach($c as $key => &$value){
                $value = trim($value);
                $value = str_replace(";", "", $value);
                $value = str_replace("\"", "", $value);
            }
            
            $val="";
            for($x = 1; $x < count($c); $x++){
                $val .= $c[$x];
            }

            // var_dump($val);
            $config[$currentZone][$c[0]]=$val;

        }

        fclose($handle);
    }
    if(count($config) == 0){
        return null;
    }
    return $config;
}