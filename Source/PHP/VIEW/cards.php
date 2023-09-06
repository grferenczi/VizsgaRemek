<?php
abstract class OrderCards{
   // Kártya html generálása
   static function CreateCardsinnerHtml($shoe,$haveDoneButton=false){
      global $USER;
      $skills = $USER!=null?$USER->skills:null;
      $avalibleSkills=[];
      if($skills!=null){

         foreach($skills as $skill){
            foreach ($shoe->avalibleprocesses as $avaproc)
            {
               if($skill==$avaproc){$avalibleSkills[]=$skill;}
            }
         }
      }

      $arr="";
      $arr.= $shoe->orderId;
      $arr.= $shoe->title?" - ".$shoe->title:"";
      $arr.= $shoe->carring?" || Elvitte-> ".$shoe->carring->name:"";
      $ddate=date('Y-m-d H:i',strtotime($shoe->deadlineDate));//
      $button=null;
      if($haveDoneButton)$button=!is_null($USER)&&($USER->role==UserRole::Modeller||$USER->role==UserRole::Admin)&&count($avalibleSkills)==1?'<input type="button" class="btn btn-primary" onclick="ConfirmSetDone(\''.$shoe->orderId.'\',\''.$avalibleSkills[0].'\')" value="Kész">':$button;
      else $button=!is_null($USER)&&$USER->role==UserRole::Admin?'<input type="button" class="btn btn-dark" onclick="window.location =\'/order/edit?id='.$shoe->orderId.'\'" value="Szerkeszt">':"";
      $body = self::CreatePerkTreeTable($shoe->processes,$skills);
      $status=self::ShoeStatusClass($shoe->status);      
      $note = $shoe->note?'<p class="mx-3">'.$shoe->note.'</p>':"";
      $filelist=self::FileList($shoe->orderId,$shoe->files);
      
      $base=<<<EOT
               <div id="$shoe->orderId" class="card my-3">
                  <div class="card-header flex spacebetween bg-gradient $status" onclick="window.location.href='/file?id=$shoe->orderId';">
                     <h5 class="l">$arr</h5>
                     <h5 class="r">$ddate</h5>
                  </div>
                  <div class="card-body flex spacebetween">
                     $body
                     $button
                  </div>
                  $filelist
                 $note
               </div>
            EOT;
      return $base;
   }
   // File elérési link létrehozása
   private static function FileList($id, $files){
      $filelist ="";
      if($files){
         $filelist .='<ul class="fileul">';
         foreach($files as $filename){
            $filelist .= '<li class="fileli"><a href="/file?id='.$id.'&filename='.$filename.'">'.$filename.'</a></li>';
         }
         $filelist .="</ul>";
      }
      return $filelist;
   }
   // Háttér színezés státusz szerint
   private static function ShoeStatusClass($status) {
      switch($status){
         case Shoe_Status::Canceled:$status="bg-danger";break;
         case Shoe_Status::OnHold:$status="bg-warning";break;
         case Shoe_Status::WaitFor:$status="bg-info";break;
         case Shoe_Status::Complete:$status="bg-success";break;
         case Shoe_Status::InProgress:$status="bg-light";break;
         default:$status="";break;
      }
      return $status;
   }

   // Folymatok táblázatba rendezése
   static function CreatePerkTreeTable($processes,$skills=null){
      if(!$processes){return;}
      global $cfg;
      $lines = $cfg["Lines"];
      $steps = $cfg["Steps"];
      $multarr=array_fill(0, $lines, NULL);
      for($r=0;$r<count($multarr);$r++)
      {
         $multarr[$r] = array_fill(0, $steps, NULL);
      }
      foreach($processes as $proc)
      {
         $multarr[$proc->line][$proc->step] = $proc;         
      }   
      
      $str ="";
      for($i=0;$i<$lines;$i++){
         $str.="<tr id=".$i.">";
         for($j=0;$j<$steps;$j++){            
            if(!is_null($multarr[$i][$j])){
               $str.="<td id=".$multarr[$i][$j]->name.">";
               $class="badge badge-pill fill ";
               if($multarr[$i][$j]->done){$class.="bg-success";}               
               else if($multarr[$i][$j]->avalible){
                  if(!is_null($skills)&&is_array($skills)&&in_array($multarr[$i][$j]->name,$skills)){
                     $class.="bg-danger";
                  }
                  else{ $class.="bg-primary";}
                 
               }
               else{$class.="bg-secondary";}
               $str.='<div title="'.$multarr[$i][$j]->req.'" class="'.$class.'">';
               $str.=$multarr[$i][$j]->name;
               $str.='</div>';
            }
            else{
               $str.="<td>";
            }
            $str.="</td>";
         }
         $str.="</tr>";
      }
      $base=<<<EOT
            <Table class="perk">
               $str
            </Table>
            EOT;            
      return $base;
   }
}


