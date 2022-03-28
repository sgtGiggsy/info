<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $where = null;
    if(isset($_GET['id']))
    {
        $modelltipid = $_GET['id'];
        $where = "WHERE modellek.tipus = $thelyid";
    }

    $modellek = mySQLConnect("SELECT modellek.id AS id, gyartok.nev AS gyarto, modell, eszkoztipusok.nev AS tipus
        FROM modellek
            INNER JOIN gyartok ON modellek.gyarto = gyartok.id
            INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
        $where
        ORDER BY tipus, gyarto, modell;");
    ?><?=($mindir) ? "<a href='$RootPath/modellszerkeszt'>Új modell hozzáadása</a>" : "" ?>
    <div class="oldalcim">Modellek listája</div><?php
    $zar = false;
    foreach($modellek as $modell)
    {
        if(@$tipus != $modell['tipus'])
        {
            if($zar)
            {
                ?></tbody>
                </table><?php
            }

            $tipus = $modell['tipus'];
            ?><h1 style="text-transform: capitalize;"><?=$tipus?></h1>
            <table id="<?=$tipus?>">
            <thead>
                <tr>
                    <th class="tsorth" onclick="sortTable(0, 's', '<?=$tipus?>?>')">Gyártó</th>
                    <th class="tsorth" onclick="sortTable(1, 's', '<?=$tipus?>?>')">Modell</th>
                    <th></th>
                </tr>
            </thead>
            <tbody><?php
            $zar = true;
        }

        ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/modellszerkeszt/<?=$modell['id']?>'>
            <td><?=$modell['gyarto']?></td>
            <td><?=$modell['modell']?></td>
        </tr><?php
    }
    ?></tbody>
    </table><?php
}