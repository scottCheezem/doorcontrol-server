<?php


$con = mysql_connect("localhost", "devicemanager", "managedevice");


if(!$con){
    die('Could not connect:'.mysql_error());
}elseif(mysql_select_db("pushdevices")){
    
    if(isset($_POST['id'])){
        $deleteId = $_POST['id'];
        $query = "DELETE FROM IOSpushDevice WHERE P_ID=".$deleteId;
        mysql_query($query);
        echo 'Deleteing '.$deleteId;
    }
    
    
}
mysql_close($con);


?>