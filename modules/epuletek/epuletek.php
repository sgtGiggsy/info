<?php

if(!@$csoportolvas)
{
	getPermissionError();
}
else
{
    $csoportwhere = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => "telephely",                        // A szűrés típusa, null = mindkettő, szervezet = szervezet, telephely = telephely
            'and' => false,                          // Kerüljön-e AND a parancs elejére
            'szervezetelo' => null,                  // A tábla neve, ahonnan az szervezet neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'szervezetnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az szervezetszűréshez
            'telephelynull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'szervezetmegnevezes' => "tulajdonos"    // Az szervezetot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
    }

    $where = null;
    if(isset($_GET['id']))
    {
        $thelyid = $_GET['id'];
        $where = "WHERE epuletek.telephely = $thelyid";
    }

    if(!$where && isset($csoportwhere))
    {
        $where = "WHERE ";
    }

    $epuletek = mySQLConnect("SELECT epuletek.id AS id, szam, epuletek.nev AS nev, telephelyek.telephely AS telephely, epulettipusok.tipus AS tipus, epuletek.megjegyzes AS megjegyzes, naprakesz
        FROM epuletek
            LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
            LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
        $where $csoportwhere
        ORDER BY telephelyek.id, szam + 0;");

    $oszlopok = array(
        array('nev' => '&nbsp;&nbsp;', 'tipus' => 's'),
        array('nev' => 'Épületszám', 'tipus' => 'i'),
        array('nev' => 'Épület megnevezése', 'tipus' => 's'),
        array('nev' => 'Típus', 'tipus' => 's'),
        array('nev' => 'Megjegyzés', 'tipus' => 's')
    );
    if($csoportir)
    {
        $oszlopok[] = array('nev' => '', 'tipus' => 's');
    }

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/epulet?action=addnew'">Új épület</button><?php
    }

    ?><div class="oldalcim">Épületek listája</div><?php
    $zar = false;
    foreach($epuletek as $epulet)
    {
        if(@$telephely != $epulet['telephely'])
        {
            if($zar)
            {
                ?></tbody>
                </table><?php
            }

            $telephely = $epulet['telephely'];
            ?><h1><?=$epulet['telephely']?></h1>
            <table id="<?=$telephely?>">
            <thead>
                <tr><?php
                    sortTableHeader($oszlopok, $telephely);
                ?></tr>
            </thead>
            <tbody><?php
            $zar = true;
        }

        $kattinthatolink = $RootPath . "/epulet/" . $epulet['id'];
        ?><tr class="trlink">
            <td><a href="<?=$kattinthatolink?>"><?=($epulet['naprakesz']) ? "&check;" : "" ?></a></td>
            <td><a href="<?=$kattinthatolink?>"><?=$epulet['szam']?></a></td>
            <td><a href="<?=$kattinthatolink?>"><?=$epulet['nev']?></a></td>
            <td><a href="<?=$kattinthatolink?>"><?=$epulet['tipus']?></a></td>
            <td><a href="<?=$kattinthatolink?>"><?=$epulet['megjegyzes']?></a></td><?php
            if($mindir)
            {
                ?><td><a href='<?=$RootPath?>/epulet/<?=$epulet['id']?>?action=edit'><img src='<?=$RootPath?>/images/edit.png' alt='Épület szerkesztése' title='Épület szerkesztése'/></a></td><?php
            }
        ?></tr><?php
    }
    ?></tbody>
    </table><?php
}