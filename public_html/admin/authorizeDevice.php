<?php
    
    
	function postNotify($id){
		$url = 'http://doorcontrol.theroyalwe.net/admin/notify.php';
		
		$postvars='';
		$sep='';
		
		foreach($id as $key=>$value){
			$postvars.= $sep.urlencode($key).'='.urlencode($value);
			$sep='&';
			
		}
		
		
		//open connection
		$ch = curl_init();
		
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,count($id));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
		
		//execute post
		$result = curl_exec($ch);
		
		//close connection
		curl_close($ch);
		
		
	}
	
	
	
	
    $con = mysql_connect("localhost", "devicemanager", "managedevice");
    
    
    if(!$con){
        die('Could not connect:'.mysql_error());
    }elseif(mysql_select_db("pushdevices")){
        
        if(isset($_POST['authid'])){
            //check id, othere params to come...start time - end time
            $authId = $_POST['authid'];
            $authQuery = 'insert into AuthorizedDevices (A_ID) VALUES("'.$authId.'")';
            $query = mysql_query($authQuery);
			$postArr = array("auth"=>$authId);
			postNotify($postArr);
                        
            
        }else if(isset($_POST['deauthid'])){
            //remove the A_ID from list...
            $deauthId = $_POST['deauthid'];
            $deauthQuery = 'delete from AuthorizedDevices where A_ID="'.$deauthId.'"';
            $query = mysql_query($deauthQuery);
			
			$postArr = array("deauth"=>$deauthId);
			postNotify($postArr);
            
            
        }else if(isset($_POST['makeowner'])){
            //remove the A_ID from list...
            $authId = $_POST['makeowner'];
            $ownerQuery = 'update AuthorizedDevices set isOwner = 1 where A_ID="'.$authId.'"';
            $query = mysql_query($ownerQuery);
			
			$postArr = array("owner"=>$authId);
			postNotify($postArr);
            
            
        }else if(isset($_POST['takeowner'])){
            //remove the A_ID from list...
            $deauthId = $_POST['takeowner'];
            $deauthQuery = 'update AuthorizedDevices set isOwner = 0 where A_ID="'.$deauthId.'"';
            $query = mysql_query($deauthQuery);
            
            $postArr = array("unown"=>$deauthId);
			postNotify($postArr);
        }
        
        
    }
    mysql_close($con);
    
    
    ?>