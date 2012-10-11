<?PHP


class DoorLock
{
    private static $instance;

    //0 = unlocked, 1=locked
    //need some way to restore this state????
    private $lockState = True;
    
    public function __construct(){
	        

    }
    

    //will I actually use self?
//    public static function getInstance(){
//        if(!self::$instance){
//            self::$instance = new self();
//        }
//        
//        return self::$instance;
//    }
    
    
    public function unLock(){
        //make call to script
        $this->lockState=False;
    }
    
    public function lock(){
        //make call to script
        $this->lockState=True;
    }
    
    public function queryLockState(){
        
        return $this->lockState;
    }
    
    public static function learn(){
        //make call to script...
    }
    
    
}
    

?>
