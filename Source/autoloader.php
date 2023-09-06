<?php
$folders=["VIEW","MODEL","API"];
foreach (glob("PHP/*.php") as $filename)
    {
        include $filename;
    }
foreach ($folders as $folder)
{   
    foreach (glob("PHP/$folder/*.php") as $filename)
    {
        include $filename;
    }
}