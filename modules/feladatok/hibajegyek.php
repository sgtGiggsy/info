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
        $where .= " AND felhasznalok.alakulat = $alakulat";
    }
    elseif($sajatolvas)
    {
        $where .= " AND feladatok.felhasznalo = $felhasznaloid";
    }

    $hibajegyek = mySQLConnect("SELECT feladatok.id AS hibid, pubid,
            felhasznalok.alakulat AS alakulat, alakulatok.rovid AS alakulatnev,
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
            LEFT JOIN alakulatok ON felhasznalok.alakulat = alakulatok.id
            LEFT JOIN felhasznalok modositok ON feladatallapotok.felhasznalo = modositok.id
            LEFT JOIN helyisegek ON feladatok.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON feladatok.epulet = epuletek.id
            LEFT JOIN szakok ON feladatok.szakid = szakok.id
        $where
        ORDER BY feladatok.timestamp DESC");

    if($sajatir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/hibajegy?action=addnew'">Hiba bejelentése</button><?php
    }
    $tipus = "hibajegyek";
    $oszlopok = array(
        array('nev' => 'ID', 'tipus' => 'i'),
        array('nev' => 'Bejelentő', 'tipus' => 's'),
        array('nev' => 'Alakulat', 'tipus' => 's'),
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
                if($mindir)
                {
                    ?><th class="prioritas"></th><?php
                    $i = 1;
                }
                else
                {
                    $i = 0;
                }
                
                foreach($oszlopok as $oszlop)
                {
                    ?><th class="tsorth"><p><span class="dontprint"><input size="1" type="text" id="f<?=$i?>" onkeyup="filterTable('f<?=$i?>', '<?=$tipus?>', <?=$i?>)" placeholder="<?=$oszlop['nev']?>" title="<?=$oszlop['nev']?>"><br></span><span onclick="sortTable(<?=$i?>, '<?=$oszlop['tipus']?>', '<?=$tipus?>')"><?=$oszlop['nev']?></span></p></th><?php
                    $i++;
                }
            ?></tr>
        </thead>
        <tbody><?php
            foreach($hibajegyek as $hibajegy)
            {
                if($mindir)
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

                $hibid = $hibajegy['pubid']

                ?><tr class='kattinthatotr' data-href='./hibajegy/<?=$hibid?>'><?php
                    if($mindir)
                    {
                        ?><td class="prioritas <?=$szint?>"></td><?php
                    }
                    ?><td><?=$hibid?></td>
                    <td><?=$hibajegy['bejelento']?></td>
                    <td><?=$hibajegy['alakulatnev']?></td>
                    <td><?=$hibajegy['bejelentesideje']?></td>
                    <td><?=$hibajegy['eszkozneve']?></td>
                    <td><?=$hibajegy['rovid']?></td>
                    <td style="text-transform: capitalize"><?=$hibajegy['tipus']?></td>
                    <td><?=$allapot?></td>
                    <td><?=($hibajegy['csatolmanyok'] > 0) ? "Van" : "Nincs" ?></td>
                    <td><?=($hibajegy['epuletszam']) ? $hibajegy['epuletszam'] : "" ?> <?=($hibajegy['epuletnev']) ? "(" . $hibajegy['epuletnev'] . ")" : "" ?></td>
                    <td><?=($hibajegy['helyisegszam']) ? $hibajegy['helyisegszam'] : "" ?> <?=($hibajegy['helyisegnev']) ? "(" . $hibajegy['helyisegnev'] . ")" : "" ?></td>
                    <td><?=$hibajegy['modosito']?></td>
                    <td><?=$hibajegy['megjegyzes']?></td>
                    <td><?=$hibajegy['timestamp']?></td>
                </tr><?php
            }
        ?></tbody>
    </table><?php
}