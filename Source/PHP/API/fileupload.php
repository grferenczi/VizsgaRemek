<?php
abstract class FileUpload{

    // Ideiglenesen eltárolja a fájlokat ameddig az új megrendelések nem leszenk rögzítve
    // TODO: befoltozni a biztonsági rést -> Meg lehet tölteni a tárhelyet felesleges fájlokkal.-> Megadni a tempnek egy maximális méretet.
    static function TemporaryUpload($id){
        global $cfg;
        
        $dir=$cfg["ROOT"]."/Temp/".$id."/";
        if ( !file_exists( $dir ) && !is_dir( $dir ) ) {
            mkdir( $dir );       
        }
       
        
        if ($_FILES["file"]["error"] > 0)
        {            
            Utils::Debug("Error Code: " . $_FILES["file"]["error"]);
        }
        else
        {
            move_uploaded_file($_FILES["file"]["tmp_name"],$dir . $_FILES['file']['name']); 
        }
    
        if (false&&!empty($_FILES)) {
            $tempFile = $_FILES['file']['tmp_name']; 
            $targetFile =  $dir . $_FILES['file']['name'];
            
            rename($tempFile, $targetFile);
        }
        

    }
    // Eltávolítja az ideiglenes fájlpokat.
    static function ClearTemp($id){
        $files = glob('./Temp/'.$id.'/*'); 
        foreach($files as $file){ 
        if(is_file($file)) {
        unlink($file); 
             }
        }
    }
    

}