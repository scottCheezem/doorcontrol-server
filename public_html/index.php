<?PHP
    //session_start();
    require('class.doorLock.php');
    $myDoorLock = new DoorLock;
    
    

    //make it able to take commands...

    //another condition to check is isAuthorizedToControlDoor...
    //so we need to pass in at least the deviceID
    //or a 
    
    if(isset($_GET['c']) && preg_match('/^(t|u|U|l|L|q|Q){1}$/', $_GET['c']) ){

        $command = $_GET['c'];
	$con = mysql_connect("localhost", "devicemanager", "managedevice");

        if(!$con){
                die('Could not connect:'.mysql_error());
        }elseif(mysql_select_db("pushdevices")){
                //add the deviceid to the table
                //$idcheck = 'select COUNT(*) from IOSpushDevices where devicetoken = "'.$devid.'" and appid = "'.$appid.'"';
        switch($command){
            case ('l'|'L'):
                echo "locking\n";
                $myDoorLock->lock();
                mysql_query("CALL ToggleLock('Main',1)");
//                echo $myDoorLock->queryLockState()."\n";
                `echo L > /dev/ttyUSB1`;
            break;
            case ('u'|'U'):
                echo "unlocking\n";
                $myDoorLock->unLock();
                mysql_query("CALL ToggleLock('Main',0)");
//                echo $myDoorLock->queryLockState()."\n";
                `echo U > /dev/ttyUSB1`;
            break;
            case ('q'|'Q'):
                echo "querying lock state\n";
                $result = mysql_query("SELECT * FROM Lock_State");
                //echo $myDoorLock->queryLockState()."\n";
                $row = mysql_fetch_array($result);
                echo $row['state'];
                echo "<br />";
                if($row['state'] == 0){
                    echo "{lockstate:false} \n";
                }else{
                    echo "{lockstate:true} \n";
                }
//                `echo ? > /dev/ttyUSB1`;
                break;
//            case('t'):
//                `echo . > /dev/ttyUSB1`;
//            break;
        }
	}   
	mysql_close($con); 
}
    
    
?>

