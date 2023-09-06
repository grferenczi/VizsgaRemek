<?php
abstract class ApiResponse{
    static function ProcessStatus($shoe, $proc){
        header('Content-type: application/json; charset=utf-8');
        if($shoe ==null) echo json_encode(null);
        if(isset($shoe->processes[$proc])){
            echo json_encode($shoe->processes[$proc]);
        }
        else echo json_encode(null);
    }
    static function OrderStatus($arr){
        header('Content-type: application/json; charset=utf-8');
        if(is_array($arr)){
            $str = "";
            foreach ($arr as $a) {
                $str.=json_encode($arr);
            }
            echo $str;
        }
        else{
            echo json_encode($arr);
        }
    }
   
}