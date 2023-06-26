<?php

if($globaltelefonkonyvadmin = telefonKonyvAdminCheck($mindir))
{
    if($globaltelefonkonyvadmin && isset($_GET['action']) && $_GET['action'] == "confirmchanges")
    {
        $irhat = true;
        include("./modules/telefonkonyv/db/telefonkonyvdb.php");
        
        redirectToGyujto("telefonkonyvvaltozasok");
    }
    
    $szamlalo = null;

    $alegysegek = mySQLConnect("SELECT * FROM telefonkonyvcsoportok WHERE id > 1;");

    $telefonkonyv = mySQLConnect("SELECT telefonkonyvvaltozasok.id AS valtozasid,
            telefonkonyvbeosztasok.nev AS beosztas,
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
            telefonkonyvvaltozasok.beosztasnev AS beosztasnev,
            felhasznalok.felhasznalonev AS felhasznalo,
            telefonkonyvvaltozasok.megjegyzes AS megjegyzes,
            telefonkonyvvaltozasok.allapot AS allapot
        FROM telefonkonyvvaltozasok
            LEFT JOIN nevelotagok ON telefonkonyvvaltozasok.elotag = nevelotagok.id
            LEFT JOIN titulusok ON telefonkonyvvaltozasok.titulus = titulusok.id
            LEFT JOIN rendfokozatok ON telefonkonyvvaltozasok.rendfokozat = rendfokozatok.id
            LEFT JOIN telefonkonyvbeosztasok ON telefonkonyvvaltozasok.beosztas = telefonkonyvbeosztasok.id
            LEFT JOIN telefonkonyvcsoportok ON (telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id OR telefonkonyvvaltozasok.csoport = telefonkonyvcsoportok.id)
            LEFT JOIN felhasznalok ON telefonkonyvvaltozasok.felhasznalo = felhasznalok.id
        ORDER BY telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok.sorrend;");

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
    ?></datalist><?php

    if($globaltelefonkonyvadmin) 
    {
        ?><button type="button" onclick="confirmDiscard()">Jóváhagyott módosítások rögzítése</button><?php
    }
    ?><div class="PrintArea">
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
                        ?><tr id="<?=$telefonszam['csoport']?>-<?=$szamlalo?>">
                            <td colspan=<?=count($oszlopok)?> style="cursor:pointer" class="telefonkonyvelvalaszto" onclick="showHideAlegyseg('<?=$telefonszam['csoport']?>', '<?=$tipus?>')"><?=$telefonszam['csoport']?></td>
                        </tr><?php
                        $elozocsoport = $telefonszam['csoport'];
                        $szamlalo++;
                    }
                    $valtozasid = $telefonszam['valtozasid'];
                    ?><tr <?=($csoportir) ? "class='kattinthatotr'" . "data-href='$RootPath/valtozasfelulvizsgalat/$valtozasid'" : "" ?> id="<?=$telefonszam['csoport']?>-<?=$szamlalo?>" style="font-weight: normal;">
                        <td class="prioritas <?=$allapot?>"></td>
                        <td><?=$telefonszam['beosztasnev']?></td>
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
    </script><?php
}
else
{
    getPermissionError();
}