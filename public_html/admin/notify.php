<?php

$con = mysql_connect("localhost", "devicemanager", "managedevice");
	    

//this is the defulat behaviour, which should be in some kind of notify function...
    
if(!$con){
	die('Could not connect:'.mysql_error());
}elseif(mysql_select_db("pushdevices")){

//make a descision based on post parameters, what we should do -  notify or auth device.
    if(isset($_POST['auth'])){
        //push out an auth token to some device from the table that wants to be authenticated
        
        //don't forget to clean and check the input here...
        $aid = $_POST['auth'];
        sendAuth($aid);
        
    }else if(isset($_POST['deauth'])){
        $did = $_POST['deauth'];
        sendDeAuth($did);
    }else{
        notifyState();
    }
	
    
}
	
mysql_close($con);

    
    
    
    
    
    function sendAuth($id){


        
        $authQuery = 'select deviceToken,StartTime,EndTime from IOSpushDevices,AuthorizedDevices where appid="net.theroyalwe.doorControl" and A_ID=P_ID and P_ID='.$id;
        $query = mysql_query($authQuery);
        $row = mysql_fetch_array($query);
        $deviceToken = $row['deviceToken'];
        
        //remember, this is to auth a device, not invite it! well do that later...
        $message='This device has been authorized.';
        $extra=array("isAuthed"=>"true","starttime"=>$row['StartTime'],"endtime"=>$row['EndTime']);
        

        
        $fp=openConnection();
        apns($deviceToken, $message, $extra, $fp);//my message!!!
        closeConnection($fp);
        
        
        
    }
    
    
    function sendDeAuth($id){
        
        
        
        $authQuery = 'select deviceToken from IOSpushDevices where appid="net.theroyalwe.doorControl" and P_ID='.$id;
        $query = mysql_query($authQuery);
        $row = mysql_fetch_array($query);
        $deviceToken = $row['deviceToken'];
        
        //remember, this is to auth a device, not invite it! well do that later...
        $message='This device is no longer authorized.';
        $extra=array("isAuthed"=>"false");
        
        
        
        $fp=openConnection();
        apns($deviceToken, $message, $extra, $fp);//my message!!!
        closeConnection($fp);
        
        
        
    }
    
    
    function notifyState(){
        
        //lets get the lock state and the last user to update
        
        $lockStatequery = 'select state , user from Lock_State where 1';
        $query = mysql_query($lockStatequery);
        $lockState = "";
        //since we have the "lockation" that should probably be in here too"
        $message = "door was ";
        $row = mysql_fetch_array($query);
        $device = $row['user'];
        if($row['state'] == 0){
            $lockState = "false";
            $message = $message  . 	"unlocked by " .$device;
        }else if($row['state'] == 1){
            $lockState = "true";
            $message = $message  . 	"locked by " .$device;
        }
        
        
        
        $extra = array('lockstate'=>$lockState, 'device'=>$device);
        
        $recipients = 'select deviceToken from IOSpushDevices,AuthorizedDevices where appid="net.theroyalwe.doorcontrol" and P_ID=A_ID';
        $query = mysql_query($recipients);
        
        
        $fp = openConnection();//our secure connection to apple
        echo "connection open\n";
        
        while ($row = mysql_fetch_array($query)){
            echo "pushing to " . $row['deviceToken'] . "\n";
            $deviceToken= $row['deviceToken'];
            //$message="HI!";
            apns($deviceToken, $message, $extra, $fp);//my message!!!
            
        }
        closeConnection($fp);
        
        
        
    }
    
    
function closeConnection($fp){

	// Close the connection to the server
    echo "closing connection\n";
        fclose($fp);

} 
   
function openConnection(){

	// Put your device token here (without spaces):
        
        //
        //$deviceToken = 'a918a43a26a5e42841e14fa5f49e74d2a564ac9091d82bf31d5d042d92d0a000';
        //
        //// Put your private key's passphrase here:
        $passphrase = 'doorcontrol';
        //
        //// Put your alert message here:
        //$message = 'My first push notification!';
        
        ////////////////////////////////////////////////////////////////////////////////

                
                
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', '/home/doorcontrol/public_html/.auth/doorControlCK.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client(
            'ssl://gateway.sandbox.push.apple.com:2195', $err,
            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

    if (!$fp){
            exit("Failed to connect: $err $errstr" . PHP_EOL);
    }else{
        echo 'Connected to APNS' . PHP_EOL;
    }
    return $fp;

}


    function apns($deviceToken, $message, $extra, $fp){

        

        // Create the payload body
        $body['aps'] = array(
            'alert' => $message,
            'sound' => 'default'
            );

	$body['extra'] = $extra;


        // Encode the payload as JSON
        $payload = json_encode($body);
	echo $payload;

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        if (!$result){
            echo 'Message not delivered' . PHP_EOL;
        }else{
            echo 'Message successfully delivered' . PHP_EOL;
        }
        
    }
