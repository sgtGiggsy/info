<?php

$szamlalo = null;
$csaksajat = false;
$csoportfilter = "alegysegfilter";
if(!isset($_GET['nevszerinti']))
{
    $nevszerint = false;
    $orderby = "telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok.sorrend";
    $orderbymod = "telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok_mod.sorrend";
}
else
{
    $nevszerint = true;
    $orderby = $orderbymod = "telefonkonyvfelhasznalok.nev";
}

$globaltelefonkonyvadmin = telefonKonyvAdminCheck($mindir);
$javascriptfiles[] = "includes/js/csoportFilter.js";
if(!$globaltelefonkonyvadmin && isset($felhasznaloid))
    $csoportjogok = telefonKonyvCsoportjogok();

$where = "WHERE telefonkonyvbeosztasok.allapot > 1";
$where2 = "WHERE telefonkonyvvaltozasok.allapot > 1 AND  telefonkonyvvaltozasok.allapot < 4";
if(isset($_GET['kereses']))
{
    $keres = $_GET['kereses'];
    $szurt = " AND telefonkonyvfelhasznalok.nev LIKE '%$keres%' OR telefonkonyvcsoportok.nev LIKE '%$keres%' OR belsoszam LIKE '%$keres%' OR belsoszam2 LIKE '%$keres%' OR mobil LIKE '%$keres%'";
    $where .= $szurt;
    $where2 .= $szurt;
}
if(isset($_GET['csaksajat']))
{
    $csaksajat = true;
}
if($nevszerint)
{
    $where .= " AND telefonkonyvfelhasznalok.nev IS NOT NULL";
    $where2 .= " AND telefonkonyvfelhasznalok.nev IS NOT NULL";;
}

$alegysegek = mySQLConnect("SELECT * FROM telefonkonyvcsoportok WHERE id > 1;");

$telefonkonyvorig = mySQLConnect("SELECT NULL AS modid,
        telefonkonyvbeosztasok.id AS telszamid,
        telefonkonyvcsoportok.nev AS csoport,
        telefonkonyvbeosztasok.nev AS beosztas,
        telefonkonyvbeosztasok.allapot AS allapot,
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
        felhasznalok.felhasznalonev AS felhasznalo,
        telefonkonyvbeosztasok.megjegyzes AS megjegyzes,
        telefonkonyvcsoportok.sorrend AS csoportsorrend,
        telefonkonyvbeosztasok.sorrend AS beosorrend,
        telefonkonyvcsoportok.id AS csopid
    FROM telefonkonyvbeosztasok
        LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok.felhid = telefonkonyvfelhasznalok.id
        LEFT JOIN nevelotagok ON telefonkonyvfelhasznalok.elotag = nevelotagok.id
        LEFT JOIN titulusok ON telefonkonyvfelhasznalok.titulus = titulusok.id
        LEFT JOIN rendfokozatok ON telefonkonyvfelhasznalok.rendfokozat = rendfokozatok.id
        LEFT JOIN felhasznalok ON telefonkonyvfelhasznalok.felhasznalo = felhasznalok.id
        LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
        LEFT JOIN telefonkonyvvaltozasok ON telefonkonyvbeosztasok.id = telefonkonyvvaltozasok.ujbeoid
    $where
    ORDER BY $orderby;");

$telefonkonyvuj = mySQLConnect("SELECT telefonkonyvvaltozasok.id AS modid,
        telefonkonyvvaltozasok.origbeoid AS telszamid,
        telefonkonyvcsoportok.nev AS csoport,
        telefonkonyvbeosztasok_mod.nev AS beosztas,
        telefonkonyvbeosztasok_mod.allapot AS allapot,
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
        felhasznalok.felhasznalonev AS felhasznalo,
        telefonkonyvbeosztasok_mod.megjegyzes AS megjegyzes,
        telefonkonyvcsoportok.sorrend AS csoportsorrend,
        telefonkonyvbeosztasok_mod.sorrend AS beosorrend,
        telefonkonyvcsoportok.id AS csopid
    FROM telefonkonyvbeosztasok_mod
        LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok_mod.felhid = telefonkonyvfelhasznalok.id
        LEFT JOIN nevelotagok ON telefonkonyvfelhasznalok.elotag = nevelotagok.id
        LEFT JOIN titulusok ON telefonkonyvfelhasznalok.titulus = titulusok.id
        LEFT JOIN rendfokozatok ON telefonkonyvfelhasznalok.rendfokozat = rendfokozatok.id
        LEFT JOIN felhasznalok ON telefonkonyvfelhasznalok.felhasznalo = felhasznalok.id
        LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok_mod.csoport = telefonkonyvcsoportok.id
        LEFT JOIN telefonkonyvvaltozasok ON telefonkonyvbeosztasok_mod.id = telefonkonyvvaltozasok.ujbeoid
    $where2
    ORDER BY $orderbymod;");

$telefonkonyv = array();

$uj = false;
foreach($telefonkonyvorig as $fixbejegyzes)
{
    foreach($telefonkonyvuj as $ujbejegyzes)
    {
        $uj = false;
        if($fixbejegyzes['telszamid'] == $ujbejegyzes['telszamid'])
        {
            //echo $ujbejegyzes['nev'] . "<br>";
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

foreach($telefonkonyvuj as $ujbejegyzes)
{
    if(!$ujbejegyzes['telszamid'])
    {
        $ujbejegyzes['uj'] = true;
        $telefonkonyv[] = $ujbejegyzes;
    }
}

if(!$nevszerint)
{
    $csoportsorrend  = array_column($telefonkonyv, 'csoportsorrend');
    $beosorrend = array_column($telefonkonyv, 'beosorrend');
    array_multisort($csoportsorrend, SORT_ASC, $beosorrend, SORT_ASC, $telefonkonyv);

    $oszlopok = array(
        array('nev' => '', 'tipus' => 's', 'adatmezo' => 'csoport'),
        array('nev' => 'Beosztás', 'tipus' => 's', 'adatmezo' => 'beosztas'),
        array('nev' => 'Előtag', 'tipus' => 's', 'adatmezo' => 'elotag'),
        array('nev' => 'Név', 'tipus' => 's', 'adatmezo' => 'nev'),
        array('nev' => 'Titulus', 'tipus' => 's', 'adatmezo' => 'titulus'),
        array('nev' => 'Rendfokozat', 'tipus' => 's', 'adatmezo' => 'rendfokozat'),
        array('nev' => 'Belső szám', 'tipus' => 's', 'adatmezo' => 'belsoszam'),
        array('nev' => 'Közcélú', 'tipus' => 's', 'adatmezo' => 'kozcelu'),
        array('nev' => 'Fax', 'tipus' => 's', 'adatmezo' => 'fax'),
        array('nev' => 'Közcélú fax', 'tipus' => 's', 'adatmezo' => 'kozcelufax'),
        array('nev' => 'Szolgálati mobil', 'tipus' => 's', 'adatmezo' => 'mobil'),
        array('nev' => 'Megjegyzés', 'tipus' => 's', 'adatmezo' => 'megjegyzes')
    );
}
else
{
    $oszlopok = array(
        array('nev' => 'Előtag', 'tipus' => 's', 'adatmezo' => 'elotag'),
        array('nev' => 'Név', 'tipus' => 's', 'adatmezo' => 'nev'),
        array('nev' => 'Titulus', 'tipus' => 's', 'adatmezo' => 'titulus'),
        array('nev' => 'Rendfokozat', 'tipus' => 's', 'adatmezo' => 'rendfokozat'),
        array('nev' => 'Beosztás', 'tipus' => 's', 'adatmezo' => 'beosztas'),
        array('nev' => 'Belső szám', 'tipus' => 's', 'adatmezo' => 'belsoszam'),
        array('nev' => 'Közcélú', 'tipus' => 's', 'adatmezo' => 'kozcelu'),
        array('nev' => 'Szolgálati mobil', 'tipus' => 's', 'adatmezo' => 'mobil'),
        array('nev' => 'Alegység', 'tipus' => 's', 'adatmezo' => 'alegyseg')
    );
}

if(isset($_GET['action']) && $_GET['action'] == "exportexcel")
{
    include("./modules/telefonkonyv/includes/telefonkonyvexport.php");
}

$oszlopszam = 0;
$tipus = "telefonkonyv";
$enablekeres = true;

?><datalist id="alegysegek"><?php
foreach($alegysegek as $alegyseg)
{
    ?><option><?=$alegyseg['nev']?></option><?php
}
?></datalist><?php
if(isset($_GET['kereses']))
{
    ?><h2>Eredmények a keresőkifejezésre: <span style="font-size: 1.5em"><?=$_GET['kereses']?></span></h2><br><br><?php
}

?><div class="szerkgombsor">
    <button type="button" onclick="location.href='<?=$RootPath?>/telefonkonyv?action=exportexcel'">Telefonkönyv mentése Excel-be</button><?php
    if($globaltelefonkonyvadmin || (isset($csoportjogok) && count($csoportjogok) > 0))
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/telefonszamvaltozas?action=addnew'">Új felhasználó felvétele új beosztásra</button><?php
    }
    if(!$globaltelefonkonyvadmin && (isset($csoportjogok) && count($csoportjogok) > 0))
    {
        if(isset($_GET['csaksajat']))
        {
            ?><button type="button" onclick="location.href='<?=$RootPath?>/telefonkonyv'">A teljes telefonkönyv mutatása</button><?php
        }
        else
        {
            ?><button type="button" onclick="location.href='<?=$RootPath?>/telefonkonyv?csaksajat'">Csak a saját kezelésű alegységek mutatása</button><?php
        }
    }
?></div>
<div class="PrintArea">
    <div class="oldalcim nomargbottom">Telefonkönyv
        <div class="szuresvalaszto">Alegységre szűrés
            <input style="width: 40ch"
                    size="1"
                    type="search"
                    id="<?=$csoportfilter?>"
                    list="alegysegek"
                    onkeyup="filterCsoport('<?=$csoportfilter?>', '<?=$tipus?>')"
                    placeholder="Alegység"
                    title="Alegység">
        </div>
    </div>
    <div class="szerkcardoptions">
        <div class="szerkcardoptionelement" id="alegyegenkent"><span onclick="location.href='./telefonkonyv'">Alegységekre bontott</span></div>
        <div class="szerkcardoptionelement" id="nevszerint"><span onclick="location.href='?nevszerinti'">Név szerinti</span></div>
    </div>
    <table id="<?=$tipus?>" class="telefonkonyvtabla">
        <thead>
            <tr><?php
            if(!$nevszerint)
            {
                sortTableHeader($oszlopok, $tipus, true, false);
            }
            else
            {
                sortTableHeader($oszlopok, $tipus, true, false, false);
            }
            ?></tr>
        </thead>
        <tbody><?php
            $elozocsoport = 0;
            $csoportszamlalo = 0;

            foreach($telefonkonyv as $telefonszam)
            {
                if($elozocsoport != $telefonszam['csoport'])
                {
                    $szamlalo = 0;
                    $csoportnevalap = "csoport" . $csoportszamlalo . "-";
                    $csoportnev = $csoportnevalap . $szamlalo;
                    $elozocsoport = $telefonszam['csoport'];
                    if(!$nevszerint)
                    {
                        ?><tr id="<?=$csoportnev?>" class="<?=$elozocsoport?>" style="<?=($csaksajat && !(isset($csoportjogok) && in_array($telefonszam['csopid'], $csoportjogok))) ? 'display: none;' :  '' ?>">
                            <td colspan=<?=count($oszlopok)?> style="cursor:pointer;" class="telefonkonyvelvalaszto" onclick="showHideCsoport('<?=$csoportnevalap?>', '<?=$tipus?>')"><?=$telefonszam['csoport']?></td>
                        </tr><?php
                    }
                    
                    $csoportszamlalo++;
                    $szamlalo++;
                }
                $telszamid = $telefonszam['telszamid'];
                $csoportnev = $csoportnevalap . $szamlalo;
                $kattinthatolink = './telefonszamvaltozas';
                $szerklink = $szerklinkzar = "";

                if($telefonszam['allapot'] == 4)
                {
                    $kattinthatolink .= "?modid=" . $telefonszam['modid'];
                }
                else
                {
                    $kattinthatolink .= "/" . $telszamid;
                }
                if($csoportir)
                {
                    $szerklink = "<a href='$kattinthatolink'>";
                    $szerklinkr = "<a href='$kattinthatolink' style='justify-content: right;'>";
                    $szerklinkzar = "</a>";
                }

                ?><tr class="trlink"
                        id="<?=$csoportnev?>"
                        style="<?=($telefonszam['allapot'] == 4 && $globaltelefonkonyvadmin) ? 'font-style: italic; font-weight: normal;' : ((!$globaltelefonkonyvadmin && isset($csoportjogok) && in_array($telefonszam['csopid'], $csoportjogok)) ? 'font-style: italic; ' : 'font-weight: normal; ' )?>
                        <?=($csaksajat && !(isset($csoportjogok) && in_array($telefonszam['csopid'], $csoportjogok))) ? 'display: none;' :  '' ?>"
                    ><?php
                    if(!$nevszerint)
                    {
                        ?><td><?=$szerklink?><?=$szerklinkzar?></td>
                        <td><?=$szerklink?><?=$telefonszam['beosztas']?><?=$szerklinkzar?></td><?php
                    }
                    ?><td class="linkjobbra" style="width:4ch;"><?=$szerklink?><?=$telefonszam['elotag']?><?=$szerklinkzar?></td>
                    <td><?=$szerklink?><?=$telefonszam['nev']?><?=$szerklinkzar?></td>
                    <td class="linkjobbra" style="width:5ch;"><?=$szerklink?><?=$telefonszam['titulus']?><?=$szerklinkzar?></td>
                    <td style="width:8ch"><?=$szerklink?><?=$telefonszam['rendfokozat']?><?=$szerklinkzar?></td><?php
                    if($nevszerint)
                    {
                        ?><td><?=$szerklink?><?=$telefonszam['beosztas']?><?=$szerklinkzar?></td><?php
                    }
                    ?><td nowrap><?=$szerklink?><?=$telefonszam['belsoszam']?><?=($telefonszam['belsoszam2']) ? "<br>" . $telefonszam['belsoszam2'] : "" ?><?=$szerklinkzar?></td>
                    <td><?=$szerklink?><?=$telefonszam['kozcelu']?><?=$szerklinkzar?></td><?php
                    if(!$nevszerint)
                    {
                        ?><td nowrap><?=$szerklink?><?=$telefonszam['fax']?><?=$szerklinkzar?></td>
                        <td><?=$szerklink?><?=$telefonszam['kozcelufax']?><?=$szerklinkzar?></td><?php
                    }
                    ?><td><?=$szerklink?><?=$telefonszam['mobil']?><?=$szerklinkzar?></td>
                    <td><?=$szerklink?><?=($nevszerint) ? $telefonszam['csoport'] : $telefonszam['megjegyzes'] ?><?=$szerklinkzar?></td>
                </tr><?php
                $szamlalo++;
            }
        ?></tbody>
    </table>
</div>
<script><?php
$oszlopszam = 0;
foreach($oszlopok as $oszlop)
{
    if($oszlop['nev'])
    {
        ?>
        document.getElementById("f<?=$oszlopszam?>").addEventListener("search", function(event) {
			filterTable('f<?=$oszlopszam?>', '<?=$tipus?>', <?=$oszlopszam?>, true);
		}); <?php
    }
    $oszlopszam++;
}
?></script>