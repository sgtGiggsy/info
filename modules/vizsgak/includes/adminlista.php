<?php
if(!$felhasznaloid)
{
    echo "<h2>Az oldal kizárólag bejelentkezett felhasználók számára érhető el!</h2>";
}
elseif(!$contextmenujogok['adminlista'])
{
    echo "<h2>Nincs jogosultsága az oldal megtekintésére!</h2>";
}
else
{
    $adminlista = mySQLConnect("SELECT beallitasok, kerdesek, adminkijeloles, ujkornyitas,
                felhasznalok.nev AS felhasznalo,
                felhasznalok.felhasznalonev AS felhasznalonev,
                felhasznalok.id AS felhasznaloid
            FROM vizsgak_adminok
                INNER JOIN felhasznalok ON vizsgak_adminok.felhasznalo = felhasznalok.id
            WHERE vizsga = '$vizsgaid'
            ORDER BY felhasznalok.nev ASC;");

    $oszlopok = array(
        array('nev' => 'Név', 'tipus' => 's'),
        array('nev' => 'Eredmény megtekintés', 'tipus' => 's'),
        array('nev' => 'Kérdés adminisztráció', 'tipus' => 's'),
        array('nev' => 'Beállítások', 'tipus' => 's'),
        array('nev' => 'Új kör nyitása', 'tipus' => 's'),
        array('nev' => 'Adminok kijelölése', 'tipus' => 's')
    );

    $oszlopszam = 0;
    $tipus = "adminlist";

    if($contextmenujogok['adminkijeloles'])
    {
        ?><div class="szerkgombsor">
            <button type="button" onclick="location.href='<?=$RootPath?>/vizsga/<?=$vizsgaadatok['url']?>/adminszerkeszt?action=addnew'">Új admin kijelölése</button>
        </div><?php
    }
    ?><div class="PrintArea">
        <div class="oldalcim">A vizsga adminisztrátorai</div>
        <table id="<?=$tipus?>">
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
                foreach($adminlista as $vizsgaadmin)
                {
                    $url = $vizsgaadatok['url'];
                    $adminfelhid = $vizsgaadmin['felhasznaloid'];
                    ?><tr <?=($contextmenujogok['adminkijeloles']) ? "class='kattinthatotr' data-href='$RootPath/vizsga/$url/adminszerkeszt/$adminfelhid'" : "" ?>>
                        <td><?=$vizsgaadmin['felhasznalo']?> (<?=$vizsgaadmin['felhasznalonev']?>)</td>
                        <td>Igen</td>
                        <td><?=($vizsgaadmin['kerdesek']) ? "Igen" : "Nem" ?></td>
                        <td><?=($vizsgaadmin['beallitasok']) ? "Igen" : "Nem" ?></td>
                        <td><?=($vizsgaadmin['ujkornyitas']) ? "Igen" : "Nem" ?></td>
                        <td><?=($vizsgaadmin['adminkijeloles']) ? "Igen" : "Nem" ?></td>
                    </tr><?php
                }
            ?></tbody>
        </table>
    </div><?php
}