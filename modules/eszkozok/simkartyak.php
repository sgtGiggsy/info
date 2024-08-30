<?php

if(!$sajatolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $szuresek = getWhere();
    $where = $szuresek['where'];

    $mindeneszkoz = mySQLConnect("SELECT
            eszkozok.id AS id,
            sorozatszam,
            telefonszam,
            pinkod,
            pukkod,
            simkartyak.tipus AS tipid,
            simtipusok.nev AS tipus,
            simkartyak.felhasznaloszam AS felhasznaloszamid,
            simfelhasznaloszamok.nev AS felhasznaloszam,
            szervezetek.rovid AS tulajdonos,
            eszkozok.megjegyzes AS megjegyzes,
            raktarak.nev as raktar,
            hibas
        FROM eszkozok
                INNER JOIN simkartyak ON eszkozok.id = simkartyak.eszkoz
                LEFT JOIN raktarak ON eszkozok.raktar = raktarak.id
                LEFT JOIN szervezetek ON eszkozok.tulajdonos = szervezetek.id
                LEFT JOIN simtipusok ON simkartyak.tipus = simtipusok.id
                LEFT JOIN simfelhasznaloszamok ON simkartyak.felhasznaloszam = simfelhasznaloszamok.id;");

    $tipus = 'simkartyak';
    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/simkartya?action=addnew'">Új SIMkártya</button><?php
    }
    $oszlopok = array(
        array('nev' => 'IMEI szám', 'tipus' => 's'),
        array('nev' => 'Telefonszám', 'tipus' => 's'),
        array('nev' => 'Típus', 'tipus' => 's'),
        array('nev' => 'Felhasználószám', 'tipus' => 's'),
        array('nev' => 'PIN kód', 'tipus' => 's'),
        array('nev' => 'PUK kód', 'tipus' => 's'),
        array('nev' => 'Raktár', 'tipus' => 's')
    );
    if($csoportir)
    {
        $oszlopok[] = array('nev' => 'Megjegyzés', 'tipus' => 's');
    }


    ?><div class="PrintArea">
        <div class="oldalcim">SIM kártyák <?=$szuresek['szures']?> <?=keszletFilter($_GET['page'], $szuresek['filter'])?></div>
        <table id="<?=$tipus?>">
            <thead>
                <tr><?php
                    sortTableHeader($oszlopok, $tipus, true);
                    if($csoportir)
                    {
                        ?><th class="dontprint"></th>
                        <th class="dontprint"></th>
                        <th class="dontprint"></th><?php
                    }
                ?></tr>
            </thead>
            <tbody><?php
                $nembeepitett = array();
                foreach($mindeneszkoz as $sim)
                {
                    $kattinthatolink = './eszkozszerkeszt/' . $eszkoz['id'] . '?tipus=simkartya';
                    ?><tr class='trlink'>
                        <td><a href="<?=$kattinthatolink?>"><?=$sim['sorozatszam']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$sim['telefonszam']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$sim['tipus']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$sim['felhasznaloszam']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$sim['pinkod']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$sim['pukkod']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$sim['raktar']?></a></td><?php
                        if($csoportir)
                        {
                            ?><td><a href="<?=$kattinthatolink?>"><?=$sim['megjegyzes']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><!-- Szerződés szerkesztése placeholder --></a></td>
                            <td><a href="<?=$kattinthatolink?>"><!-- Új szerződés placeholder --></a></td>
                            <td><a href='<?=$RootPath?>/eszkozszerkeszt/<?=$sim['id']?>?tipus=simkartya'><img src='<?=$RootPath?>/images/edit.png' alt='SIM kártya szerkesztése' title='SIM kártya szerkesztése'/></a></td><?php
                        }
                    ?></tr><?php
                }
            ?></tbody>
        </table>
    </div><?php
}