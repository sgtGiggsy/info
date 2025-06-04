<?php

if(!@$csoportolvas)
{
	getPermissionError();
}
else
{
    $adattabla = "munkalapok";
    $oldalnev = "munkalapok";
    $oldalcim = "Munkalista";
    $table = "modules/munkalapok/includes/munkalistatable";
    
    $where = $csoportwhere = $keres = null;
    $enablekeres = true;
    if(isset($_GET['kereses']))
    {
        $keres = $_GET['kereses'];
        $where = "WHERE felhasznalok.nev LIKE '%$keres%' OR szervezetek.rovid LIKE '%$keres%' OR mv1.nev LIKE '%$keres%' OR mv2.nev LIKE '%$keres%' OR leiras LIKE '%$keres%' OR eszkoz LIKE '%$keres%'";
    }
    
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => "szervezet",                        // A szűrés típusa, null = mindkettő, szervezet = szervezet, telephely = telephely
            'and' => false,                          // Kerüljön-e AND a parancs elejére
            'szervezetelo' => "felhasznalok",                  // A tábla neve, ahonnan az szervezet neve jön
            'telephelyelo' => null,           // A tábla neve, ahonnan a telephely neve jön
            'szervezetnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az szervezetszűréshez
            'telephelynull' => true,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'szervezetmegnevezes' => "szervezet"    // Az szervezetot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
        if(!$where)
        {
            $where = "WHERE ";
        }
        else
        {
            $csoportwhere = "AND $csoportwhere";
        }
    }
    

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/munkaszerkeszt'">Új munka</button><?php
    }
    
    include('././templates/lapozas.tpl.php');
}