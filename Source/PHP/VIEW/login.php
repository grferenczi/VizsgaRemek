<?php
abstract class Login{
 static function Form(){
    $str=<<<EOT
    <div class="center login-box" action="/login" method="POST">
    <div class="form-group">
        <label for="id">Azonosító</label>
        <input type="password" class="form-control" required name="id" id="user_id">
      </div>
      <button class="btn btn-outline-success mt-1" onclick="Login();">Belépés</button>
      </div>
    EOT;
    return $str;
 }
 

}
