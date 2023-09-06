<?php
abstract class API{
    static function Routing(){      

        $path = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH),"/");
        $_DATA=[];
        if($_SERVER["REQUEST_METHOD"]=="GET"){$_DATA=Utils::test_input($_GET);$path.="|GET";}
        elseif($_SERVER["REQUEST_METHOD"]=="POST"){$_DATA=count($_POST)>0?Utils::test_input($_POST):Utils::test_input(Utils::GetBodyParams());$path.="|POST";}
        elseif($_SERVER["REQUEST_METHOD"]=="PUT"){$_DATA = Utils::test_input(Utils::GetBodyParams());$path.="|PUT";}
        elseif($_SERVER["REQUEST_METHOD"]=="DELETE"){ $_DATA = Utils::test_input(Utils::GetURLParams());$path.="|DELETE";}
        
        self::Navigate($path,$_DATA);
    }
    
    private static function Navigate($path,$_DATA){
        global $USER;
        $isAdmin = !is_null($USER)&&$USER->isAdmin();
        $userId = !is_null($USER)&&$USER->id;
        switch($path){
            ////REST////
            case "login|POST":  User::Login($_DATA["id"],true); break;
            case "login|DELETE":User::Logout();break;
            case "order|GET":if(isset($_DATA["id"])){ApiResponse::OrderStatus(Shoe::GetOrder($_DATA["id"]));}else{ApiResponse::OrderStatus(Shoe::GetAvalibleOrders());};break;
            case "order|POST":if($isAdmin)self::UploadNewOrder($_DATA);
            case "order|PUT":if($isAdmin)Shoe::UpdateFromArray($_DATA,$_DATA["order_id"]);Shoe::FileListUpdate($_DATA["order_id"]);            break;
            case "process|GET":ApiResponse::ProcessStatus(Shoe::GetOrder($_DATA["id"]),$_DATA["proc"]);break;
            case "process|POST":if($isAdmin)self::NewStatus($_DATA);break;
            case "process|DELETE":if($isAdmin)Process::Delete($_DATA);break;
            case "process|PUT":if($userId)self::SetStatusDone($_DATA,$userId);break;
            case "file|GET":self::ShowFile($_DATA["id"],$_DATA["filename"]);break;
            case "file|POST":if($isAdmin)FileUpload::TemporaryUpload($userId);break;
            ////VIEW////
            case "login|GET": echo BaseView::BaseSite(Login::Form(),"Bejelentkezés"); break;
            case "|GET": 
            case "index.php|GET": echo BaseView::BaseSite(BaseView::OrderListByWeek(Shoe::GetAvalibleOrders()),"Lista"); break;
            case "order/new|GET": FileUpload::ClearTemp($userId);echo BaseView::BaseSite(NewOrder::OrderDetailsForm(null),"Új megrendelés");break;
            case "order/edit|GET": echo BaseView::BaseSite(Details::Workpage(Shoe::GetOrder($_DATA["id"])),$_DATA["id"]);break;
            case "order/location|GET": echo BaseView::BaseSite(NewOrder::SelectLocationForm(Shoe::GetAllHasLocation(),$_DATA["id"]),"Tábla");break;
            case "order/search|GET":echo BaseView::BaseSite(BaseView::OrderList(Shoe::GetOrdersByLike($_DATA["id"])),"Keresés");break;
            case "order/nonready|GET":echo BaseView::BaseSite(BaseView::OrderList(Shoe::GetNonreadyOrders()),"Előkészítés");break;
            case "order/archive|GET":echo BaseView::BaseSite(BaseView::OrderList(Shoe::GetArchiveOrders()),"Archív");break;
            case "order/byweek|GET":echo BaseView::BaseSite(BaseView::OrderListByWeek(Shoe::GetAvalibleOrders(),$_DATA["week"]),$_DATA["week"].".hét");break;
            case "order/specific|GET":echo BaseView::BaseSite(BaseView::OrderList(Shoe::GetOrdersWithAvalibleProcessForUserExtend($USER),true),"Specifikus");break;
            default : echo BaseView::BaseSite(MissingPage(),"404");
         }
    }
    /////////////////////////////////////
    //Alfunkciók:
    ////////////////////////////////////
   
    
    private static function SetStatusDone($_DATA,$userId=null){
        $where["order_id"]=$_DATA["order_id"];
        $where["name"]=$_DATA["proc"];
        Process::Update(["done"=>1,"user"=>$userId],$where);

    }
    
    private static function NewStatus($_DATA){
    
        $update=$_DATA["name"];
        $shoe=Shoe::GetOrder($_DATA["order_id"]);
        $array["done"]=$_DATA["done"];
        $array["req"] = $_DATA["req"];
        if(isset($_DATA["line"]))$array["line"]=$_DATA["line"];
        if(isset($_DATA["step"]))$array["step"]=$_DATA["step"];
        
        if(isset($shoe->processes[$update])){
            $where["order_id"]=$_DATA["order_id"];
            $where["name"]=$update;
            Process::Update($array,$where);
        }
        else{
            $array["name"] = $update;
            $array["order_id"] = $_DATA["order_id"];

            Process::Create($array);            
        }
    }

    private static function UploadNewOrder($_DATA){
        global $USER,$cfg;
        if($USER==null||!$USER->isAdmin()){echo BaseView::BaseSite(MissingPage(),"404");die;};
        $now=new DateTime("now");
        $now=$now->format("Y-m-d H:i");
        $dt=$_DATA["date"]." ".$_DATA["time"];
        $title=isset($_DATA["title"])?$_DATA["title"]:null;
		$tmp_names = glob('./Temp/'.$USER->id.'/*');
        $n =count($tmp_names);
        for($i=0;$i<count($tmp_names);$i++){
			 $id=pathinfo($tmp_names[$i],PATHINFO_FILENAME);	
			
			 Shoe::Create($id,$title,$_DATA["location"],$now,$dt,$tmp_names[$i]);
			
			if(isset($_DATA["saved"])&&$_DATA["saved"]!=null){
				
				$TMP=null;            
				$TMP=$cfg["Saved"][$_DATA["saved"]];
				if($TMP==null)return;
				foreach($TMP as $proc){
					$tmp = $proc;
					$tmp["order_id"]=$id;
					
					Process::Create($tmp);
				}
			}
		}
		
    }
    
    // Generál a fájlhoz elérési utat
    private static function ShowFile($id, $filename=null)
    {
        global $cfg;
        $filename = $filename ?? $id.".pdf";
        $url =$cfg["DATA"]."Aktív/".$id."/".$filename;
        $url2 =$cfg["DATA"]."Archív/".$id."/".$filename;

        if(is_file(".".$url)){
        header("Location: ".$url );}
        elseif(is_file(".".$url2)){
        header("Location: ".$url2 );}
        else{
            header("location: /404");
        }
    }

}