<?php
class Process {
    public $order_id;
    public $name;
    public $done;
    public $date;
    public $user;
    public $line;
    public $step;
    public $stepSpan;
    public $req; //Required 
    public $avalible;  

    function __construct($arr, $aval = false) {
        $this -> order_id = $arr["order_id"];
        $this -> name = $arr["name"];
        $this -> line = $arr["line"];
        $this -> step = $arr["step"];
        $this -> user = $arr["user"];
        $this -> date = $arr["date"];
        $this -> done = $arr["done"];
        $this -> req = $arr["req"];
        $this -> stepSpan = $arr["step_span"];
        $this -> avalible = $aval;
    }
    static function Create($params){
       SQL::Querry(SQL::Insert("status",$params));
    }   
    static function Update($params,$where){
    
        SQL::Querry(SQL::Update("status",$params,$where));

    }   
    static function Delete($where){
        SQL::Querry(SQL::Delete("status",$where));
    } 
    static function ProcessList($shoes){
        $processes=array();
        if($shoes==null)return null;
        foreach($shoes as $shoe){
            foreach($shoe->processes as $process){
                if(!in_array($process,$processes))$processes[]=$process;
            }
        }
        return $processes;
    }
}