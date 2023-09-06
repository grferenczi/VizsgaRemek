<?php
include_once("autoloader.php");
$USER = null;
session_start();

//Felhasználó beléptetése.
if(isset($_SESSION["user"])&&$_SESSION["user"]!==null){
  $USER = new User($_SESSION["user"]);
}
API::Routing();