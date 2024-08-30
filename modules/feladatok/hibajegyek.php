<?php
if(!$_SESSION[getenv('SESSION_NAME').'id'])
{
    getPermissionError();
}
else
{
    // Mivel ehhez a menüponthoz mindenki hozzáfér legalább saját jogosultsággal a legegegyszerűbb
    // itt jogosultságot adni nekik. Olyanokra, akik magasabb jogosultsággal rendelkeznek
    // ez nincs kihatással
    $sajatolvas = $sajatir = true;

    // Először kiválasztjuk a megjelenítendő hibajegyek listáját.
    // Plusz jogosultság nélkül mindenki csak a sajátját látja.
    $where = "WHERE feladattipus = 1 AND (feladatallapotok.id = (SELECT MAX(hjstate.id) FROM feladatallapotok hjstate WHERE hjstate.feladat = feladatallapotok.feladat) OR feladatallapotok.id IS NULL)";
    if($mindolvas)
    {}
    elseif($csoportolvas)
    {
        $where .= " AND (felhasznalok.szervezet = $szervezet";
    }
    elseif($sajatolvas)
    {
        $where .= " AND (feladatok.felhasznalo = $felhasznaloid";
    }

    $csoportwhere = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => "szervezet",                 // A szűrés típusa, null = mindkettő, szervezet = szervezet, telephely = telephely
            'and' => false,                          // Kerüljön-e AND a parancs elejére
            'szervezetelo' => "felhasznalok",                  // A tábla neve, ahonnan az szervezet neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'szervezetnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az szervezetszűréshez
            'telephelynull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'szervezetmegnevezes' => "szervezet"    // Az szervezetot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = "OR " . csoportWhere($csoporttagsagok, $csopwhereset) . ")";
    }

    if(!$csoportir)
    {
        $csoportwhere = ")";
    }

    $hibajegyek = mySQLConnect("SELECT feladatok.id AS hibid, pubid,
            felhasznalok.szervezet AS szervezet, szervezetek.rovid AS szervezetnev,
            felhasznalok.nev AS bejelento, feladatok.allapot AS allapot,
            feladatok.rovid AS rovid, feladatok.timestamp AS bejelentesideje, eszkozneve,
            epuletek.nev AS epuletnev, epuletek.szam AS epuletszam,
            helyisegnev, helyisegszam,
            allapottipusok.folyamat AS allapottipus, modositok.nev AS modosito,
            szakok.nev AS tipus, feladatallapotok.megjegyzes AS megjegyzes, feladatallapotok.timestamp AS timestamp, feladatok.prioritas AS prioritas,
            (SELECT count(id) FROM feladatfajlok WHERE feladat = hibid) AS csatolmanyok
        FROM feladatok
            INNER JOIN felhasznalok ON feladatok.felhasznalo = felhasznalok.id
            LEFT JOIN feladatallapotok ON feladatallapotok.feladat = feladatok.id
            LEFT JOIN allapottipusok ON feladatallapotok.allapottipus = allapottipusok.id
            LEFT JOIN szervezetek ON felhasznalok.szervezet = szervezetek.id
            LEFT JOIN felhasznalok modositok ON feladatallapotok.felhasznalo = modositok.id
            LEFT JOIN helyisegek ON feladatok.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON feladatok.epulet = epuletek.id
            LEFT JOIN szakok ON feladatok.szakid = szakok.id
        $where $csoportwhere
        ORDER BY feladatok.timestamp DESC");

    if($sajatir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/hibajegy?action=addnew'">Hiba bejelentése</button><?php
    }
    $javascriptfiles[] = "modules/feladatok/includes/hibajegy.js";
    $tipus = "hibajegyek";
    $oszlopok = array(
        array('nev' => 'ID', 'tipus' => 'i'),
        array('nev' => 'Bejelentő', 'tipus' => 's'),
        array('nev' => 'Szervezet', 'tipus' => 's'),
        array('nev' => 'Bejelentve', 'tipus' => 's'),
        array('nev' => 'Eszköz', 'tipus' => 's'),
        array('nev' => 'Rövid leírás', 'tipus' => 's'),
        array('nev' => 'Típus', 'tipus' => 's'),
        array('nev' => 'Állapot', 'tipus' => 's'),
        array('nev' => 'Csatolmány', 'tipus' => 's'),
        array('nev' => 'Épület', 'tipus' => 's'),
        array('nev' => 'Helyiség', 'tipus' => 's'),
        array('nev' => 'Utolsó módosítás', 'tipus' => 's'),
        array('nev' => 'Legutóbbi megjegyzés', 'tipus' => 's'),
        array('nev' => 'Módosítás ideje', 'tipus' => 's')
    );

    ?><div class='oldalcim'>Bejelentett hibák listája</div>
    <table id="<?=$tipus?>">
        <thead>
            <tr><?php
                if($csoportir)
                {
                    ?><th class="prioritas"></th><?php
                }
                sortTableHeader($oszlopok, $tipus, true)
            ?></tr>
        </thead>
        <tbody><?php
            foreach($hibajegyek as $hibajegy)
            {
                if($csoportir)
                {
                    switch($hibajegy['prioritas'])
                    {
                        case 1: $szint = "alacsony"; break;
                        case 2: $szint = "halaszthato"; break;
                        case 3: $szint = "fontos"; break;
                        case 4: $szint = "surgos"; break;
                        case 5: $szint = "kritikus"; break;
                        default: $szint = "";
                    }
                }

                if($hibajegy['allapot'] == 1)
                {
                    if($hibajegy['allapottipus'])
                    {
                        $allapot = $hibajegy['allapottipus'];
                    }
                    else
                    {
                        $allapot = "Nem látott";
                    }
                }
                else
                {
                    $allapot = "Lezárva";
                    $szint = "kesz";
                }

                $hibid = $hibajegy['pubid'];
                $kattinthatolink = './hibajegy/' . $hibid;

                ?><tr class='trlink'><?php
                    if($csoportir)
                    {
                        ?><td class="prioritas <?=$szint?>"><a href="<?=$kattinthatolink?>"></a></td><?php
                    }
                    ?><td><a href="<?=$kattinthatolink?>"><?=$hibid?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$hibajegy['bejelento']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$hibajegy['szervezetnev']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$hibajegy['bejelentesideje']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$hibajegy['eszkozneve']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$hibajegy['rovid']?></a></td>
                    <td style="text-transform: capitalize"><a href="<?=$kattinthatolink?>"><?=$hibajegy['tipus']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$allapot?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=($hibajegy['csatolmanyok'] > 0) ? "Van" : "Nincs" ?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=($hibajegy['epuletszam']) ? $hibajegy['epuletszam'] : "" ?> <?=($hibajegy['epuletnev']) ? "(" . $hibajegy['epuletnev'] . ")" : "" ?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=($hibajegy['helyisegszam']) ? $hibajegy['helyisegszam'] : "" ?> <?=($hibajegy['helyisegnev']) ? "(" . $hibajegy['helyisegnev'] . ")" : "" ?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$hibajegy['modosito']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$hibajegy['megjegyzes']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$hibajegy['timestamp']?></a></td>
                </tr><?php
            }
        ?></tbody>
    </table><?php
}