<?php
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
    <table id="<?=$tipus?>" class="telefonkonyvtabla sorhover">
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
            $output = "";
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
                $kattinthatolink = './telefonkonyv/valtozas';
                $szerklink = $szerklinkzar = "";

                if($telefonszam['allapot'] == 4)
                {
                    $kattinthatolink .= "?modid=" . $telefonszam['modid'] . "&veglegesitett";
                }
                else
                {
                    $kattinthatolink .= "/" . $telszamid;
                }

                $linkstyle = null;
                $megjegyzes = $telefonszam['megjegyzes'];
                if($csoportir)
                {
                    //$szerklink = "<a href='$kattinthatolink' target='_blank' style='$linkstyle'>";
                    //$szerklinkr = "<a href='$kattinthatolink' target='_blank' style='justify-content: right; $linkstyle'>";
                    $szerklink = "<a onclick='window.open(\"$kattinthatolink\", \"_blank\")' style='$linkstyle; cursor: pointer;'>";
                    $szerklinkr = "<a onclick='window.open(\"$kattinthatolink\", \"_blank\")' style='justify-content: right; cursor: pointer; $linkstyle'>";
                    $szerklinkzar = "</a>";
                }
                $style = " normalweightlink";
                if(($telefonszam['allapot'] == 4 && $globaltelefonkonyvadmin) || (!$globaltelefonkonyvadmin && isset($csoportjogok) && in_array($telefonszam['csopid'], $csoportjogok)))
                {
                    $style = "";
                }
                if(!$globaltelefonkonyvadmin && isset($csoportjogok) && in_array($telefonszam['csopid'], $csoportjogok) && $telefonszam['allapot'] == 1)
                {
                    $style = " linkitalic";
                    $megjegyzes = "Változás beküldve, admin még nem hagyta jóvá";
                }

                ?><tr class="trlink<?=$style?>"
                        id="<?=$csoportnev?>"
                        style="<?=($csaksajat && !(isset($csoportjogok) && in_array($telefonszam['csopid'], $csoportjogok))) ? ' display: none;' :  '' ?>"
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
                    <td><?=$szerklink?><?=($nevszerint) ? $telefonszam['csoport'] : $megjegyzes ?><?=$szerklinkzar?></td>
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