<?PHP
    //session_start();
    //require('class.doorLock.php');
    //$myDoorLock = new DoorLock;
    
    

    //make it able to take commands...

    //another condition to check is isAuthorizedToControlDoor...
    //so we need to pass in at least the deviceID
    //or a 

header("Content-type: text/json");
`doorControl.py 2>/dev/null`;//start the daemon no matter what! if its already running, and there is no argument, it will exit..
    
    
$con = mysql_connect("localhost", "devicemanager", "managedevice");
if(!$con){
    
    die('Could not connect:'.mysql_error());
    
}elseif(mysql_select_db("pushdevices")){

    if(isset($_POST['c']) && preg_match('/^(t|u|U|l|L|q|Q){1}$/', $_POST['c']) && isset($_POST['devToken'])){

            $command = $_POST['c'];
            $devToken = $_POST['devToken'];

	//using devToken for now...
	//this should be the authtoken...we'll do some processing on it here to authenticate it...        
        
            switch($command){
                case ('l'|'L'):
            #echo "LOCKING";
                    `doorControl.py L $devToken`;
                break;
                case ('u'|'U'):
            #echo "UNLOCKING";
                    `doorControl.py U $devToken`;
                break;
        }
        sleep(1);
            
    }



//
//
//$con = mysql_connect("localhost", "devicemanager", "managedevice");
//if(!$con){
//
//	die('Could not connect:'.mysql_error());
//
//}elseif(mysql_select_db("pushdevices")){

	$result = mysql_query("SELECT * FROM Lock_State");
	$row = mysql_fetch_array($result);
	#echo $row['state'];

	if($row['state'] == 0){
		echo "{\"lockstate\":\"false\"} \n";
	}else{
		echo "{\"lockstate\":\"true\"} \n";
	}

}
mysql_close($con);

    
?>

