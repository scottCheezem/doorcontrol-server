<?PHP
    //session_start();
    require('class.doorLock.php');
    $myDoorLock = new DoorLock;
    
    

    //make it able to take commands...

    if(isset($_GET['c']) && preg_match('/^(u|U|l|L|q|Q){1}$/', $_GET['c']) ){

        $command = $_GET['c'];

        switch($command){
            case ('l'|'L'):
                echo "locking\n";
                $myDoorLock->lock();
                echo $myDoorLock->queryLockState()."\n";
                break;
            case ('u'|'U'):
                echo "unlocking\n";
                $myDoorLock->unLock();
                echo $myDoorLock->queryLockState()."\n";
                break;
            case ('q'|'Q'):
                echo "querying lock state\n";

                echo $myDoorLock->queryLockState()."\n";
                break;
                
        }
    }
    
    
?>

