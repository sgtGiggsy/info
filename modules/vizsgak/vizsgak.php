<?php
$szamlalo = null;
$oldalcimsor = "Vizsgák listája";

$vizsgak = mySQLConnect("SELECT felhasznalok.id AS szerkesztoid,
        vizsgak_vizsgak.url AS vizsgaurl,
        felhasznalok.nev AS vizsgaadmin,
        vizsgak_vizsgak.nev AS vizsganev,
        eles,
        vizsgak_vizsgak.leiras AS leiras,
        korlatozott,
        felheng.id AS engedfelh
    FROM vizsgak_vizsgak
        LEFT JOIN vizsgak_adminok ON vizsgak_adminok.vizsga = vizsgak_vizsgak.id
        LEFT JOIN felhasznalok ON vizsgak_adminok.felhasznalo = felhasznalok.id
        LEFT JOIN vizsgak_engedelyezettek ON vizsgak_engedelyezettek.vizsga = vizsgak_vizsgak.id
        LEFT JOIN felhasznalok felheng ON vizsgak_engedelyezettek.felhasznalo = felheng.id
    ORDER BY vizsgak_vizsgak.id;");


$oszlopok = array(
    array('nev' => '', 'tipus' => 's'),
    array('nev' => 'Az elérhető vizsgák listája', 'tipus' => 's'),
    array('nev' => '', 'tipus' => 's')
);

$oszlopszam = 0;
$tipus = "szerkesztok";

if(@$mindir)
{
    ?><div class="szerkgombsor">
        <button type="button" onclick="location.href='<?=$RootPath?>/vizsga?action=addnew'">Új vizsga létrehozása</button>
    </div><?php
}
?><div class="PrintArea">
    <div class="oldalcim"><?=$oldalcimsor?></div>

    <table id="<?=$tipus?>" class="telefonkonyvtabla">
        <thead>
            <tr><?php
                foreach($oszlopok as $oszlop)
                {
                    if($oszlop['nev'])
                    {
                        ?><th class="tsorth">
                            <span onclick="sortTable(<?=$oszlopszam?>, '<?=$oszlop['tipus']?>', '<?=$tipus?>')"><?=$oszlop['nev']?></span>
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
                if($mindir || $vizsga['szerkesztoid'] == $felhasznaloid || ($vizsga['eles'] && (!$vizsga['korlatozott'] || ($vizsga['korlatozott'] && $vizsga['engedfelh'] == $felhasznaloid))))
                {
                    if($elozovizsga != $vizsga['vizsganev'])
                    {
                        $elozovizsga = $vizsga['vizsganev'];
                        $elozoszerkeszto = "";
                        $elozoengedelyezett = $vizsga['engedfelh'];
                    
                        ?><tr>
                            <td colspan=<?=count($oszlopok)?> class="telefonkonyvelvalaszto">
                                <a href="<?=$RootPath?>/vizsga/<?=$vizsga['vizsgaurl']?>/ismerteto" style="width: 100%; height: 100%; display: block;"><?=$vizsga['vizsganev']?><?php
                                if($vizsga['leiras'])
                                {
                                    ?><br>&nbsp;&nbsp;<small style="padding-top: 0; font-weight: normal"><?=$vizsga['leiras']?></small></a><?php
                                }
                            ?></td>
                        </tr><?php
                        
                    }
                }
                if($mindir && $elozoszerkeszto != $vizsga['szerkesztoid'] && (!$vizsga['engedfelh'] || $elozoengedelyezett == $vizsga['engedfelh']))
                {
                    $elozoszerkeszto = $vizsga['szerkesztoid'];
                    
                    ?><tr>
                        <td></td>
                        <td><a href='<?=$RootPath?>/vizsga/<?=$vizsga['vizsgaurl']?>/adminszerkeszt/<?=$vizsga['szerkesztoid']?>'><?=$vizsga['vizsgaadmin']?></td>
                        <td></td>
                    </tr><?php
                }
            }
        ?></tbody>
    </table>
</div>