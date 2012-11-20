<?php
    
    
    $con = mysql_connect("localhost", "devicemanager", "managedevice");
    
    
    if(!$con){
        die('Could not connect:'.mysql_error());
    }elseif(mysql_select_db("pushdevices")){
        
        if(isset($_POST['id'])){
            
        }
        
        
    }
    mysql_close($con);
    
    
    ?>