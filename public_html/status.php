<?PHP
	
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
    

    
    
    
header("Content-type: text/json");
`doorControl.py`;//start the daemon no matter what! if its already running, and there is no argument, it will exit..
$output = array();

$con = mysql_connect("localhost", "devicemanager", "managedevice");
if(!$con){
    
    die('Could not connect:'.mysql_error());
    
}elseif(mysql_select_db("pushdevices")){

    if( (isset($_POST['devToken']) && isValidUDID($_POST['devToken']))){
        
        $devToken = $_POST['devToken'];
        
        $authQuery = 'select COUNT(deviceToken) as total from IOSpushDevices,AuthorizedDevices where A_ID=P_ID and deviceToken="'.$devToken.'"';
        $authResult = mysql_query($authQuery);
        $authRows = mysql_fetch_array($authResult);
        $isAuthed = ($authRows['total']==1)?TRUE:FALSE;
        
        if($isAuthed===true){
        if(isset($_POST['c']) && preg_match('/^(t|u|U|l|L|q|Q){1}$/', $_POST['c'])){
            $command = $_POST['c'];
            
            

            
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
        
        

        $result = mysql_query("SELECT * FROM Lock_State");
        $locked = mysql_fetch_array($result);
        $lockedState = (($locked['state']==0)?"false":"true");
        $output["lockstate"] = $lockedState;
        
        
        //if(isset($_POST['devToken']) && isValidUDID($_POST['devToken'])){
        
        //append isowner and isAuthed to output...
        $query = mysql_query('select P_ID,A_ID,isOwner from (select * from IOSpushDevices where appid="net.theroyalwe.doorcontrol" and deviceToken="'.$_POST['devToken'].'") as i LEFT OUTER JOIN AuthorizedDevices as a on a.A_ID=i.P_ID');
        $privs = mysql_fetch_array($query);
        $output['isAuthed'] = (($privs['A_ID'] == $privs['P_ID'])?"true":"false");
        $output['isOwner'] = (($privs['isOwner'] == 1)?"true":"false");
        
        
        }else{
            $output['isAuthed']="false";
        }
        
        echo json_encode($output);
    }

       
       
    


//	if($row['state'] == 0){
//		echo "{\"lockstate\":\"false\"} \n";
//	}else{
//		echo "{\"lockstate\":\"true\"} \n";
//	}

}
mysql_close($con);

    
?>

