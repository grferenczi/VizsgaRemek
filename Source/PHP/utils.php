<?php
abstract class Utils{
    static function GetURLParams(){
        $params = [];
        foreach(explode("&",$_SERVER["QUERY_STRING"]) as $t){
            $kv = explode("=",$t);
            if(count($kv)==2)$params[$kv[0]] = urldecode($kv[1]);
        }
        return $params;
    }
    static function GetBodyParams(){
    $params = [];
    $params=json_decode(file_get_contents('php://input'),true);
    return $params;
    }
    

    static function GetFilesFromURL($url,$id){        
        $tmp = [];      
        foreach (glob($url."*") as $path)
        {
            $filename = pathinfo($path,PATHINFO_BASENAME);
            $mime = mime_content_type($path);
            if($filename==$id.".pdf" || strpos($filename,"VRML"))continue;
            if(strstr($mime,"image")||strstr($mime,"pdf")){

                $tmp[]=$filename;
            }
        }
        return $tmp;
    }
    static function Debug($str){
        $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
        $txt="";    
        if(!is_array($str))$txt = $str;
            else{
                foreach ($str as $key => $value) {
                   $txt.= $key ." : ".$value."\n";
                }
            }
            fwrite($myfile, $txt);            
            fclose($myfile);
    }
    static function getStartAndEndDate($week, $year) {
        $dto = new DateTime();
        $ret[] = $dto->setISODate($year, $week);
        $ret[] = $dto->modify('+6 days');
        return $ret;
      }
      static function test_input($input) {
        $datas = is_array($input)?$input:$data=[$input];
        foreach($datas as $data){
            if(is_array($data)){$data=self::test_input($data);}
            if(!is_string($data))continue;
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
        }
        return $datas;
    }
}