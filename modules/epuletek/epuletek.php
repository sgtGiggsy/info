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
            'tipus' => "telephely",                        // A szűrés típusa, null = mindkettő, alakulat = alakulat, telephely = telephely
            'and' => false,                          // Kerüljön-e AND a parancs elejére
            'alakulatelo' => null,                  // A tábla neve, ahonnan az alakulat neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'alakulatnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az alakulatszűréshez
            'telephelynull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'alakulatmegnevezes' => "tulajdonos"    // Az alakulatot tartalmazó mező neve a felhasznált táblában
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

    $epuletek = mySQLConnect("SELECT epuletek.id AS id, szam, epuletek.nev AS nev, telephelyek.telephely AS telephely, epulettipusok.tipus AS tipus
        FROM epuletek
            LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
            LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
        $where $csoportwhere
        ORDER BY telephelyek.id, szam + 0;");

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
                <tr>
                    <th class="tsorth" onclick="sortTable(0, 'i', '<?=$telephely?>')">Épületszám</th>
                    <th class="tsorth" onclick="sortTable(1, 's', '<?=$telephely?>')">Épület megnevezése</th>
                    <th class="tsorth" onclick="sortTable(2, 's', '<?=$telephely?>')">Típus</th><?php
                    if($mindir)
                    {
                        ?><th></th><?php
                    }
                ?></tr>
            </thead>
            <tbody><?php
            $zar = true;
        }

        ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/epulet/<?=$epulet['id']?>'>
            <td><?=$epulet['szam']?></td>
            <td><?=$epulet['nev']?></td>
            <td><?=$epulet['tipus']?></td><?php
            if($mindir)
            {
                ?><td><a href='<?=$RootPath?>/epulet/<?=$epulet['id']?>?action=edit'><img src='<?=$RootPath?>/images/edit.png' alt='Épület szerkesztése' title='Épület szerkesztése'/></a></td><?php
            }
        ?></tr><?php
    }
    ?></tbody>
    </table><?php
}