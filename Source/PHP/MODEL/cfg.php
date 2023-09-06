<?php
//Restapi paraméter:
$cfg=array();
$cfg["Domain"]="localhost"; //Nincs használatban
$cfg["ROOT"]=$_SERVER["DOCUMENT_ROOT"];
$cfg["DATA"]="/Rendelések/";
$cfg["DATAPATH"]=$cfg["ROOT"].$cfg["DATA"]; //Ne!

//Adatbázis paraméter:
$cfg["DBDomain"]="localhost";
$cfg["DBUser"]="root";
$cfg["DBPass"]="";
$cfg["DBName"]="orderdb";

//Kártya paraméter:
$cfg["Lines"]=5;
$cfg["Steps"]=10;

//Hotelszoba paraméter:
$cfg["Rows"]=6;
$cfg["Columns"]=7;

//Tárolt folyamatok:
$cfg["Processes"] = ["Kaptafa jelölés","3D nyomtatás","Tervezés","Szabás","Tűzés","Alja","Meo","Talp CAM","Talp marás","Betét CAM","Betét marás","Betét bevonás"];

$cfg["Saved"]["Normal"][] = ["name"=>"Kaptafa jelölés","line"=>'1',"step"=>'0'];
$cfg["Saved"]["Normal"][] = ["name"=>"3D nyomtatás","line"=>'1',"step"=>'1',"req"=>"Kaptafa jelölés"];
$cfg["Saved"]["Normal"][] = ["name"=>"Tervezés","line"=>'0',"step"=>'0',"req"=>"Kaptafa jelölés"];
$cfg["Saved"]["Normal"][] = ["name"=>"Szabás","line"=>'0',"step"=>'1',"req"=>"Tervezés"];
$cfg["Saved"]["Normal"][] = ["name"=>"Tűzés","line"=>'0',"step"=>'2',"req"=>"Szabás"];
$cfg["Saved"]["Normal"][] = ["name"=>"Alja","line"=>'0',"step"=>'3',"req"=>"Tűzés|3D nyomtatás"];
$cfg["Saved"]["Normal"][] = ["name"=>"Meo","line"=>'0',"step"=>'4',"req"=>"Alja"];

$cfg["Saved"]["Talp"][] = ["name"=>"Kaptafa jelölés","line"=>'1',"step"=>'0'];
$cfg["Saved"]["Talp"][] = ["name"=>"3D nyomtatás","line"=>'1',"step"=>'1',"req"=>"Kaptafa jelölés"];
$cfg["Saved"]["Talp"][] = ["name"=>"Tervezés","line"=>'0',"step"=>'0',"req"=>"Kaptafa jelölés"];
$cfg["Saved"]["Talp"][] = ["name"=>"Szabás","line"=>'0',"step"=>'1',"req"=>"Tervezés"];
$cfg["Saved"]["Talp"][] = ["name"=>"Tűzés","line"=>'0',"step"=>'2',"req"=>"Szabás"];
$cfg["Saved"]["Talp"][] = ["name"=>"Alja","line"=>'0',"step"=>'3',"req"=>"Tűzés|3D nyomtatás|Talp marás"];
$cfg["Saved"]["Talp"][] = ["name"=>"Meo","line"=>'0',"step"=>'4',"req"=>"Alja"];
$cfg["Saved"]["Talp"][] = ["name"=>"Talp CAM","line"=>'2',"step"=>'0'];
$cfg["Saved"]["Talp"][] = ["name"=>"Talp marás","line"=>'2',"step"=>'1',"req"=>"Talp CAM"];

$cfg["Saved"]["Talp+Betét"][] = ["name"=>"Kaptafa jelölés","line"=>'1',"step"=>'0'];
$cfg["Saved"]["Talp+Betét"][] = ["name"=>"3D nyomtatás","line"=>'1',"step"=>'1',"req"=>"Kaptafa jelölés"];
$cfg["Saved"]["Talp+Betét"][] = ["name"=>"Tervezés","line"=>'0',"step"=>'0',"req"=>"Kaptafa jelölés"];
$cfg["Saved"]["Talp+Betét"][] = ["name"=>"Szabás","line"=>'0',"step"=>'1',"req"=>"Tervezés"];
$cfg["Saved"]["Talp+Betét"][] = ["name"=>"Tűzés","line"=>'0',"step"=>'2',"req"=>"Szabás"];
$cfg["Saved"]["Talp+Betét"][] = ["name"=>"Alja","line"=>'0',"step"=>'3',"req"=>"Tűzés|3D nyomtatás|Marás"];
$cfg["Saved"]["Talp+Betét"][] = ["name"=>"Meo","line"=>'0',"step"=>'4',"req"=>"Alja|Betét bevonás"];
$cfg["Saved"]["Talp+Betét"][] = ["name"=>"Talp CAM","line"=>'2',"step"=>'0'];
$cfg["Saved"]["Talp+Betét"][] = ["name"=>"Talp marás","line"=>'2',"step"=>'1',"req"=>"Talp CAM"];
$cfg["Saved"]["Talp+Betét"][] = ["name"=>"Betét CAM","line"=>'2',"step"=>'2'];
$cfg["Saved"]["Talp+Betét"][] = ["name"=>"Betét marás","line"=>'2',"step"=>'3',"req"=>"Betét CAM"];
$cfg["Saved"]["Talp+Betét"][] = ["name"=>"Betét bevonás","line"=>'2',"step"=>'4',"req"=>"Betét marás"];
