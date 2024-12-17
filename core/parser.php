<?php


$GLOBALS["BIND9_DATABLOCK_VALUES"] = array(
    "allow-transfer",
);

function bind9_zoneconfig_decode($file){
    // $config=array(
    //     "raw" => "",
    // );
    $handle = fopen($file, "r");
    $currentZone = "";
    $currentBlock = "";
    $readingDataBlock = false;
    if ($handle) {
        while (($x = fgets($handle)) !== false) {
            
            if(str_starts_with($x, "//")){
                continue;
            }

            // $config["raw"].=$x;

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
                // echo "Data block:" . $currentBlock . "\n";
                

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
                if(empty($value))
                    unset($c[$key]);
            }
            $c = array_values($c);
            // var_dump($c);
            $val="";
            for($x = 1; $x < count($c); $x++){
                $val .= $c[$x];
            }

            // var_dump($c[0]);
            $config[$currentZone][$c[0]]=$val;

        }

        fclose($handle);
    }
    if(count($config) == 0){
        return null;
    }
    return $config;
}

function bind9_zonedb_decode($file){
    $database = array();
    $handle = fopen($file, "r");
    $db_params_gathered = 0;
    $db_params_gathering = false;
    $buffer = "";
    if ($handle) {
        while (($x = fgets($handle)) !== false) {
            $y = $x;
            // echo $x;
            
            if(str_starts_with($y, ";")){
                continue;
            }

            if(str_contains($x, ";")){
                $y = preg_replace("/\;.*$/", "", $x);
            }

            // echo "trimmed: " . trim($y) . "\n";
            // echo "empty: " . print_r(empty(trim($y))) . "\n";

            if((empty(trim($y))) && (trim($y) != "0")) continue;
            
            if(str_starts_with($y, "\$TTL")){
                $value = preg_replace("/[^0-9]/", "", $y);
                // print_r($d);
                $database["DEFAULT_TTL"] = $value;
                continue;
            }

            if(preg_match("/\@\s+IN\s+SOA\s+/", $y) || $db_params_gathering){
                $db_params_gathering = true;
                if(!preg_match("/\((.*)\)/", $buffer)){
                    $buffer .= "$y";
                    $buffer = str_replace("\n", "", $buffer);
                    // echo $buffer . "\n";
                    if(!preg_match("/\((.*)\)/", $buffer))
                        continue;
                }
                // echo $buffer;

                $s = preg_split("/\s+/", $buffer);
                $database["SOA"] = $s[3];
                $z = 4;
                while($s[$z] != "("){
                    $database["SOA_SERVERS"]=$s[$z];
                    $z++;
                    if($z >= count($s)){
                        break;
                    }
                }

                $database["SERIAL"]=$s[6];
                $database["REFRESH"]=$s[7];
                $database["RETRY"]=$s[8];
                $database["EXPIRE"]=$s[9];
                $database["NEGATIVE_CACHE_TTL"]=$s[10];

                // print_r($s);

                $db_params_gathering = false;
                continue;
            }

            if(!isset($database["recordset"])){
                $database["recordset"] = array();
            }

            $record = preg_split("/\s+/", $y);

            //offets according to record setup
            $rpos = 0;
            $ttlpos = 1;
            $inpos = 2;
            $typepos = 3;
            $resolutionpos = 4;
            if(count($record) < 6){
                $rpos = 0;
                $ttlpos = -1;
                $inpos = 1;
                $typepos = 2;
                $resolutionpos = 3;
            }

            if(!isset($database["recordset"][$record[$rpos]])){
                $database["recordset"][$record[$rpos]] = array();
            }

            if(!isset($database["recordset"][$record[$rpos]]["types"])){
                $database["recordset"][$record[$rpos]]["types"] = array();
            }

            if(!in_array($record[$typepos], $database["recordset"][$record[$rpos]]["types"])){
                array_push($database["recordset"][$record[$rpos]]["types"], $record[$typepos]);
            }

            if(!isset($database["recordset"][$record[$rpos]][$record[$typepos]])){
                $database["recordset"][$record[$rpos]][$record[$typepos]] = array();
            }
            $ttl = "";
            if($ttlpos == -1){
                $ttl = $database["DEFAULT_TTL"];
            }else{
                $ttl = $record[$ttlpos];
            }
            array_push($database["recordset"][$record[$rpos]][$record[$typepos]], array(
                "value" => $record[$resolutionpos],
                "ttl" => $ttl,
            ));

            // echo $y;
        }
        fclose($handle);
        if(count($database) > 0){
            return $database;
        }
    }
    return null;
}