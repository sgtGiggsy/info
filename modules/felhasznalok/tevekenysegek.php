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
    $where = $keres = null;
    if(isset($_GET['kereses']))
    {
        $keres = $_GET['kereses'];
        $where = "WHERE felhasznalok.nev LIKE '%$keres%' OR felhasznalonev LIKE '%$keres%'";
        $keres = "?kereses=" . $keres;
    }

    include('././templates/lapozas.tpl.php');
}
?>