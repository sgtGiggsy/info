<?php

if(!isset($felhid) && isset($csoportolvas) && !$csoportolvas)
{
    getPermissionError();
}
else
{
    $adattabla = "felhasznalotevekenysegek";
    $oldalnev = "tevekenysegek";
    $oldalcim = "Felhasználói tevékenységek";
    $table = "modules/felhasznalok/includes/tevekenysegtable";

    $enablekeres = true;
    $keres = null;
    $where = "LEFT JOIN felhasznalok ON felhasznalotevekenysegek.felhasznalo = felhasznalok.id ";
    if(isset($_GET['kereses']))
    {
        $keres = $_GET['kereses'];
        $where .= "WHERE felhasznalok.nev LIKE '%$keres%' OR felhasznalonev LIKE '%$keres%'";
        $keres = "?kereses=" . $keres;
        $keresinheader = $_GET['kereses'];
    }

    include('././templates/lapozas.tpl.php');
}
?>