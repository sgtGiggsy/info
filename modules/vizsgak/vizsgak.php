<?php
if(!$csoportir)
{
    getPermissionError();
}
else
{
    $szamlalo = null;
    $oldalcimsor = "Vizsgák listája";
    $csoportfilter = "csoportfilter";

    $vizsgak = mySQLConnect("SELECT felhasznalok.id AS szerkesztoid,
            vizsgak_vizsgak.url AS vizsgaurl,
            felhasznalok.nev AS vizsgaadmin,
            vizsgak_vizsgak.nev AS vizsganev
        FROM vizsgak_vizsgak
            LEFT JOIN vizsgak_adminok ON vizsgak_adminok.vizsga = vizsgak_vizsgak.id
            LEFT JOIN felhasznalok ON vizsgak_adminok.felhasznalo = felhasznalok.id
        ORDER BY vizsgak_vizsgak.id;");


    $oszlopok = array(
        array('nev' => '', 'tipus' => 's'),
        array('nev' => 'Viszgák és szerkesztőik', 'tipus' => 's'),
        array('nev' => '', 'tipus' => 's')
    );

    $oszlopszam = 0;
    $tipus = "szerkesztok";

    ?><div class="szerkgombsor">
        <button type="button" onclick="location.href='<?=$RootPath?>/vizsgaadminok?action=addnew'">Új szerkesztő felvétele</button>
    </div>
    <div class="PrintArea">
        <div class="oldalcim"><?=$oldalcimsor?></div>

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
                $elozovizsga = 0;

                foreach($vizsgak as $vizsga)
                {
                    if($elozovizsga != $vizsga['vizsganev'])
                    {
                        $elozovizsga = $vizsga['vizsganev'];
                        ?><tr>
                            <td colspan=<?=count($oszlopok)?> class="telefonkonyvelvalaszto"><a href="<?=$RootPath?>/vizsga/<?=$vizsga['vizsgaurl']?>" style="width: 100%; height: 100%; display: block;"><?=$vizsga['vizsganev']?></a></td>
                        </tr><?php
                    }
                    ?><tr>
                        <td></td>
                        <td><a href='<?=$RootPath?>/vizsga/<?=$vizsga['vizsgaurl']?>'><?=$vizsga['vizsgaadmin']?></td>
                        <td></td>
                    </tr><?php
                }
            ?></tbody>
        </table>
    </div><?php
}
