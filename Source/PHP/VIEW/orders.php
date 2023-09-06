<?php
abstract class NewOrder{
    static function SelectLocationForm($shoes,$id=false){
        global $cfg, $USER ;
        $cols=$cfg["Columns"];
        $rows=$cfg["Rows"];
        $multarr=array_fill(0, $rows, NULL);
        for($r=0;$r<count($multarr);$r++)
        {
           $multarr[$r] = array_fill(0, $cols, NULL);
        }
        foreach($shoes as $shoe)
        {
            $multarr[($shoe->location-1)%$rows][($shoe->location-1)/$rows]=$shoe;  
        }
        $str="";
        for($i=0;$i<$rows;$i++){
            $str.="<tr id=".$i.">";
            for($j=0;$j<$cols;$j++){            
               if(!is_null($multarr[$i][$j])){
                $str.='<td class="bg-danger">'.$multarr[$i][$j]->orderId;
               }
               else{
                    $fnc = !$id?"new":"edit_location&id=".$id;
                  $str.='<td class="bg-success"><a class="clearlink" href="/order/edit?id='.$id.'" onclick="SetLocation('.$id.','.($j*$rows+$i+1).')">Szabad</a>';
               }
               $str.="</td>";
            }
            $str.="</tr>";
         } 
         $body=<<<EOT
         <div class="content-width">
            <table id="st" class="text-center location">
            $str
            </table>
         </div>
         EOT; 
         return $body;
    }
    static function OrderDetailsForm($location){
        global $cfg;
        $now = new DateTime("now");
        $dayofweek = date('w', $now->getTimestamp());
        if($dayofweek!=5)$now->modify("next friday");
        $now->modify("+2weeks");
        $now=date_format($now,"Y-m-d");
        $options='';
        if(isset($cfg["Saved"])&& count($cfg["Saved"])>0){
            foreach(array_keys($cfg["Saved"]) as $save){
                $options.='<option value="'.$save.'">'.$save.'</option>';
            }
        }
        $options.='<option value="">Nincs</option>';
        $str=<<<EOT
        <div class="smallcol center mt-50">
        <div class="form-group">
            <label for="title">Fejléc</label>
            <input type="text" class="form-control" name="title" id="title">
        </div>
        <div class="form-group">
            <label for="date">Tervezett</label>
            <div class="flex">
            <input type="date" class="form-control" value="$now" required name="date" id="date">
            <input type="time" class="form-control" value="12:00" required name="time" id="time">
            </div>
        </div>
        <div class="form-group">
            <label for="saved">Folyamatok</label>
            <select class="form-control my-1" name="saved" id="saved">
            $options
            </select>
        </div>
        
        <button class="btn btn-outline-success mt-3 fill" onclick="OrderNew()">Felvesz</button>
    </div>
    <form action="/file" method="post" enctype="multipart/form-data" class="dropzone editor-width center mt-3 mb-50" id="myDropzone"></form>
    <script src="/js/dropzone.js"></script>
    <script>
    Dropzone.options.myDropzone = {
        dictDefaultMessage: "Húzzd ide a fájlokat, vagy kattints ide a feltöltéshez",
        maxFilesize:1000,
        uploadMultiple: false   
    };
    </script>
    EOT;
    //<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/min/dropzone.min.js"></script>
    return $str;
    }
}