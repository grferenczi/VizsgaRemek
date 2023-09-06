<?php
abstract class BaseView{

  //View alap csonváza.
  static function BaseSite($body,$head = "Document"){
    $nav = self::BaseSiteNav();
    $str =<<<EOT
    <!DOCTYPE html>
    <html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>$head</title>
        <link rel="stylesheet" href="/css/bootstrap.css">
        <link rel="stylesheet" href="/css/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/min/dropzone.min.css" />
        <script src="/js/scripts.js"></script>
        </head>
    <body class="justify-content-center"> 
    $nav
    $body
        </body>
    </html>
    EOT;
    return $str;
}

//View Navigációs sáv
  private static function BaseSiteNav(){
      global $USER;
      $login = '<input type="button" class="btn" onClick="window.location.href = \'/login\';" '.(isset($_SESSION["user"])&&!is_null($_SESSION["user"])?"hidden":"").' value="Bejelentkezés">';
      $login .= '<input type="button" class="btn" onClick="Logout();" '.(!isset($_SESSION["user"])||is_null($_SESSION["user"])?"hidden":"").' value="Kijelentkezés">';
      $userspecific=!is_null($USER)?'<li class="nav-item">
      <a class="nav-link" href="/order/specific">Nekem</a>
    </li>':null;
      $forAdmin=$USER!=null&&$USER->role==UserRole::Admin?'
                  <li class="nav-item">
                    <a class="nav-link" href="/order/new">Új megrendelés</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="/order/nonready">Előkészítés</a>
                  </li>
                  ':"";
      $str =<<<EOT
        <nav class="navbar navbar-expand-sm bg-body-tertiary">
            <div class="container-fluid nav-content-width">
                <ul class="navbar-nav">
                $userspecific
                  <li class="nav-item">
                    <a class="nav-link" href="/">Lista</a>
                  </li>
                  <li class="nav-item">
                  <a class="nav-link" href="/order/archive">Archiváltak</a>
                  </li>
                  $forAdmin
                </ul>            
            
              <form class="d-flex" action="/order/search">
                 $login
                  <input class="form-control me-2" required type="search" name="id" placeholder="Keresés" aria-label="Search">
                  <button class="btn btn-outline-success" type="submit">Keresés</button>
                </form>
            </div>
      
        </nav>
        EOT;
        return $str;
    }
   
   // Egyszerű soronként kirajzolás
    static function OrderList($data,$haveDone=false){
      $str= '<div id="cards" class="cards-width">'.self::ListAllCards($data,$haveDone).'</div>';      
      return $str;
    }
    // Heti bontásos kirajzolás. Mindig csak 1 hét lehet nyitva.
    static function OrderListByWeek($data,$week=false){
      $dateArray=[];
      foreach($data as $shoe){
        $dt = date_create_from_format('Y-m-d H:i:s', $shoe->deadlineDate);
        $dateArray[$dt->format('W')]["count"]=isset($dateArray[$dt->format('W')])?$dateArray[$dt->format('W')]["count"]+1:1;
        $dateArray[$dt->format('W')]["year"]=$dt->format('Y');
      }
      $str="";
      foreach($dateArray as $date=>$dateData){
        $showCards=$week && $date==$week;
        $methodCall=$showCards?"window.location='/":"window.location='/order/byweek?week=".$date;
        $str.='
        <div class="content-width card my-3">
          <div class="card-header flex spacebetween bg-gradient" onclick="'.$methodCall.'\'">
            <h5 class="l">'.$date.'. hét</h5>
            <h5 class="center">'.Utils::getStartAndEndDate($date,$dateData["year"])[1]->modify('-2days')->format('Y-m-d').'.-ig</h5>
            <h5 id="'.$date.'-count" class="r">Összesen '.$dateData["count"].' db</h5>
          </div>
          <div class="card-body flex spacebetween" '.($showCards?"":"hidden").'>
          <div name="'.$date.'" id="'.$date.'" class="cards-width">
          '.($showCards?self::ListAllCards(Shoe::GetShoesByWeek($week,0,$data)):"").'
          </div>
          </div>
          </div>';
        }
      return $str;
    }
    
    private static function ListAllCards($data,$haveDone=false){
      $str="";
      if(!$data){return;}
      foreach($data as $shoe){
        $str.=OrderCards::CreateCardsinnerHtml($shoe,$haveDone);
      }
      return $str;
    }
    
}

