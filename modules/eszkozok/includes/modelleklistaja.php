<?php

if(!@$mindolvas)
{
	getPermissionError();
}
else
{
    $tipus = $where = $modelltipid = null;
    if($elemid)
    {
        $modelltipid = $elemid;
        $where = "WHERE eszkoztipusok.nev = ?";
    }

    $modellek = new MySQLHandler("SELECT modellek.id AS id, gyartok.nev AS gyarto, modell, eszkoztipusok.nev AS tipus
            FROM modellek
                INNER JOIN gyartok ON modellek.gyarto = gyartok.id
                INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
            $where
            ORDER BY tipus, gyarto, modell;", $modelltipid);
    $modellek = $modellek->Result();

    $oszlopok = array(
        array('nev' => 'Gyártó', 'tipus' => 's'),
        array('nev' => 'Modell', 'tipus' => 's')
    );

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/modellszerkeszt'">Új modell</button><?php
    }

    ?><div class="oldalcim">Modellek listája</div><?php
    foreach($modellek as $modell)
    {
        $kattinthatolink = "$RootPath/eszkozalap/modellszerkeszt/" . $modell['id'];
        if($tipus != $modell['tipus'])
        {
            if($tipus)
            {
                ?></tbody>
                </table><?php
            }

            $tipus = $modell['tipus'];

            ?><h1 style="text-transform: capitalize;"><?=$tipus?></h1>
            <table id="<?=$tipus?>">
            <thead>
                <tr><?php
                    sortTableHeader($oszlopok, $tipus);
                ?></tr>
            </thead>
            <tbody><?php
        }

        ?><tr class='trlink'>
            <td><a href="<?=$kattinthatolink?>"><?=$modell['gyarto']?></a></td>
            <td><a href="<?=$kattinthatolink?>"><?=$modell['modell']?></a></td>
        </tr><?php
    }
    ?></tbody>
    </table><?php
}