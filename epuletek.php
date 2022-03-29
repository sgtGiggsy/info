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
        $thelyid = $_GET['id'];
        $where = "WHERE epuletek.telephely = $thelyid";
    }

    $epuletek = mySQLConnect("SELECT epuletek.id AS id, szam, epuletek.nev AS nev, telephelyek.telephely AS telephely, epulettipusok.tipus AS tipus
    FROM epuletek
        LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
        LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
    $where
    ORDER BY telephely, szam + 0;");

    ?><?=($mindir) ? "<a href='$RootPath/epuletszerkeszt'>Új épület hozzáadása</a>" : "" ?>
    <div class="oldalcim">Épületek listája</div><?php
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
                    <th class="tsorth" onclick="sortTable(2, 's', '<?=$telephely?>')">Típus</th>
                    <th></th>
                </tr>
            </thead>
            <tbody><?php
            $zar = true;
        }

        ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/epulet/<?=$epulet['id']?>'>
            <td><?=$epulet['szam']?></td>
            <td><?=$epulet['nev']?></td>
            <td><?=$epulet['tipus']?></td>
            <td><a href='<?=$RootPath?>/epuletszerkeszt/<?=$epulet['id']?>'><img src='<?=$RootPath?>/images/edit.png' alt='Épület szerkesztése' title='Épület szerkesztése'/></a></td>
        </tr><?php
    }
    ?></tbody>
    </table><?php
}