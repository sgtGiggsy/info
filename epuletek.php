<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $epuletek = mySQLConnect("SELECT epuletek.id AS id, szam, epuletek.nev AS nev, telephelyek.telephely AS telephely, epulettipusok.tipus AS tipus
    FROM epuletek
        LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
        LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
    ORDER BY telephely, szam + 0;");
    ?><div class="oldalcim">Épületek adminisztrációja</div>
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
                    <td><?=$epulet['telephely']?></td>
                    <td><?=$epulet['szam']?></td>
                    <td><?=$epulet['nev']?></td>
                    <td><?=$epulet['tipus']?></td>
                </tr><?php
            }
        ?></tbody>
    </table><?php
}