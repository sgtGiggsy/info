<?php

if(!$contextmenujogok['firmwarelista'])
{
	getPermissionError();
}
else
{
    $javascriptfiles[] = "modules/eszkozok/includes/firmwarelista.js";
    $firmwarelista = new MySQLHandler("SELECT firmwarelist.id AS id,
            firmwarelist.nev AS nev,
            kiadasideje,
            gyartok.nev AS gyarto,
            modellek.modell AS modell,
            modellek.id AS modellid,
            vegsoverzio
        FROM firmwarelist
            LEFT JOIN modellek ON firmwarelist.eszkoztipus = modellek.id
            LEFT JOIN gyartok ON modellek.gyarto = gyartok.id
        ORDER BY gyartok.nev ASC, modellek.modell ASC, firmwarelist.kiadasideje DESC;");

    $eszkozlistasql = new MySQLHandler("SELECT ipcimek.ipcim AS ipcim,
            beepitesek.nev AS beepitesnev,
            aktiveszkozok.szoftver AS szoftver,
            sohoeszkozok.szoftver AS sohoszoftver,
            eszkozok.sorozatszam,
            eszkozok.modell AS modell,
            modellek.modell AS modellnev,
            gyartok.nev AS gyarto,
            eszkozok.varians AS varians,
            eszkozok.leadva AS leadva,
            eszkozok.hibas AS hibas
        FROM eszkozok
            LEFT JOIN aktiveszkozok ON aktiveszkozok.eszkoz = eszkozok.id
            LEFT JOIN sohoeszkozok ON sohoeszkozok.eszkoz = eszkozok.id
            LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
            LEFT JOIN modellek ON eszkozok.modell = modellek.id
            LEFT JOIN gyartok ON modellek.gyarto = gyartok.id
        WHERE modellek.tipus < 11 AND eszkozok.leadva IS NULL
        ORDER BY beepitesek.beepitesideje DESC");

    $elozomodell = 0;
    $sorozatszamok = array();
    $eszkozlista = $eszkozlistasql->Result();
    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/eszkozalap/firmware'">Új firmware felvétele</button><?php
    }

    $tipus = "firmwarelista";

    $oszlopok = array(
        array('nev' => 'Modell', 'tipus' => 's'),
        array('nev' => 'Firmware', 'tipus' => 's'),
        array('nev' => 'Kiadás ideje', 'tipus' => 's'),
        array('nev' => 'Végső verzió', 'tipus' => 's')
    );

    ?><div class="oldalcim">Firmware-ek listája
        <div class="szuresvalaszto">
            <select id="szures" onchange="firmwareSzur()">
                <option value="mindmutat">Minden eszköz mutatása</option>
                <option value="legfrissebbrejt">Csak a frissítendő eszközök mutatása</option>
            </select>
        </div>
    </div>
    <table id="<?$tipus?>">
    <thead>
        <tr>
            <?php sortTableHeader($oszlopok, $tipus, true) ?>
        </tr>
    </thead>
    <tbody><?php
    
    foreach($firmwarelista->Result() as $firmware)
    {
        $firmwareid = $firmware['id'];
        $kattinthatolink = "$RootPath/eszkozalap/firmware/" . $firmware['id'];
        $adatoszlopok = array();
        if($elozomodell != $firmware['modell'])
        {
            $adatoszlopok[] = $firmware['gyarto'] . " " . $firmware['modell'];
        }
        else
        {
            $adatoszlopok[] = "";
        }
        $adatoszlopok[] = $firmware['nev'];
        $adatoszlopok[] = $firmware['kiadasideje'];
        if($firmware['vegsoverzio'])
        {
            $adatoszlopok[] = "Igen";
        }
        else
        {
            $adatoszlopok[] = "";
        }
        
        ?><tr style="font-size: 1.2em;" class="trlink"><?php
            $elso = true;
            foreach($adatoszlopok as $adatoszlop)
            {
                ?><td <?=($elso) ? "style='font-size: 1.3em;'" : "" ?>>
                    <?=($mindir) ? "<a href='" . $kattinthatolink . "'>" : "" ?>
                        <?=$adatoszlop?>
                    <?=($mindir) ? "</a>" : "" ?>
                </td><?php
                $elso = false;
            }
        $volteszkoz = false;
        $tempeszklist = array();
        foreach($eszkozlista as $eszkoz)
        {
            if(!$eszkoz['leadva'] && $eszkoz['hibas'] < 2)
            {
                if($firmware['modellid'] == $eszkoz['modell'] && ($eszkoz['szoftver'] == $firmware['nev'] || $eszkoz['sohoszoftver'] == $firmware['nev']))
                {
                    if(!$volteszkoz)
                    {
                        $volteszkoz = true;
                        ?><tr><td></td><td colspan="3"><table style='margin-left: 2em; width: 25vw'><?php
                    }

                    if(!in_array($eszkoz['sorozatszam'], $sorozatszamok))
                    {
                        $sorozatszamok[] = $eszkoz['sorozatszam'];
                        ?><tr <?=($elozomodell != $firmware['modell'] || $firmware['vegsoverzio']) ? 'class="legfrissebb"' : '' ?>>
                            <td style="width: 20%"><?=$eszkoz['ipcim']?></td>
                            <td style="width: 60%"><?=$eszkoz['beepitesnev']?></td>
                            <td><?=$eszkoz['sorozatszam']?></td>
                        </tr><?php
                    }
                }
                else
                {
                    $tempeszklist[] = $eszkoz;
                }
            }
        }
        if($volteszkoz)
        {
            ?></td></tr></table><?php
        }
        $eszkozlista = $tempeszklist;
        $elozomodell = $firmware['modell'];
    }
    ?></tbody>
    </table>
    
    <div class="oldalcim">Eszközök be nem azonosított firmware-rel</div>
    <table>
        <thead>
            <tr>
                <th>IP cím</th>
                <th>Típus</th>
                <th>Beépítési név</th>
                <th>Sorozatszám</th>
                <th>Adatlapon szereplő firmware</th>
            </tr>
        </thead>
        <tbody><?php
            foreach($eszkozlista as $ismeretlen)
            {
                ?><tr>
                    <td><?=$ismeretlen['ipcim']?></td>
                    <td><?=$ismeretlen['gyarto']?> <?=$ismeretlen['modellnev']?><?=$ismeretlen['varians']?></td>
                    <td><?=$ismeretlen['beepitesnev']?></td>
                    <td><?=$ismeretlen['sorozatszam']?></td>
                    <td><?=$ismeretlen['szoftver']?><?=$ismeretlen['sohoszoftver']?></td>
                </tr><?php
            }
        ?></tbody>
    </table><?php
}