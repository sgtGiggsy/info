<?php
/*
Az oldal megtekintéséhez legalább csoportolvas jogosultságra van szükség,
hisz nincs értelme egyéni aktív eszközöknek.
*/

if(!$csoportolvas)
{
	getPermissionError();
}
else
{
    if(!$_SESSION[getenv('SESSION_NAME').'onlinefigyeles'])
    {
        $szuresek = getWhere("(modellek.tipus = 1 OR modellek.tipus = 2)");
        $onfigy = "";
        $onjoin = "";
    }
    else
    {
        $szuresek = getWhere("(modellek.tipus = 1 OR modellek.tipus = 2) AND (aktiveszkoz_allapot.id = (SELECT MAX(ac.id) FROM aktiveszkoz_allapot ac WHERE ac.eszkozid = aktiveszkoz_allapot.eszkozid) OR aktiveszkoz_allapot.id IS NULL)");
        $onfigy = "online, ";
        $onjoin = "LEFT JOIN aktiveszkoz_allapot ON eszkozok.id = aktiveszkoz_allapot.eszkozid ";
    }
    
    $where = $szuresek['where'];
    $csoportwhere = null;

    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => null,                        // A szűrés típusa, null = mindkettő, alakulat = alakulat, telephely = telephely
            'and' => true,                          // Kerüljön-e AND a parancs elejére
            'alakulatelo' => null,                  // A tábla neve, ahonnan az alakulat neve jön
            'telephelyelo' => null,                 // A tábla neve, ahonnan a telephely neve jön
            'alakulatnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az alakulatszűréshez
            'telephelynull' => true,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'alakulatmegnevezes' => "tulajdonos"    // Az alakulatot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
    }

    $mindeneszkoz = mySQLConnect("SELECT
            eszkozok.id AS id,
            sorozatszam,
            gyartok.nev AS gyarto,
            modellek.modell AS modell,
            varians,
            aktiveszkozok.web AS web,
            eszkoztipusok.nev AS tipus,
            epuletek.nev AS epuletnev,
            epuletek.szam AS epuletszam,
            helyisegszam,
            helyisegnev,
            beepitesideje,
            kiepitesideje,
            modellek.tipus AS tipusid,
            beepitesek.id AS beepid,
            alakulatok.rovid AS tulajdonos,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            ipcimek.ipcim AS ipcim,
            beepitesek.megjegyzes AS megjegyzes,
            eszkozok.megjegyzes AS emegjegyzes,
            $onfigy
            hibas,
            portszam,
            uplinkportok,
            szoftver
        FROM eszkozok
            INNER JOIN modellek ON eszkozok.modell = modellek.id
            INNER JOIN gyartok ON modellek.gyarto = gyartok.id
            INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
            INNER JOIN aktiveszkozok ON aktiveszkozok.eszkoz = eszkozok.id
            LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
            LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
            LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
            LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
            $onjoin
        WHERE $where $csoportwhere
        ORDER BY telephely, epuletek.szam + 1, helyisegszam, rack, pozicio, modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/aktiveszkoz?action=addnew'">Új aktív eszköz</button><?php
    }

    $tipus = 'eszkozok';
    $oszlopok = array(
        array('nev' => 'IP cím', 'tipus' => 's'),
        array('nev' => 'Eszköznév', 'tipus' => 's'),
        array('nev' => 'Gyártó', 'tipus' => 's'),
        array('nev' => 'Modell', 'tipus' => 's'),
        array('nev' => 'Portok', 'tipus' => 's'),
        array('nev' => 'Sorozatszám', 'tipus' => 's'),
        array('nev' => 'Eszköztípus', 'tipus' => 's'),
        array('nev' => 'Szoftver', 'tipus' => 's'),
        array('nev' => 'Épület', 'tipus' => 's'),
        array('nev' => 'Helyiség', 'tipus' => 's'),
        array('nev' => 'Rack', 'tipus' => 's'),
        array('nev' => 'Megjegyzés', 'tipus' => 's')
    );
    if($csoportir)
    {
        $oszlopok[] = array('nev' => 'Megjegyzés', 'tipus' => 's');
    }

    ?><div class="PrintArea">
        <div class="oldalcim">Aktív eszközök <?=$szuresek['szures']?> <?=keszletFilter($_GET['page'], $szuresek['filter'])?></div>
        <table id="<?=$tipus?>">
            <thead>
                <tr><?php
                    sortTableHeader($oszlopok, $tipus, true);
                    if($csoportir)
                    {
                        ?><th class="dontprint"></th>
                        <th class="dontprint"></th>
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
                        $kattinthatolink = './aktiveszkoz/' . $eszkoz['id'];
                        ?><tr class='trlink<?=($eszkoz['hibas'] == 1) ? " reszhibas" : "" ?><?=($_SESSION[getenv('SESSION_NAME').'onlinefigyeles'] && $eszkoz['online'] == 0 && $eszkoz['online'] != null && $szemelyes['switchstateshow'] == 1) ? " offline" : "" ?>'>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['ipcim']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['beepitesinev']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['gyarto']?></a></td>
                            <td nowrap><a href="<?=$kattinthatolink?>"><?=$eszkoz['modell']?><?=$eszkoz['varians']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['portszam']?><?=($eszkoz['uplinkportok']) ? ' + ' . $eszkoz['uplinkportok'] : "" ?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['sorozatszam']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['tipus']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['szoftver']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['rack']?></a></td><?php
                            if($csoportir)
                            {
                                ?><td><a href="<?=$kattinthatolink?>"><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></a></td><?php
                                szerkSor($eszkoz['beepid'], $eszkoz['id'], "aktiveszkoz"); ?>
                                <td class="dontprint"><a href="telnet://<?=$eszkoz['ipcim']?>"><img src='<?=$RootPath?>/images/ssh.png' alt='Eszköz adminisztrálása' title='Eszköz adminisztrálása'/></a></td>
                                <td class="dontprint"><?php
                                if($eszkoz['web'])
                                {
                                    ?><a href="http://<?=$eszkoz['ipcim']?>" target="_blank"><img src='<?=$RootPath?>/images/webmanage.png' alt='Webes adminisztráció' title='Webes adminisztráció'/></a><?php
                                }
                                ?></td><?php
                            }
                        ?></tr><?php
                    }
                }
                foreach($nembeepitett as $eszkoz)
                {
                    $kattinthatolink = './aktiveszkoz/' . $eszkoz['id'];
                    ?><tr class='trlink kiepitett<?=($eszkoz['hibas'] == 2) ? " mukodeskeptelen" : (($eszkoz['hibas'] == 1) ? " reszhibas" : "") ?>'>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['ipcim']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['beepitesinev']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['gyarto']?></a></td>
                        <td nowrap><a href="<?=$kattinthatolink?>"><?=$eszkoz['modell']?><?=$eszkoz['varians']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['portszam']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['sorozatszam']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['tipus']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['szoftver']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['rack']?></a></td><?php
                        if($csoportir)
                        {
                            ?><td><a href="<?=$kattinthatolink?>"><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></a></td><?php
                            szerkSor($eszkoz['beepid'], $eszkoz['id'], "aktiveszkoz");
                            ?><td class="dontprint"></td>
                            <td class="dontprint"></td><?php
                        }
                    ?></tr><?php
                }
            ?></tbody>
        </table>
    </div><?php
}