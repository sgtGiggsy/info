<?php
$szamlalo = $felhasznaloszur = null;
$oldalcimsor = "Vizsgák listája";

$vizsgak = new MySQLHandler("SELECT GROUP_CONCAT(felhasznalok.id SEPARATOR ';') AS adminidlist,
		GROUP_CONCAT(felhasznalok.nev SEPARATOR ';') AS adminnevlist,
        vizsgak_vizsgak.url AS vizsgaurl,
        vizsgak_vizsgak.nev AS vizsganev,
        eles,
        vizsgak_vizsgak.leiras AS leiras,
        korlatozott,
        GROUP_CONCAT(felheng.id SEPARATOR ';') AS engedfelh
    FROM vizsgak_vizsgak
        LEFT JOIN vizsgak_adminok ON vizsgak_adminok.vizsga = vizsgak_vizsgak.id
        LEFT JOIN felhasznalok ON vizsgak_adminok.felhasznalo = felhasznalok.id
        LEFT JOIN vizsgak_engedelyezettek ON vizsgak_engedelyezettek.vizsga = vizsgak_vizsgak.id
        LEFT JOIN felhasznalok felheng ON vizsgak_engedelyezettek.felhasznalo = felheng.id
    GROUP BY vizsgak_vizsgak.id
    ORDER BY vizsgak_vizsgak.id;");
$vizsgak = $vizsgak->Result();


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
            foreach($vizsgak as $vizsga)
            {
                $felhasznalolist = array();
                $adminlist = explode(";", $vizsga['adminidlist']);
                if($vizsga['engedfelh'])
                    $felhasznalolist = explode(";", $vizsga['engedfelh']);    

                if($mindir
                    || ($felhasznaloid && $vizsga['adminidlist'] && in_array($felhasznaloid, $adminlist))
                    || ($vizsga['eles'] && !$vizsga['korlatozott'])
                    || ($felhasznaloid && $vizsga['eles'] && $vizsga['korlatozott'] && in_array($felhasznaloid, $felhasznalolist))
                )
                {
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
                if($mindir)
                {
                    $megjelent = array();
                    $adminids = explode(";", $vizsga['adminidlist']);
                    $adminnevs = explode(";", $vizsga['adminnevlist']);
                    $adminidcount = count($adminids);
                    if($vizsga['adminidlist'])
                    {
                        for($i = 0; $i < $adminidcount; $i++)
                        {
                            if(!in_array($adminids[$i], $megjelent))
                            {
                                ?><tr>
                                    <td></td>
                                    <td><a href='<?=$RootPath?>/vizsga/<?=$vizsga['vizsgaurl']?>/adminszerkeszt/<?=$adminids[$i]?>'><?=$adminnevs[$i]?></td>
                                    <td></td>
                                </tr><?php
                                $megjelent[] = $adminids[$i];
                            }
                        }
                    }
                }
            }
        ?></tbody>
    </table>
</div>