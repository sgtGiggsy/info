<?php
$globaltelefonkonyvadmin = telefonKonyvAdminCheck($mindir);
if($globaltelefonkonyvadmin || $csoportir)
{   
    $valtozaskorok = mySQLConnect("SELECT * FROM telefonkonyvmodositaskorok ORDER BY id DESC;");
    $modositasikor = mysqli_fetch_assoc($valtozaskorok);
    $modkorid = $modositasikor['id'];
    $excelenged = true;

    if(isset($_GET['modositasikor']))
    {
        $modkorid = $_GET['modositasikor'];
        foreach($valtozaskorok as $kor)
        {
            if($kor['id'] == $modkorid)
            {
                if($kor['lezarva'])
                {
                    $excelenged = false;
                }
                break;
            }
        }
    }

    $szamlalo = null;
    $csoportfilter = "alegysegfilter";

    $tkonyvwheresettings = array(
        'where' => true,
        'mezonev' => true,
        'felhasznalo' => '',
        'modkorszur' => true,
        'modid' => $modkorid
    );
    $where = getTkonyvszerkesztoWhere($globaltelefonkonyvadmin, $tkonyvwheresettings);
    $alegysegek = mySQLConnect("SELECT * FROM telefonkonyvcsoportok WHERE id > 1;");

    $telefonkonyv = mySQLConnect("SELECT telefonkonyvvaltozasok.id AS valtozasid,
            telefonkonyvbeosztasok_mod.id AS telszamid,
            telefonkonyvbeosztasok_mod.nev AS beosztas,
            nevelotagok.nev AS elotag,
            telefonkonyvfelhasznalok.nev AS nev,
            titulusok.nev AS titulus,
            rendfokozatok.nev AS rendfokozat,
            telefonkonyvbeosztasok_mod.belsoszam AS belsoszam,
            telefonkonyvbeosztasok_mod.belsoszam2 AS belsoszam2,
            telefonkonyvbeosztasok_mod.kozcelu AS kozcelu,
            telefonkonyvbeosztasok_mod.fax AS fax,
            telefonkonyvbeosztasok_mod.kozcelufax AS kozcelufax,
            telefonkonyvfelhasznalok.mobil AS mobil,
            telefonkonyvcsoportok.nev AS csoport,
            felhasznalok.felhasznalonev AS felhasznalonev,
            felhasznalok.nev AS modosito,
            telefonkonyvbeosztasok_mod.megjegyzes AS megjegyzes,
            telefonkonyvvaltozasok.allapot AS allapot
        FROM telefonkonyvvaltozasok
            LEFT JOIN telefonkonyvbeosztasok_mod ON telefonkonyvvaltozasok.ujbeoid = telefonkonyvbeosztasok_mod.id
            LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvvaltozasok.ujfelhid = telefonkonyvfelhasznalok.id
            LEFT JOIN nevelotagok ON telefonkonyvfelhasznalok.elotag = nevelotagok.id
            LEFT JOIN titulusok ON telefonkonyvfelhasznalok.titulus = titulusok.id
            LEFT JOIN rendfokozatok ON telefonkonyvfelhasznalok.rendfokozat = rendfokozatok.id
            LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok_mod.csoport = telefonkonyvcsoportok.id
            LEFT JOIN felhasznalok ON telefonkonyvvaltozasok.bejelento = felhasznalok.id
        $where
        ORDER BY telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok_mod.sorrend;");

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
    if($globaltelefonkonyvadmin)
    {
        $oszlopok[] = array('nev' => 'Bejelentő', 'tipus' => 's');
    }

    $oszlopszam = 0;
    $tipus = "telefonkonyv";

    if($globaltelefonkonyvadmin && isset($_GET['action']) && $_GET['action'] == "exportexcel")
    {    
        mySQLConnect("UPDATE telefonkonyvmodositaskorok SET excelexport = 1 WHERE id = $modkorid");
        include("./modules/telefonkonyv/includes/telefonkonyvmodositasexport.php");
    }
    elseif($globaltelefonkonyvadmin && isset($_GET['action']) && $_GET['action'] == "confirmchanges")
    {
        if($modositasikor['excelexport'])
        {
            $irhat = true;
            include("./modules/telefonkonyv/db/telefonkonyvdb.php");
            
            redirectToGyujto("telefonkonyvvaltozasok");
        }
        else
        {
            $uzenet = "Sajnálom, de a változások nem véglegesíthetőek az Excel exportálás elkészülte előtt";
            ?><h2 style="color: #990000"><?=$uzenet?></h2>
            <script>alert("<?=$uzenet?>")</script><?php
        }
    }

    ?><datalist id="alegysegek"><?php
    foreach($alegysegek as $alegyseg)
    {
        ?><option><?=$alegyseg['nev']?></option><?php
    }
    ?></datalist><?php

    if($globaltelefonkonyvadmin) 
    {
        ?><div class="szerkgombsor"><?php
            if($excelenged)
            {
                ?><button type="button" onclick="location.href='<?=$RootPath?>/telefonkonyvvaltozasok?action=exportexcel'">Módosítási Excel fájl generálása</button><?php
            }
            if($modositasikor['excelexport'])
            {
                ?><button type="button" onclick="confirmDiscard()">Jóváhagyott módosítások rögzítése</button><?php
            }
        ?></div><?php
    }
    ?><div class="PrintArea">
        <div class="oldalcim">Telefonkönyv módosítások (<?=$modositasikor['megkezdve']?> - <?=$modositasikor['lezarva']?>)
            <div class="buttondropdown">
                <span style="cursor: pointer;" onclick="showPopup('korabbikorok')">⮟</span>
                <div id="korabbikorok" onmouseleave="hidePopup('korabbikorok')"><?php
                    foreach($valtozaskorok as $kor)
                    {
                        ?><a href="<?=$RootPath?>/telefonkonyvvaltozasok?modositasikor=<?=$kor['id']?>"><?=$kor['megkezdve']?> - <?=$kor['lezarva']?></a><?php
                    }
                ?></div>
            </div>
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
                                    type="text"
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
                $csoportszamlalo = 0;

                foreach($telefonkonyv as $telefonszam)
                {
                    switch($telefonszam['allapot'])
                    {
                        case 1: $allapot = ""; break;
                        case 2: $allapot = "fontos"; break;
                        case 3: $allapot = "halaszthato"; break;
                        case 4: $allapot = "alacsony"; break;
                        default: $allapot = "kritikus";
                    }
                    
                    if($elozocsoport != $telefonszam['csoport'])
                    {
                        $szamlalo = 0;
                        $csoportnevalap = "csoport" . $csoportszamlalo . "-";
                        $csoportnev = $csoportnevalap . $szamlalo;
                        $elozocsoport = $telefonszam['csoport'];
                        ?><tr id="<?=$csoportnev?>" class="<?=$elozocsoport?>">
                            <td colspan=<?=count($oszlopok)?> style="cursor:pointer" class="telefonkonyvelvalaszto" onclick="showHideCsoport('<?=$csoportnevalap?>', '<?=$tipus?>')"><?=$telefonszam['csoport']?></td>
                        </tr><?php

                        $csoportszamlalo++;
                        $szamlalo++;
                    }
                    $valtozasid = $telefonszam['valtozasid'];
                    $csoportnev = $csoportnevalap . $szamlalo;
                    ?><tr class='kattinthatotr <?=$elozocsoport?>' <?=($globaltelefonkonyvadmin) ?  "data-href='$RootPath/valtozasfelulvizsgalat/$valtozasid'" : "data-href='$RootPath/telefonszamvaltozas?modid=$valtozasid'" ?>
                            id="<?=$csoportnev?>"
                            style="font-weight: normal;">
                        <td class="prioritas <?=$allapot?>"></td>
                        <td><?=$telefonszam['beosztas']?></td>
                        <td style="width:4ch; text-align:right;"><?=$telefonszam['elotag']?></td>
                        <td><?=$telefonszam['nev']?></td>
                        <td style="width:5ch; text-align:right;"><?=$telefonszam['titulus']?></td>
                        <td style="width:8ch;"><?=$telefonszam['rendfokozat']?></td>
                        <td><?=$telefonszam['belsoszam']?><?=($telefonszam['belsoszam2']) ? "<br>" . $telefonszam['belsoszam2'] : "" ?></td>
                        <td><?=$telefonszam['kozcelu']?></td>
                        <td><?=$telefonszam['fax']?></td>
                        <td><?=$telefonszam['kozcelufax']?></td>
                        <td><?=$telefonszam['mobil']?></td>
                        <td><?=$telefonszam['megjegyzes']?></td><?php
                        if($globaltelefonkonyvadmin)
                        {
                            ?><td><?=$telefonszam['modosito']?> (<?=$telefonszam['felhasznalonev']?>)</td><?php
                        }
                    ?></tr><?php
                    $szamlalo++;
                }
            ?></tbody>
        </table>
    </div>
    
    <script>
        function confirmDiscard()
        {
            var x = confirm("Biztosan rögzíteni akarod a módosításokat?\nA rögzítést követően nem lehet már a változásjelentési exportot megcsinálni!");
            if (x)
            {
                window.location.href="<?=$RootPath?>/telefonkonyvvaltozasok?action=confirmchanges"
            }
            else
            {
                return false;
            }
        }

        function openKorabbi()
        {
            korabbi = document.getElementById('korabbikorok');
            korabbi.style.display = "block";
        }
    </script><?php
}
else
{
    getPermissionError();
}