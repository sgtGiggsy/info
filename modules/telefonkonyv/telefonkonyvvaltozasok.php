<?php
$globaltelefonkonyvadmin = telefonKonyvAdminCheck($mindir);
if($globaltelefonkonyvadmin || $csoportir)
{   
    $valtozasszures = "minden";
    if(isset($_GET['valtozasszures']))
    {
        $valtozasszures = $_GET['valtozasszures'];
        $_SESSION[getenv('SESSION_NAME').'valtozasszures'] = $valtozasszures;
    }
    elseif(isset($_SESSION[getenv('SESSION_NAME').'valtozasszures']))
    {
        $valtozasszures = $_SESSION[getenv('SESSION_NAME').'valtozasszures'];
    }

    $javascriptfiles[] = "modules/telefonkonyv/includes/telefonkonyv.js";
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

    $tkonyvwheresettings = array(
        'where' => true,
        'mezonev' => true,
        'felhasznalo' => '',
        'modkorszur' => true,
        'modid' => $modkorid
    );
    $where = getTkonyvszerkesztoWhere($globaltelefonkonyvadmin, $tkonyvwheresettings);
    $allapotszur = null;
    if($valtozasszures != "minden")
    {
        $allapotszur = " AND ";
        switch($valtozasszures)
        {
            case "jovahagyasravarok" : $allapotszur .= "telefonkonyvvaltozasok.allapot = 1"; break;
            case "jovahagyottak" : $allapotszur .= "telefonkonyvvaltozasok.allapot > 1"; break;
            case "jovahagyottvagyelutasitott" : $allapotszur .= "(telefonkonyvvaltozasok.allapot > 1 OR telefonkonyvvaltozasok.allapot IS NULL)"; break;
            case "elutasitottak" : $allapotszur .= "telefonkonyvvaltozasok.allapot IS NULL"; break;
        }
    }

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
            telefonkonyvcsoportok.id AS csoportid,
            felhasznalok.felhasznalonev AS felhasznalonev,
            felhasznalok.nev AS modosito,
            telefonkonyvbeosztasok_mod.megjegyzes AS megjegyzes,
            telefonkonyvvaltozasok.allapot AS allapot,
            telefonkonyvbeosztasok_mod.torolve AS torolve,
            telefonkonyvvaltozasok.timestamp AS bekuldesideje
        FROM telefonkonyvvaltozasok
            LEFT JOIN telefonkonyvbeosztasok_mod ON telefonkonyvvaltozasok.ujbeoid = telefonkonyvbeosztasok_mod.id
            LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvvaltozasok.ujfelhid = telefonkonyvfelhasznalok.id
            LEFT JOIN nevelotagok ON telefonkonyvfelhasznalok.elotag = nevelotagok.id
            LEFT JOIN titulusok ON telefonkonyvfelhasznalok.titulus = titulusok.id
            LEFT JOIN rendfokozatok ON telefonkonyvfelhasznalok.rendfokozat = rendfokozatok.id
            LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok_mod.csoport = telefonkonyvcsoportok.id
            LEFT JOIN felhasznalok ON telefonkonyvvaltozasok.bejelento = felhasznalok.id
        $where
        $allapotszur
        ORDER BY telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok_mod.sorrend, telefonkonyvfelhasznalok.nev DESC;");

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
        array('nev' => 'Megjegyzés', 'tipus' => 's'),
        array('nev' => 'Bejelentő', 'tipus' => 's')
    );

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

    if($globaltelefonkonyvadmin) 
    {
        ?><div class="szerkgombsor"><?php
            if($excelenged)
            {
                ?><button type="button" onclick="location.href='<?=$RootPath?>/telefonkonyvvaltozasok?action=exportexcel'">Módosítási Excel fájl generálása</button><?php
            }
            if($modositasikor['excelexport'])
            {
                ?><button type="button" onclick="confirmFinalize()">Jóváhagyott módosítások rögzítése</button><?php
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
            <div class="szuresvalaszto">Lista szűrése
                <select id="valtozasszures" name="valtozasszures" onchange="valtozasokSzurese();">
                    <option value="minden" <?=($valtozasszures == "minden") ? "selected" : "" ?>>Minden mutatása</option>
                    <option value="jovahagyasravarok" <?=($valtozasszures == "jovahagyasravarok") ? "selected" : "" ?>>Jóváhagyásra várók</option>
                    <option value="jovahagyottak" <?=($valtozasszures == "jovahagyottak") ? "selected" : "" ?>>Jóváhagyottak</option>
                    <option value="jovahagyottvagyelutasitott" <?=($valtozasszures == "jovahagyottvagyelutasitott") ? "selected" : "" ?>>Jóváhagyott vagy elutasitott</option>
                    <option value="elutasitottak" <?=($valtozasszures == "elutasitottak") ? "selected" : "" ?>>Elutasítottak</option>
                </select>
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
                    if($globaltelefonkonyvadmin)
                    {
                        $kattinthatolink = $RootPath . "/valtozasfelulvizsgalat/" . $valtozasid;
                    }
                    else
                    {
                        $kattinthatolink = $RootPath . "/telefonszamvaltozas?modid=" . $valtozasid;
                    } 

                    ?><tr class="trlink<?=($telefonszam['torolve']) ? ' mukodeskeptelen' : '' ?>"
                            id="<?=$csoportnev?>"
                            style="font-weight: normal;">
                        <td class="prioritas <?=$allapot?>" style="position: relative;" id="prioritylevel-<?=$valtozasid?>"><?php
                            if($globaltelefonkonyvadmin)
                            {
                                ?><a onclick="rejtMutat('valaszto-<?=$valtozasid?>')" style="cursor:pointer;"></a>
                                <div class="tkonyvquickapprove" style="display:none;" id="valaszto-<?=$valtozasid?>">
                                    <form>
                                        <input type ="hidden" id="id" name="id" value="<?=$valtozasid?>">
                                        <input type ="hidden" id="csoport-<?=$valtozasid?>" name="csoport" value="<?=$telefonszam['csoportid']?>">
                                        <input type ="hidden" id="allapot-<?=$valtozasid?>" name="allapot" value="0">
                                    </form>
                                    <div><a style="background-color: var(--online)" onclick="gyorsJovahagyas('<?=$valtozasid?>', 1)">Jóváhagy</a></div>
                                    <div><a style="background-color: var(--offline)" onclick="gyorsJovahagyas('<?=$valtozasid?>', 0)">Elutasít</a></div>
                                </div><?php
                            }
                            else
                            {
                                ?><a href="<?=$kattinthatolink?>"></a><?php
                            }
                        ?></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['beosztas']?></a></td>
                        <td style="width:4ch; text-align:right;"><a href="<?=$kattinthatolink?>"><?=$telefonszam['elotag']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['nev']?></a></td>
                        <td style="width:5ch; text-align:right;"><a href="<?=$kattinthatolink?>"><?=$telefonszam['titulus']?></a></td>
                        <td style="width:8ch;"><a href="<?=$kattinthatolink?>"><?=$telefonszam['rendfokozat']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['belsoszam']?><?=($telefonszam['belsoszam2']) ? "<br>" . $telefonszam['belsoszam2'] : "" ?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['kozcelu']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['fax']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['kozcelufax']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['mobil']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['megjegyzes']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['modosito']?> (<?=$telefonszam['felhasznalonev']?>)<br><?=$telefonszam['bekuldesideje']?></a></td>
                    </tr><?php
                    $szamlalo++;
                }
            ?></tbody>
        </table>
    </div><?php
}
else
{
    getPermissionError();
}