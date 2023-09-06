<?php
class User{
    public $id;
    public $name;
    public $role;
    public $skills;

    function __construct($array)
    {
        $this->role=UserRole::from($array["role"]);
        $this->id=$array["id"];
        $this->name=$array["name"];
        if(is_null($array["skills"])){$this->skills=null;}
        else if(is_array($array["skills"])){$this->skills=$array["skills"];}
        else {$this->skills=explode("|",$array["skills"]);}
        $this->id=$array["id"];
    }

    function isAdmin():bool{
        return $this->role==UserRole::Admin;
    }
    function isModeller():bool{
        return $this->role==UserRole::Modeller;
    }
   
    static function Login($id,$inSession=false){
        $user = self::GetUserData($id);
        if($inSession){
          if($user == null){unset($_SESSION["user"]);}
          else{
            $_SESSION["user"]= $user;
          }
        }
        return $user;
     }
     static function Logout(){
        $_SESSION["user"]=null;
        unset($_SESSION["user"]);
        
     }
     static function GetUserData($id){
        $raw = SQL::Querry(SQL::Select("users",["id"=>$id]),true);
        $user=null;
        if($raw)
        {
            $user=$raw[0];
            $user["skills"]=explode("|",$user["skills"]);
        }
        return $user;
    }
}



enum UserRole:int{
    case Operator=0;
    case Modeller=1;
    case Admin=2;
}