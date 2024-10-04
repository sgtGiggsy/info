<?php

if(!$globaltelefonkonyvadmin)
{
    getPermissionError();
}
else
{
    $javascriptfiles[] = "includes/js/csoportFilter.js";
    $szamlalo = null;
    $oldalcimsor = "Telefonkönyv szerkesztők - ";
    $csoportfilter = "csoportfilter";

    if(isset($_GET['felhasznalonkent']))
    {
        $oldalcimsor .= "Felhasználónként csoportosítva";
        $felhasznalok = "cimsor";
        $csoportok = "elem";
        $orderby = "felhasznalok.nev";
        $where = "WHERE felhasznalok.id IS NOT NULL";
        $szurescimsor = "Szerkesztőkre";
        $oszlopfej = "Alegységek";
        $csoportositas = "Csoportosítás alegységek szerint";
        $csoplink = null;
    }
    else
    {
        $oldalcimsor .= "Alegységenként csoportosítva";
        $felhasznalok = "elem";
        $csoportok = "cimsor";
        $orderby = "telefonkonyvcsoportok.sorrend";
        $where = null;
        $szurescimsor = "Alegységekre";
        $oszlopfej = "Szerkesztők";
        $csoportositas = "Csoportosítás szerkesztők szerint";
        $csoplink = "?felhasznalonkent";
    }

    $szerkesztok = mySQLConnect("SELECT felhasznalok.id AS szerkesztoid,
            felhasznalok.nev AS $felhasznalok,
            telefonkonyvcsoportok.nev AS $csoportok
        FROM telefonkonyvcsoportok
            LEFT JOIN telefonkonyvadminok ON telefonkonyvadminok.csoport = telefonkonyvcsoportok.id
            LEFT JOIN felhasznalok ON telefonkonyvadminok.felhasznalo = felhasznalok.id
        $where
        ORDER BY $orderby;");


    $oszlopok = array(
        array('nev' => '', 'tipus' => 's'),
        array('nev' => $oszlopfej, 'tipus' => 's'),
        array('nev' => '', 'tipus' => 's')
    );

    $oszlopszam = 0;
    $tipus = "szerkesztok";

    ?><datalist id="szerkesztokdl"><?php
    foreach($szerkesztok as $szerkeszto)
    {
        ?><option><?=$szerkeszto['cimsor']?></option><?php
    }
    ?></datalist>
    <div class="szerkgombsor">
        <button type="button" onclick="location.href='<?=$RootPath?>/telefonkonyv/szerkeszto?action=addnew'">Új szerkesztő felvétele</button>
        <button type="button" onclick="location.href='<?=$RootPath?>/telefonkonyv/szerkesztok<?=$csoplink?>'"><?=$csoportositas?></button>
    </div>
    <div class="PrintArea">
        <div class="oldalcim"><?=$oldalcimsor?>
            <div class="szuresvalaszto"><?=$szurescimsor?> szűrés
                <input style="width: 40ch"
                        size="1"
                        type="search"
                        id="<?=$csoportfilter?>"
                        list="szerkesztokdl"
                        onkeyup="filterCsoport('<?=$csoportfilter?>', '<?=$tipus?>')"
                        placeholder="Szerkesztő"
                        title="Szerkesztő">
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
                $csoportszamlalo = 0;

                foreach($szerkesztok as $szerkeszto)
                {
                    if($elozocsoport != $szerkeszto['cimsor'])
                    {
                        $szamlalo = 0;
                        $csoportnevalap = "csoport" . $csoportszamlalo . "-";
                        $csoportnev = $csoportnevalap . $szamlalo;
                        $elozocsoport = $szerkeszto['cimsor'];
                        ?><tr id="<?=$csoportnev?>" class="<?=$elozocsoport?>">
                            <td colspan=<?=count($oszlopok)?> style="cursor:pointer" class="telefonkonyvelvalaszto <?=$elozocsoport?>" onclick="showHideCsoport('<?=$csoportnevalap?>', '<?=$tipus?>')"><?=$szerkeszto['cimsor']?></td>
                        </tr><?php
                        $csoportszamlalo++;
                        $szamlalo++;
                    }
                    $szerkesztoid = $szerkeszto['szerkesztoid'];
                    $csoportnev = $csoportnevalap . $szamlalo;
                    ?><tr id="<?=$csoportnev?>" class="<?=$elozocsoport?>">
                        <td></td>
                        <td><a href='<?=$RootPath?>/telefonkonyv/szerkeszto/<?=$szerkesztoid?>'><?=$szerkeszto['elem']?></td>
                        <td></td>
                    </tr><?php
                    $szamlalo++;
                }
            ?></tbody>
        </table>
    </div><?php
}
