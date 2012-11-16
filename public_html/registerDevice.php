<?php

//we are expceting a post with the device token and appID

//check this, make sure it won't lead to an intrusion
function isValidUDID($udid)
{
    if (strlen($udid) != 64 ){  // 32 for simulator
        
        echo "bad udid" . strlen($udid) ."\n";
        return false;
    }
    if (preg_match("/^[0-9a-fA-F]+$/", $udid) == 0)
        return false;
    
    return true;
}
    
    
    
if(isset($_POST['appid']) && isset($_POST['devid']) && isValidUDID($_POST['devid'])){

	$devid = $_POST['devid'];
	$appid = $_POST['appid'];
	$devicename = addslashes($_POST['devname']);//this needs to be scrubbed!!!
	$devicetype = $_POST['devtype'];

	$con = mysql_connect("localhost", "devicemanager", "managedevice");


	if(!$con){
		die('Could not connect:'.mysql_error());
	}elseif(mysql_select_db("pushdevices")){
		//add the deviceid to the table
       	 	$idcheck = 'select COUNT(*) from IOSpushDevices where devicetoken = "'.$devid.'" and appid = "'.$appid.'"';
        	$query = mysql_query($idcheck);
        	$numRows = mysql_num_rows($query, $con);
        	echo "ran ". $idcheck  . " got ".$numRows ."\n";
		if($numRows == 0 ){
            		//this particular combination is not in the db...
            		echo "inserting...\n";
            		$insertNewDeviceId = 'insert into IOSpushDevices (devicetoken, appid, devicename, devicetype) VALUES("'.$devid.'", "'.$appid.'", "'.$devicename.'", "'.$devicetype.'")';
            		echo $insertNewDeviceId."\n";
            		$query = mysql_query($insertNewDeviceId, $con);
                }
	}
    mysql_close($con);

}else{

    echo "there was a problem with the deviceID, len:".strlen($_POST['devid']);

}


?>
