<?php

 $con = mysql_connect("localhost", "devicemanager", "managedevice");


        if(!$con){
                die('Could not connect:'.mysql_error());
        }elseif(mysql_select_db("pushdevices")){
		
	}
 
$checked = $_POST['checkbox'];
$count = count($checked);
for($i=0; $i < $count; $i++){
        $query = "DELETE FROM IOSpushDevices WHERE P_ID=".$checked[$i];
        mysql_query($query);
}

$result = mysql_query("SELECT * FROM IOSpushDevices where appid='net.theroyalwe.doorcontrol'");
echo "<form method=POST>"; 
echo "<table border='1'>
 <tr>
 <th>ID</th>
 <th>DeviceName</th>
 <th>Delete?</th>
 </tr>";
 
while($row = mysql_fetch_array($result))
   {
   echo "<tr>";
   echo "<td>" . $row['P_ID'] . "</td>";
   echo "<td>" . $row['devicename'] . "</td>";
   echo "<td>";
   echo "<input name=checkbox[] value=".$row['P_ID']." type=checkbox>";
   echo "</td>";
   echo "</tr>";
   }
 echo "</table>";
 echo "<input type=submit value=Delete />";
echo "</form>";

mysql_close($con);
 ?> 
