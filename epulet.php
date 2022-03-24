<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $epid = $_GET['id'];
    $epuletek = mySQLConnect("SELECT epuletek.id AS id, szam, epuletek.nev AS nev, telephelyek.telephely AS telephely, epulettipusok.tipus AS tipus
        FROM epuletek
            LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
            LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
        WHERE epuletek.id = $epid;");
    $helyisegek = mySQLConnect("SELECT id, helyisegszam, helyisegnev, emelet
        FROM helyisegek
        WHERE epulet = $epid
        ORDER BY helyisegszam ASC;");
    
    $epulet = mysqli_fetch_assoc($epuletek);
    ?><div class="oldalcim"><?=$epulet['telephely']?> - <?=$epulet['szam']?>. <?=$epulet['tipus']?> (<?=$epulet['nev']?>)</div>
    <?=($mindir) ? "<a href='$RootPath/epuletszerkeszt/$epid'>Épület szerkesztése</a>" : "" ?>
    <div class="oldalcim">Helyiségek</div><?php
    $zar = false;
    foreach($helyisegek as $helyiseg)
    {
        if(@$emelet != $helyiseg['emelet'])
        {
            if($zar)
            {
                ?></tbody>
                </table><?php
            }

            $emelet = $helyiseg['emelet'];
            ?><h1><?=($helyiseg['emelet'] == 0) ? "Földszint" : $helyiseg['emelet'] . ". emelet" ?></h1>
            <table id="<?=$emelet?>">
            <thead>
                <tr>
                    <th class="tsorth" onclick="sortTable(0, 'i', '<?=$emelet?>')">Helyiség száma</th>
                    <th class="tsorth" onclick="sortTable(1, 's', '<?=$emelet?>')">Helyiség megnevezése</th>
                    <th></th>
                </tr>
            </thead>
            <tbody><?php
            $zar = true;
        }

        ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/helyiseg/<?=$helyiseg['id']?>'>
            <td><?=$helyiseg['helyisegszam']?></td>
            <td><?=$helyiseg['helyisegnev']?></td>
            <td><a href='<?=$RootPath?>/helyisegszerkeszt/<?=$helyiseg['id']?>'>Szerkesztés</a></td>
        </tr><?php
    }
    ?></tbody>
    </table><?php
}