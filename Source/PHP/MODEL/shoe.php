<?php

class Shoe{
    public $orderId;
    public $title;    
    public $location;
    public $carring;
    public $status;
    public $orderDate;
    public $deadlineDate;
    public $finishDate;
    public $processes;
    public $note;
    public $avalibleprocesses;
    public $files;

    static function Create($id,$title,$location,$order,$deadline,$file){
        $tmp = [
            "order_id" => $id,
            "title" => $title,
            "location" => $location,
            "order_date" => $order,
            "deadline_date" => $deadline
        ];
		
       if(Sql::Querry(Sql::Insert("orders",$tmp))){
        global $cfg;
        $dir=$cfg["DATAPATH"]."Aktív/".$id."/";
        mkdir($dir);
        rename($file,$dir.$id.".zip");
            $zip = new ZipArchive;
            $zipFile = $dir.$id.".zip";
            $extractTo = $dir;

            if ($zip->open($zipFile) === true) {
                $zip->extractTo($extractTo);
                $zip->close();
                unlink($zipFile);
                self::FileListUpdate($id);
            }             
           return true;
        }
    }
    static function FileListUpdate($id){
        global $cfg;
        $aktivdir=$cfg["DATAPATH"]."Aktív/".$id."/";
        $archivdir=$cfg["DATAPATH"]."Archív/".$id."/";
        $dir="";
        if(file_exists($aktivdir)){$dir = $aktivdir;}
        else if(file_exists($archivdir)){$dir = $archivdir;}
        else{return;}
        $files = Utils::GetFilesFromURL($dir,$id)??[];
        self::UpdateFromArray(["files"=>json_encode($files)],$id);
    } 
    static function UpdateFromArray(array $arr,$id){
        if(isset($arr["status"])){
            if($arr["status"]>2){
                $arr["location"]=null;
                $arr["close_date"]=(new DateTime("now"))->format('Y-m-d H:i');
            }
            
        }
        $where["order_id"] = $id;
        if(Sql::Querry(Sql::Update("orders",$arr,$where))){
            global $cfg;
            $dir=$cfg["DATAPATH"]."Aktív/".$where["order_id"]."/";
            $dir2=$cfg["DATAPATH"]."Archív/".$where["order_id"]."/";
            if(isset($arr["status"])&&$arr["status"]>2&&file_exists($dir))rename($dir, $dir2);
            elseif(isset($arr["status"])&&$arr["status"]<=2&&!file_exists($dir))rename($dir2, $dir);
            
        }
        
     }
     static function GetAllHasLocation(){
        return self::GetOrders(SQL::Select("orders","`location` is not null"),false);       
     }
     
     static public function GetOrder($id,$withProcess=true)
     {
        return self::GetOrders(SQL::Select("orders",["order_id"=>$id]),$withProcess)[0];
     }
     static function GetOrdersByLike($id,$withProcess=true){        
        return self::GetOrders(SQL::Select("orders",[["order_id","%".$id."%","LIKE"]]),$withProcess);
     }
     static function GetOrders($querry,$withProcess=true){
        
            $db_ord = SQL::Querry($querry,true);
            $db_stat = SQL::Querry(SQL::Select("status"),true);
            $allShoes = array();
            if($db_ord){
            foreach($db_ord as $order){
                $allShoes[] = new Shoe($order,self::ProcessFill($db_stat,$order));
            }}
            return $allShoes;
     }
     static function GetAvalibleOrders(){
        return self::GetOrders(SQL::Select("orders", "`status` BETWEEN 1 AND 2",["deadline_date","order_date"]));
     }
     static function ProcessFill($processDB,$shoe){
        $tmp = array();
        foreach($processDB as $status){
            if($shoe["order_id"]==$status["order_id"]){$tmp[]=$status;}
        }
        return $tmp;
    }
    
     static function GetOrdersWithAvalibleProcessForUser($user = false){
        $shoes = self::GetAvalibleOrders();
        $ok=array();
        if($user) $USER = $user;
        else global $USER;
        if($USER ==null) return;
        foreach($shoes as $shoe){
            foreach($USER->skills as $skill){
                if(isset($shoe->processes[$skill])&&$shoe->processes[$skill]->avalible&&!$shoe->processes[$skill]->done){
                    $newshoe=clone $shoe;
                    $newshoe->processes=array();
                    $newshoe->processes[$skill] = $shoe->processes[$skill];
                    $ok[]=$newshoe;
                }
            }
        }
        return $ok;
     }
     static function GetOrdersWithAvalibleProcessForUserExtend($user = false){
        $shoes = self::GetAvalibleOrders();
        $ok=array();
        if($user) $USER = $user;
        else global $USER;
        if($USER ==null) return;
        foreach($shoes as $shoe){
            foreach($USER->skills as $skill){
                if(isset($shoe->processes[$skill])&&$shoe->processes[$skill]->avalible&&!$shoe->processes[$skill]->done){
                    
                    $ok[]=$shoe;
                }
            }
        }
        return $ok;
     }
     static function GetNonreadyOrders(){
        return self::GetOrders(SQL::Select("orders", "`status` = 0","deadline_date"));
     }
     static function GetArchiveOrders(){
        return self::GetOrders(SQL::Select("orders", "`status` BETWEEN 3 AND 4","deadline_date",false,100));
     }
     static function GetShoesByWeek(int $week,int $year = 0, array $shoes = null){
        $shoesList=[];
        if($shoes){
            foreach($shoes as $shoe){
                $dt = date_create_from_format('Y-m-d H:i:s', $shoe->deadlineDate);
                if($dt->format('W') == $week && (!$year || $year==$dt->format('Y'))){
                    $shoesList[]=$shoe;
                }
              }
        }
        else{
           $temp= Utils::getStartAndEndDate($week,$year);
           $shoesList=self::GetOrders(SQL::Select("orders", "`deadline_date` BETWEEN ".$temp[0]->format('Y-m-d')." 0:0:0"." AND ".$temp[1]->format('Y-m-d')." 23:59:59","deadline_date",false,100));
        }
        return $shoesList;
     }

    function __construct(array $array,array $processes=null)
    {
        $this->avalibleprocesses = [];
        $this->orderId = $array["order_id"];
        $this->title = $array["title"];      
        $this->location = $array["location"];
        $this->carring = $array["carring"]?new User(User::GetUserData($array["carring"])):false;
        $this->status = Shoe_Status::from($array["status"]);
        $this->orderDate = $array["order_date"];
        $this->deadlineDate = $array["deadline_date"];
        $this->finishDate = $array["close_date"];
        $this->note = $array["note"];
        $this->files = json_decode($array["files"],true)??[];
        if($processes){                
            foreach($processes as $proc){                    
                $this->processes[$proc["name"]]=new Process($proc);               
            }
            foreach($this->processes as $proc){
                if(is_null($proc->req)){$proc->avalible = true;}
                else{
                   $reqs = explode("|",$proc->req);              
                    if(count($reqs)>1){
                        $aval = true;
                        foreach($reqs as $req){                    
                            if (!isset($this->processes[$req])||!$this->processes[$req]->done){
                                $aval = false;
                                break;
                            }              
                        }
                        $proc->avalible = $aval;
                    }
                    else{
                        $proc->avalible = isset($this->processes[$proc->req])&&$this->processes[$proc->req]->done;
                    }
                }                    
                if($proc->avalible&&!$proc->done)$this->avalibleprocesses[]=$proc->name;
            }
        }
        else{$processes=null;}
    }
    

    function InLocation():bool{
        return is_null($this->carring);
    }
   
}
enum Shoe_Status:int{
    case WaitFor=0;
    case InProgress=1;
    case OnHold=2;
    case Complete=3;
    case Canceled=4;
}