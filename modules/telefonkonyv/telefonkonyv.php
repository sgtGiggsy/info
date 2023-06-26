<?php

$szamlalo = null;
$globaltelefonkonyvadmin = telefonKonyvAdminCheck($mindir);

$alegysegek = mySQLConnect("SELECT * FROM telefonkonyvcsoportok WHERE id > 1;");

$telefonkonyvmentett = mySQLConnect("SELECT telefonkonyvbeosztasok.id AS telszamid,
        telefonkonyvbeosztasok.nev AS beosztas,
        nevelotagok.nev AS elotag,
        telefonkonyvfelhasznalok.nev AS nev,
        titulusok.nev AS titulus,
        rendfokozatok.nev AS rendfokozat,
        belsoszam,
        belsoszam2,
        kozcelu,
        fax,
        kozcelufax,
        mobil,
        telefonkonyvcsoportok.nev AS csoport,
        felhasznalok.felhasznalonev AS felhasznalo,
        telefonkonyvbeosztasok.megjegyzes AS megjegyzes,
        telefonkonyvcsoportok.sorrend AS csoportsorrend,
        telefonkonyvbeosztasok.sorrend AS beosorrend
    FROM telefonkonyvbeosztasok
        LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok.felhid = telefonkonyvfelhasznalok.id
        LEFT JOIN nevelotagok ON telefonkonyvfelhasznalok.elotag = nevelotagok.id
        LEFT JOIN titulusok ON telefonkonyvfelhasznalok.titulus = titulusok.id
        LEFT JOIN rendfokozatok ON telefonkonyvfelhasznalok.rendfokozat = rendfokozatok.id
        LEFT JOIN felhasznalok ON telefonkonyvfelhasznalok.felhasznalo = felhasznalok.id
        LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
    ORDER BY telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok.sorrend;");

$valtozasok = mySQLConnect("SELECT telefonkonyvbeosztasok.id AS telszamid,
        telefonkonyvvaltozasok.beosztasnev AS beosztas,
        nevelotagok.nev AS elotag,
        telefonkonyvvaltozasok.nev AS nev,
        titulusok.nev AS titulus,
        rendfokozatok.nev AS rendfokozat,
        telefonkonyvvaltozasok.belsoszam AS belsoszam,
        telefonkonyvvaltozasok.belsoszam2 AS belsoszam2,
        telefonkonyvvaltozasok.kozcelu AS kozcelu,
        telefonkonyvvaltozasok.fax AS fax,
        telefonkonyvvaltozasok.kozcelufax AS kozcelufax,
        telefonkonyvvaltozasok.mobil AS mobil,
        telefonkonyvcsoportok.nev AS csoport,
        felhasznalok.felhasznalonev AS felhasznalo,
        telefonkonyvvaltozasok.megjegyzes AS megjegyzes,
        telefonkonyvcsoportok.sorrend AS csoportsorrend,
        telefonkonyvvaltozasok.sorrend AS beosorrend
    FROM telefonkonyvvaltozasok
        LEFT JOIN nevelotagok ON telefonkonyvvaltozasok.elotag = nevelotagok.id
        LEFT JOIN titulusok ON telefonkonyvvaltozasok.titulus = titulusok.id
        LEFT JOIN rendfokozatok ON telefonkonyvvaltozasok.rendfokozat = rendfokozatok.id
        LEFT JOIN telefonkonyvbeosztasok ON telefonkonyvvaltozasok.beosztas = telefonkonyvbeosztasok.id
        LEFT JOIN telefonkonyvcsoportok ON telefonkonyvvaltozasok.csoport = telefonkonyvcsoportok.id
        LEFT JOIN felhasznalok ON telefonkonyvvaltozasok.felhasznalo = felhasznalok.id
    WHERE (telefonkonyvvaltozasok.allapot > 1 AND telefonkonyvvaltozasok.allapot < 4)
        AND telefonkonyvvaltozasok.modid = (SELECT id FROM telefonkonyvmodositaskorok ORDER BY id DESC LIMIT 1)
    ORDER BY telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok.sorrend;");

$telefonkonyv = array();

$uj = false;
foreach($telefonkonyvmentett as $fixbejegyzes)
{
    foreach($valtozasok as $ujbejegyzes)
    {
        $uj = false;
        if($fixbejegyzes['telszamid'] == $ujbejegyzes['telszamid'])
        {
            $ujbejegyzes['uj'] = true;
            $telefonkonyv[] = $ujbejegyzes;
            $uj = true;
            break;
        }
    }
    if(!$uj)
    {
        $fixbejegyzes['uj'] = false;
        $telefonkonyv[] = $fixbejegyzes;
    }
}

foreach($valtozasok as $ujbejegyzes)
{
    if(!$ujbejegyzes['telszamid'])
    {
        $telefonkonyv[] = $ujbejegyzes;
    }
}

$volume  = array_column($telefonkonyv, 'csoportsorrend');
$edition = array_column($telefonkonyv, 'beosorrend');
array_multisort($volume, SORT_ASC, $edition, SORT_ASC, $telefonkonyv);

$oszlopok = array(
    array('nev' => '', 'tipus' => 's'),
    array('nev' => 'Beosztás', 'tipus' => 's'),
    array('nev' => 'Előtag', 'tipus' => 's'),
    array('nev' => 'Név', 'tipus' => 's'),
    array('nev' => 'Titulus', 'tipus' => 's'),
    array('nev' => 'Rendfokozat', 'tipus' => 's'),
    array('nev' => 'Belső szám', 'tipus' => 's'),
    array('nev' => 'Közcélú', 'tipus' => 's'),
    array('nev' => 'Fax', 'tipus' => 's'),
    array('nev' => 'Közcélú fax', 'tipus' => 's'),
    array('nev' => 'Szolgálati mobil', 'tipus' => 's'),
    array('nev' => 'Megjegyzés', 'tipus' => 's')
);
$oszlopszam = 0;
$tipus = "telefonkonyv";

?><datalist id="alegysegek"><?php
foreach($alegysegek as $alegyseg)
{
    ?><option><?=$alegyseg['nev']?></option><?php
}
?></datalist>


<div class="PrintArea">
    <div class="oldalcim">Telefonkönyv
        <div class="szuresvalaszto">Alegységre szűrés
            <input style="width: 40ch"
                    size="1"
                    type="search"
                    id="alegysegfilter"
                    list="alegysegek"
                    onkeyup="filterAlegyseg('alegysegfilter', '<?=$tipus?>')"
                    placeholder="Alegység"
                    title="Alegység">
        </div>
    </div>
    <table id="<?=$tipus?>" class="telefonkonyvtabla">
        <thead>
            <tr><?php
                foreach($oszlopok as $oszlop)
                {
                    if($oszlop['nev'])
                    {
                        ?><th class="tsorth"><p><span class="dontprint">
                            <input
                                size="1"
                                type="search"
                                id="f<?=$oszlopszam?>"
                                onkeyup="filterTable('f<?=$oszlopszam?>', '<?=$tipus?>', <?=$oszlopszam?>)"
                                placeholder="<?=$oszlop['nev']?>"
                                title="<?=$oszlop['nev']?>">
                            <br></span>
                            <span onclick="sortTable(<?=$oszlopszam?>, '<?=$oszlop['tipus']?>', '<?=$tipus?>')"><?=$oszlop['nev']?></span>
                            </p>
                        </th><?php
                    }
                    else
                    {
                        ?><th style="width:2ch"></th><?php
                    }
                    $oszlopszam++;
                }
            ?></tr>
        </thead>
        <tbody><?php
            $elozocsoport = 0;

            foreach($telefonkonyv as $telefonszam)
            {
                if($elozocsoport != $telefonszam['csoport'])
                {
                    $szamlalo = 0;
                    ?><tr id="<?=$telefonszam['csoport']?>-<?=$szamlalo?>">
                        <td colspan=<?=count($oszlopok)?> style="cursor:pointer" class="telefonkonyvelvalaszto" onclick="showHideAlegyseg('<?=$telefonszam['csoport']?>', '<?=$tipus?>')"><?=$telefonszam['csoport']?></td>
                    </tr><?php
                    $elozocsoport = $telefonszam['csoport'];
                    $szamlalo++;
                }
                $telszamid = $telefonszam['telszamid'];
                ?><tr <?=($csoportir) ? "class='kattinthatotr'" . "data-href='$RootPath/telefonszamvaltozas/$telszamid'" : "" ?> id="<?=$telefonszam['csoport']?>-<?=$szamlalo?>" style="font-weight: normal; <?=($telefonszam['uj'] && $globaltelefonkonyvadmin) ? 'font-style: italic;' : '' ?>">
                    <td></td>
                    <td><?=$telefonszam['beosztas']?></td>
                    <td style="width:4ch"><?=$telefonszam['elotag']?></td>
                    <td><?=$telefonszam['nev']?></td>
                    <td style="width:5ch"><?=$telefonszam['titulus']?></td>
                    <td style="width:8ch"><?=$telefonszam['rendfokozat']?></td>
                    <td><?=$telefonszam['belsoszam']?><?=($telefonszam['belsoszam2']) ? "<br>" . $telefonszam['belsoszam2'] : "" ?></td>
                    <td><?=$telefonszam['kozcelu']?></td>
                    <td><?=$telefonszam['fax']?></td>
                    <td><?=$telefonszam['kozcelufax']?></td>
                    <td><?=$telefonszam['mobil']?></td>
                    <td><?=$telefonszam['megjegyzes']?></td>
                </tr><?php
                $szamlalo++;
            }
        ?></tbody>
    </table>
</div>
