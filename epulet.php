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
    
    $epulet = mysqli_fetch_assoc($epuletek);
    ?><div class="oldalcim"><?=$epulet['telephely']?> - <?=$epulet['szam']?>. <?=$epulet['tipus']?> (<?=$epulet['nev']?>)</div>
    <?=($mindir) ? "<a href='$RootPath/epuletszerkeszt/$epid'>Épület szerkesztése</a>" : "" ?>
    <table>
        <thead>
            <tr>
                <th>Telephely</th>
                <th>Épületszám</th>
                <th>Épület megnevezése</th>
                <th>Típus</th>
            </tr>
        </thead>
        <tbody><?php
            foreach($epuletek as $epulet)
            {
                ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/epulet/<?=$epulet['id']?>'>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr><?php
            }
        ?></tbody>
    </table><?php
}