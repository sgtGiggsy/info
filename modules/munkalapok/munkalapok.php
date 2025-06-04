<?php

if(!@$csoportolvas)
{
	getPermissionError();
}
else
{
    $contextmenu = array(
        'lista' => array('gyujtooldal' => '', 'oldal' => 'lista', 'gyujtooldalnev' => 'Lista', 'oldalnev' => 'Lista'),
        'templateek' => array('gyujtooldal' => 'templateek', 'oldal' => 'munkalapok/templateszerkeszt', 'gyujtooldalnev' => 'Templateek', 'oldalnev' => 'Template'),
        'nyomtatasikep' => array('gyujtooldal' => 'nyomtatasikep', 'oldal' => 'nyomtatasikep', 'gyujtooldalnev' => 'Nyomtatási kép', 'oldalnev' => 'Nyomtatási kép'),
        'beallitasok' => array('gyujtooldal' => 'beallitasok', 'oldal' => 'beallitasok', 'gyujtooldalnev' => 'Beállítások', 'oldalnev' => 'Beállítások')
    );

    $contextmenujogok['lista'] = $contextmenujogok['templateek'] = $contextmenujogok['nyomtatasikep'] = $contextmenujogok['beallitasok'] = true;

    if(isset($_GET['id']))
        $aloldal = $_GET['id'];
    elseif(isset($_GET['subpage']) && $_GET['subpage'] != "oldal")
        $aloldal = $_GET['subpage'];
    else
        $aloldal = "lista";

    $page = @fopen("./modules/munkalapok/includes/$aloldal.php", "r");
    if(!$page)
    {
        echo $aloldal . "<br>";
        http_response_code(404);
        echo "<h2>A keresett oldal nem található!</h2>";
    }
    else
    {
        include("./modules/munkalapok/includes/$aloldal.php");
    }
}