<?php
    
    
    $con = mysql_connect("localhost", "devicemanager", "managedevice");
    
    
    if(!$con){
        die('Could not connect:'.mysql_error());
    }elseif(mysql_select_db("pushdevices")){
        
        if(isset($_POST['authid'])){
            //check id, othere params to come...start time - end time
            $authId = $_POST['authid'];
            $authQuery = 'insert into AuthorizedDevices (A_ID) VALUES("'.$authId.'")';
            $query = mysql_query($authQuery);
        }else if(isset($_POST['deauthid'])){
            //remove the A_ID from list...
            $deauthId = $_POST['deauthid'];
            $deauthQuery = 'delete from AuthorizedDevices where A_ID="'.$deauthId.'"';
            $query = mysql_query($deauthQuery);
            
            
        }else if(isset($_POST['makeowner'])){
            //remove the A_ID from list...
            $authId = $_POST['makeowner'];
            $ownerQuery = 'update AuthorizedDevices set isOwner = 1 where A_ID="'.$authId.'"';
            $query = mysql_query($ownerQuery);
            
            
        }else if(isset($_POST['takeowner'])){
            //remove the A_ID from list...
            $deauthId = $_POST['takeowner'];
            $deauthQuery = 'update AuthorizedDevices set isOwner = 0 where A_ID="'.$deauthId.'"';
            $query = mysql_query($deauthQuery);
            
            
        }
        
        
    }
    mysql_close($con);
    
    
    ?>