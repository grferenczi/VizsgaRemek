<?php
abstract class Details{
    static function Workpage($shoe)
    {
        global $cfg;
        $orderDate=date('Y-m-d H:i:s',strtotime($shoe->orderDate));
        $closeDate=isset($shoe->finishDate)?date('Y-m-d H:i:s',strtotime($shoe->finishDate)):"";
        $deadlineDate=date('Y-m-d H:i:s',strtotime($shoe->deadlineDate));
        $prks=OrderCards::CreatePerkTreeTable($shoe->processes);
        $lines=$cfg['Lines'];
        $steps=$cfg['Steps'];
        $tmparr=$cfg["Processes"];
        $selectable='';        
        foreach($tmparr as $pro){
            $selectable.='<option value="'.$pro.'">'.$pro.'</option>';
        }
        $process='';
        if($shoe->processes){
        foreach($shoe->processes as $pro){
            $process.='<option value="'.$pro->name.'">'.$pro->name.'</option>';
        }}
        $opt="";
        $opt.='<option '.(($shoe->status==Shoe_Status::WaitFor)?"selected ":"").'value="0">Előkészítés</option>';
        $opt.='<option '.(($shoe->status==Shoe_Status::InProgress)?"selected ":"").'value="1">Folyamatban</option>';
        $opt.='<option '.(($shoe->status==Shoe_Status::OnHold)?"selected ":"").'value="2">Tartva</option>';
        $opt.='<option '.(($shoe->status==Shoe_Status::Complete)?"selected ":"").'value="3">Kész</option>';
        $opt.='<option '.(($shoe->status==Shoe_Status::Canceled)?"selected ":"").'value="4">Törölve</option>';
       $str = <<<EOT
       <div class="flex content-width center">
    <div class="">
        <div class="row smallcol">
        <div class="form-group col-12">
        <label  for="order_date">Rögzítés</label>
        <input class="form-control" type="datetime-local" readonly name="order_date" id="order_date" value="$orderDate">
        </div>
        <div class="form-group col-12">
        <label  for="close_date">Elkészült</label>
        <input class="form-control" type="datetime-local" readonly name="close_date" id="close_date" value="$closeDate">
        </div>
        <div class="form-group col-8">
            <label for="id">Azonosító</label>
            <input class="form-control" type="text" readonly name="order_id" id="id" value="$shoe->orderId">
        </div>
        <div class="form-group col-4">
        <label  for="location">Tárhely</label>
            <input class="form-control"type="text" readonly name="location" id="location" value="$shoe->location">
        </div>
        <input class="btn btn-outline-primary mt-3" onclick="window.location.href='/order/location?id=$shoe->orderId';" type="button" value="Tárhely modosítása">
            <div class="form-group">
            <label  for="title">Fejléc</label>
                <input class="form-control" type="text" name="title" id="title" value="$shoe->title">
            </div>
            <div class="form-group">
            <label for="deadline_date">Határidő</label>
                <input class="form-control" type="datetime-local"  name="deadline_date" id="deadline_date" value="$deadlineDate">
            </div>
            <div class="form-group">
            <label for="status">Státusz</label>
            <select class="form-select" id="status" name="status">
                $opt
            </select>
            </div>
            <div class="form-group">
            <label for="note">Megjegyzés</label>
                <textarea class="form-control" name="note" id="note" cols="30" rows="1">$shoe->note</textarea>
            </div>
            <button class="form-control my-3 btn btn-outline-primary" onclick="OrderUpdate('$shoe->orderId')">Mentés</button>
        </div>
    </div>
    <div class="fill">
    <fieldset>
    <legend>Folyamatok</legend>
    $prks
    <div>    
    
        <input hidden type="text" name="order_id" value="$shoe->orderId">
        <div class="row">
            <fieldset class="col-6 row">
            <label>Folyamat</label>   

                <div class="form-group col-12">
                    <select class="form-control" name="name" id="selected" onchange="ChangeSelection('$shoe->orderId',value)">
                    <option value=""></option>
                    $selectable
                </select>

            </div>
            <div class="form-group col-6">
                <label for="line">Sor</label>
                <input class="form-control" type="number" min="0" max="$lines" name="line" id="line">      
            </div>
            <div class="form-group col-6">
                <label for="step">Oszlop</label>
                <input class="form-control" type="number" min="0" max="$steps" name="step" id="step">      
            </div>
            <div class="mx-3 mt-2 form-check col-12">
                <input type="checkbox" class="form-check-input" id="done" name="done">
                <label class="form-check-label" for="done">Kész</label>
            </div>
            <div class="form-group col-6">
            <button class="btn btn-outline-primary fill mt-3" onclick="ProcessCreate('$shoe->orderId')">Mentés</button>
            </div>
            <div class="form-group col-6">
            <button class="btn btn-outline-danger fill mt-3" onclick="ProcessDelete('$shoe->orderId')">Törlés</button>
            </div>
            
            </fieldset>
            <fieldset class="col-6">
            <label>Függőségek</label>   
                    <select class="form-control my-1" name="req0" id="req0">
                    <option value=""></option>
                    $process
                    </select>
                    <select class="form-control my-1" name="req1" id="req1">
                    <option value=""></option>
                    $process
                    </select>
                    <select class="form-control my-1" name="req2" id="req2">
                    <option value=""></option>
                    $process
                    </select>
                    <select class="form-control my-1" name="req3" id="req3">
                    <option value=""></option>
                    $process
                    </select>
                </fieldset>
            
        </div>
    </form>   
</div>
    </div>
</div>
EOT;
return $str;
    } 

}
?>
