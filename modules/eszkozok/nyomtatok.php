<?php

if(!$sajatolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $szuresek = getWhere("modellek.tipus = 12");
    $where = $szuresek['where'];

    $csoportwhere = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => null,                        // A szűrés típusa, null = mindkettő, szervezet = szervezet, telephely = telephely
            'and' => true,                          // Kerüljön-e AND a parancs elejére
            'szervezetelo' => null,                  // A tábla neve, ahonnan az szervezet neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'szervezetnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az szervezetszűréshez
            'telephelynull' => true,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'szervezetmegnevezes' => "tulajdonos"    // Az szervezetot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
    }

    $mindeneszkoz = mySQLConnect("SELECT
            eszkozok.id AS id,
            sorozatszam,
            gyartok.nev AS gyarto,
            modellek.modell AS modell,
            varians,
            eszkoztipusok.nev AS tipus,
            epuletek.nev AS epuletnev,
            epuletek.szam AS epuletszam,
            helyisegszam,
            helyisegnev,
            beepitesideje,
            kiepitesideje,
            modellek.tipus AS tipusid,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            beepitesek.id AS beepid,
            ipcimek.ipcim AS ipcim,
            beepitesek.megjegyzes AS megjegyzes,
            szines,
            scanner,
            fax,
            admin,
            pass,
            defadmin,
            defpass,
            maxmeret,
            hibas
        FROM eszkozok
            INNER JOIN modellek ON eszkozok.modell = modellek.id
            LEFT JOIN nyomtatomodellek ON nyomtatomodellek.modell = modellek.id
            INNER JOIN gyartok ON modellek.gyarto = gyartok.id
            INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
            LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
            LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
            LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
        WHERE $where $csoportwhere
        ORDER BY epuletek.szam + 0, helyisegszam + 0, helyisegnev;");

    $tipus = "nyomtatok";
    $oszlopok = array(
        array('nev' => 'IP cím', 'tipus' => 's'),
        array('nev' => 'Eszköznév', 'tipus' => 's'),
        array('nev' => 'Gyártó', 'tipus' => 's'),
        array('nev' => 'Modell', 'tipus' => 's'),
        array('nev' => 'Méret', 'tipus' => 's'),
        array('nev' => 'Színek', 'tipus' => 's'),
        array('nev' => 'Scanner', 'tipus' => 's'),
        array('nev' => 'Fax', 'tipus' => 's'),
        array('nev' => 'Sorozatszám', 'tipus' => 's'),
        array('nev' => 'Épület', 'tipus' => 's'),
        array('nev' => 'Helyiség', 'tipus' => 's')
    );
    if($csoportir)
    {
        $oszlopok[] = array('nev' => 'Admin', 'tipus' => 's');
        $oszlopok[] = array('nev' => 'Jelszó', 'tipus' => 's');
        $oszlopok[] = array('nev' => 'Megjegyzés', 'tipus' => 's');
    }
    else
    {
        $oszlopok[] = array('nev' => '', 'tipus' => 's');
        $oszlopok[] = array('nev' => '', 'tipus' => 's');
        $oszlopok[] = array('nev' => '', 'tipus' => 's');
    }

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/nyomtato?action=addnew'">Új nyomtató</button><?php
    }
    ?>
    <!-- DATALISTEK -->
    <datalist id="maxmeret">
        <option>A4</option>
        <option>A3</option>
        <option>A2</option>
        <option>A1</option>
        <option>A0</option>
    </datalist>

    <datalist id="scanner">
        <option>Van</option>
        <option>Nincs</option>
    </datalist>

    <datalist id="szinek">
        <option>Színes</option>
        <option>Fekete-Fehér</option>
    </datalist>

    <datalist id="fax">
        <option>Van, beépített</option>
        <option>Alkalmas, modullal</option>
        <option>Nincs</option>
    </datalist>
    
    <div class="PrintArea">
        <div class="oldalcim">Nyomtatók <?=$szuresek['szures']?> <?=keszletFilter($_GET['page'], $szuresek['filter'])?></div><?php
        ?><table id="<?=$tipus?>">
        <thead>
            <tr><?php
                sortTableHeader($oszlopok, $tipus, true);
                if($csoportir)
                {
                    ?><th class="dontprint"></th>
                    <th class="dontprint"></th>
                    <th class="dontprint"></th>
                    <th class="dontprint"></th><?php
                }
            ?></tr>
        </thead>
        <tbody><?php
            $nembeepitett = array();
            foreach($mindeneszkoz as $eszkoz)
            {            
                if(!($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje']))
                {
                    $nembeepitett[] = $eszkoz;
                }
                else
                {
                    switch($eszkoz['maxmeret'])
                    {
                        case 1: $maxmeret = "A4"; break;
                        case 2: $maxmeret = "A3"; break;
                        case 3: $maxmeret = "A2"; break;
                        case 4: $maxmeret = "A1"; break;
                        case 5: $maxmeret = "A0"; break;
                        default: $maxmeret = "A4";
                    }
                    switch($eszkoz['szines'])
                    {
                        case 1: $szines = "Színes"; break;
                        default: $szines = "Fekete-Fehér";
                    }
                    switch($eszkoz['scanner'])
                    {
                        case 1: $scanner = "Van"; break;
                        default: $scanner = "Nincs";
                    }
                    switch($eszkoz['fax'])
                    {
                        case 1: $fax = "Van, beépített"; break;
                        case 2: $fax = "Alkalmas, modullal"; break;
                        default: $fax = "Nincs";
                    }
                    $kattinthatolink = './nyomtato/' . $eszkoz['id'];
                    ?><tr class='trlink<?=($eszkoz['hibas'] == 1) ? " reszhibas" : "" ?>'>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['ipcim']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['beepitesinev']?></a></td>
                        <td nowrap><a href="<?=$kattinthatolink?>"><?=$eszkoz['gyarto']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['modell']?><?=$eszkoz['varians']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$maxmeret?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$szines?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$scanner?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$fax?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['sorozatszam']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></a></td><?php
                        if($csoportir)
                        {
                            ?><td><a href="<?=$kattinthatolink?>"><?=($eszkoz['admin']) ? $eszkoz['admin'] : $eszkoz['defadmin'] ?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=($eszkoz['pass']) ? $eszkoz['pass'] : $eszkoz['defpass'] ?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['megjegyzes']?></a></td>
                            <?php szerkSor($eszkoz['beepid'], $eszkoz['id'], "nyomtato"); ?>
                            <td class="dontprint"><a href="http://<?=$eszkoz['ipcim']?>" target="_blank"><img src='<?=$RootPath?>/images/webmanage.png' alt='Webes adminisztráció' title='Webes adminisztráció'/></a></td><?php
                        }
                    ?></tr><?php
                }
            }

            foreach($nembeepitett as $eszkoz)
            {            
                switch($eszkoz['maxmeret'])
                {
                    case 1: $maxmeret = "A4"; break;
                    case 2: $maxmeret = "A3"; break;
                    case 3: $maxmeret = "A2"; break;
                    case 4: $maxmeret = "A1"; break;
                    case 5: $maxmeret = "A0"; break;
                    default: $maxmeret = "A4";
                }
                switch($eszkoz['szines'])
                {
                    case 1: $szines = "Színes"; break;
                    default: $szines = "Fekete-Fehér";
                }
                switch($eszkoz['scanner'])
                {
                    case 1: $scanner = "Van"; break;
                    default: $scanner = "Nincs";
                }
                switch($eszkoz['fax'])
                {
                    case 1: $fax = "Van, beépített"; break;
                    case 2: $fax = "Alkalmas, modullal"; break;
                    default: $fax = "Nincs";
                }

                $kattinthatolink = './nyomtato/' . $eszkoz['id'];
                ?><tr class='trlink kiepitett<?=($eszkoz['hibas'] == 2) ? " mukodeskeptelen" : (($eszkoz['hibas'] == 1) ? " reszhibas" : "") ?>'>
                    <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['ipcim']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['beepitesinev']?></a></td>
                    <td nowrap><a href="<?=$kattinthatolink?>"><?=$eszkoz['gyarto']?></a></td>
                    <td nowrap><a href="<?=$kattinthatolink?>"><?=$eszkoz['modell']?><?=$eszkoz['varians']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$maxmeret?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$szines?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$scanner?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$fax?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['sorozatszam']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></a></td><?php
                    if($csoportir)
                    {
                        ?><td><a href="<?=$kattinthatolink?>"><?=($eszkoz['admin']) ? $eszkoz['admin'] : $eszkoz['defadmin'] ?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=($eszkoz['pass']) ? $eszkoz['pass'] : $eszkoz['defpass'] ?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['megjegyzes']?></a></td>
                        <?php szerkSor($eszkoz['beepid'], $eszkoz['id'], "nyomtato");?>
                        <td class="dontprint"></td><?php
                    }
                ?></tr><?php
            }
            ?></tbody>
        </table>
    </div><?php
}