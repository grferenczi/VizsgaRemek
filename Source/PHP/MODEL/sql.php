<?php
abstract class Sql{
    static $con;
    static function Connect(){
        try{
            global $cfg;
            //print_r($cfg);
            self::$con = new mysqli($cfg["DBDomain"],$cfg["DBUser"],$cfg["DBPass"],$cfg["DBName"]);
        }
        catch(Exception $e){
            throw $e;
            return false;
        }
        return true;
    }
    static function Disconnect(){
        self::$con->close();
    }
    private static function WhereBuilder($where,bool $equal=true,bool $and=true):string{
        if(!is_null($where))
        {
            $q= " WHERE ";
            if(is_array($where)){
                $isAssoc = array_values($where)!=$where;
                $i=0;
                if($isAssoc){
                    foreach($where as $key=>$value)
                    {
                        $q.="`".$key."`";
                        $q.=$equal?"=":"!=";
                        $q.="'".$value."'";
                        if($i<count($where)-1){
                            $q.=$and?" AND ":" OR ";
                        }
                        $i++;
                    }
                }
                else{
                    foreach($where as $paramblock)
                    {
                        if(count($paramblock)!=3)continue;
                        $key = $paramblock[0];
                        $value = $paramblock[1];
                        $operator = $paramblock[2];
                        $q.="`".$key."`";
                        $q.=" ".$operator." ";
                        $q.="'".$value."'";
                        if($i<count($where)-1){
                            $q.=$and?" AND ":" OR ";
                        }
                        $i++;
                    }
                }
            }
            else{$q.=$where;}
            return $q;
        }else{return "";}
    }
    static function Querry(string $querry, bool $fetch=false){
        $result = false;
        if(!self::Connect())return null;
        try{
            $res = self::$con->query($querry);
            if($fetch){
                $result = $res->fetch_all(MYSQLI_ASSOC);
            }
            else{$result = true;}
        }
        catch(Exception $e){throw $e;}
        finally{
            self::Disconnect();
            return $result;
        }
    }
    static function Select(string $table, $where=null,$orderby=null,bool $asc=null ,int $limit = null):string{
        $arr=null;
        $q="SELECT * FROM `".$table."`";
        if(!is_null($where))
        {
            $q.= self::WhereBuilder($where);
        }
        if(!is_null($orderby))
        {           
           
            if(is_array($orderby)){
                $trimmed="";
                for($i=0;$i<count($orderby);$i++)
                {
                    $trimmed.=self::UnTrim(trim($orderby[$i],"`"),"`");
                    if($i<count($orderby)-1){ $trimmed.=",";}
                }
                
            }
            else{
                $trimmed=self::UnTrim(trim($orderby,"`"),"`");
                
            }
            $q.= " ORDER BY ".$trimmed;
            //rtrim($q,',');
            if(!is_null($asc))
            {
                $q.=$asc?" ASC":" DESC";
            }
        }
        if(!is_null($limit))
        {
            $q.= " LIMIT ".$limit;
        }
        $q.=";";
        return $q;
    }
    private static function UnTrim($string,string $prepost){
        if(is_array($string)){
            $arr=null;
            foreach($string as $val)
            {  if(!is_array($val))
                {
                    $arr[]=self::UnTrim($val,$prepost);
                } 
            }
            return $arr;
        }
        elseif(is_numeric($string))return $string;
        else{return $string!=null?$prepost.$string.$prepost:"NULL";}
    }
    static function Insert(string $table, array $array):string{
        $q= "INSERT INTO `".$table."` ";
        if($array!=array_values($array)) // Check array type
        {
            $keys = array_keys($array);
            $keys = implode(", ",self::UnTrim($keys,"`"));
            $q.="(".$keys.") ";
        }
        $vals = array_values($array);
        $vals = implode(", ",self::UnTrim($vals,"'"));  
        $q.="VALUES (".$vals.");"; 
        return $q;
    }
    static function Update(string $table,array $assocArray,$where):string
    {
        $q="UPDATE `".$table."` SET " ;
        $sets=null;
        foreach($assocArray as $key=>$value){
            $sets[]=self::UnTrim($key,"`")."=".($value!==null?self::UnTrim($value,"'"):"NULL");
        }
        $q.=implode(", ",$sets).self::WhereBuilder($where).";";
        return $q;
    }
    static function Delete(string $table,$where):string
    {
        $q="DELETE FROM ".self::UnTrim($table,"`").self::WhereBuilder($where).";";
        return $q;
    }
}