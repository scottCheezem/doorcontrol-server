<?php

    header("Content-type: text/json");
    
    
    $con = mysql_connect("localhost", "devicemanager", "managedevice");
    
    
    if(!$con){
        die('Could not connect:'.mysql_error());
    }elseif(mysql_select_db("pushdevices")){
        //select * from (select * from IOSpushDevices where appid='net.theroyalwe.doorcontrol') as i LEFT OUTER JOIN AuthorizedDevices as a on a.A_ID=i.P_ID
//        $result = mysql_query("SELECT * FROM IOSpushDevices where appid='net.theroyalwe.doorcontrol'");
        $result = mysql_query("select * from (select * from IOSpushDevices where appid='net.theroyalwe.doorcontrol') as i LEFT OUTER JOIN AuthorizedDevices as a on a.A_ID=i.P_ID");
        $devices=array();
        
        
        while($row = mysql_fetch_array($result)){
            $device=array("pid" => $row['P_ID'],
                          "aid" =>$row['A_ID'],
                          "isOwner"=>(($row['isOwner']==0)?false:true),
                          "startTime" => $row['StartTime'],
                          "endTime" => $row['EndTime'],
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