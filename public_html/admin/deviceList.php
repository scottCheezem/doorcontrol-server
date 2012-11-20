<?php

    header("Content-type: text/json");
    
    
    $con = mysql_connect("localhost", "devicemanager", "managedevice");
    
    
    if(!$con){
        die('Could not connect:'.mysql_error());
    }elseif(mysql_select_db("pushdevices")){
        $result = mysql_query("SELECT * FROM IOSpushDevices where appid='net.theroyalwe.doorcontrol'");

        $devices=array();
        
        
        while($row = mysql_fetch_array($result)){
            $device=array("pid" => $row['P_ID'],
                          "devicetoken" => $row['devicetoken'],
                          "devicename"=> $row['devicename'],
                          "registertime"=>$row['registertime'],
                          "devicetype"=>$row['devicetype']);
            array_push($devices, $device);
        }
        echo '{"devices":'.json_encode($devices).'}';
        
	}
    
    
    
    mysql_close($con);
    
    
    
?>