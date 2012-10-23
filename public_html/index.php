<?PHP
    //session_start();
    //require('class.doorLock.php');
    //$myDoorLock = new DoorLock;
    
    

    //make it able to take commands...

    //another condition to check is isAuthorizedToControlDoor...
    //so we need to pass in at least the deviceID
    //or a 

header("Content-type: text/json");
    if(isset($_GET['c']) && preg_match('/^(t|u|U|l|L|q|Q){1}$/', $_GET['c']) ){

        $command = $_GET['c'];

        switch($command){
            case ('l'|'L'):
		echo "LOCKING";
                `doorControl.py L`;
            break;
            case ('u'|'U'):
		echo "UNLOCKING";
                `doorControl.py U`;
            break;
	}
        
}





$con = mysql_connect("localhost", "devicemanager", "managedevice");    
if(!$con){

	die('Could not connect:'.mysql_error());

}elseif(mysql_select_db("pushdevices")){

	$result = mysql_query("SELECT * FROM Lock_State");
	$row = mysql_fetch_array($result);
	#echo $row['state'];

	if($row['state'] == 0){
		echo "{lockstate:false} \n";
	}else{
		echo "{lockstate:true} \n";
	}

}
mysql_close($con);

    
?>

