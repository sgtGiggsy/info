<?php

if(!$sajatolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $raktarak = mySQLConnect("SELECT raktarak.id AS id,
            raktarak.nev AS raktarnev,
            alakulatok.id AS alakulatid,
            alakulatok.rovid AS alakulat,
            helyisegek.id AS helyisegid,
            helyisegek.helyisegszam AS helyisegszam,
            helyisegek.helyisegnev AS helyisegnev,
            epuletek.szam AS epuletszam,
            epuletek.nev AS epuletnev,
            epulettipusok.tipus AS epulettipus,
            epuletek.telephely AS telephelyid,
            telephelyek.telephely AS telephely
        FROM raktarak
            LEFT JOIN alakulatok ON raktarak.alakulat = alakulatok.id
            LEFT JOIN helyisegek ON raktarak.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
            LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
        ORDER BY telephely, epuletek.szam + 1, helyisegszam;");

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/raktar?action=addnew'">Új raktár</button><?php
    }
    
    ?><div class="oldalcim">Raktárak</div>
    <table id="raktarak">
        <thead>
            <tr>
                <th class="tsorth" onclick="sortTable(0, 's', 'raktarak')">Raktár</th>
                <th class="tsorth" onclick="sortTable(1, 's', 'raktarak')">Raktár helye</th>
                <th class="tsorth" onclick="sortTable(2, 's', 'raktarak')">Telephely</th>
                <th class="tsorth" onclick="sortTable(3, 's', 'raktarak')">Alakulat</th>
                <th></th>
            </tr>
        </thead>
        <tbody><?php
            foreach($raktarak as $raktar)
            {
                $raktarid = $raktar['id'];
                $kattinthatolink = $RootPath . '/raktar/' . $raktar['id'];
                ?><tr class="trlink">
                    <td><a href="<?=$kattinthatolink?>"><?=$raktar['raktarnev']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$raktar['epuletszam']?>. <?=$raktar['epulettipus']?> - <?=$raktar['helyisegszam']?> (<?=$raktar['helyisegnev']?>)</a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$raktar['telephely']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$raktar['alakulat']?></a></td>
                    <td><?=($csoportir) ? "<a href='$RootPath/raktar/$raktarid?action=edit'><img src='$RootPath/images/edit.png' alt='Raktár szerkesztése' title='Raktár szerkesztése'/></a>" : "" ?></td>
                </tr><?php
            }
        ?></tbody>
    </table><?php
}