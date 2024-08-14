<?php
if(!$felhasznaloid)
{
    echo "<h2>Az oldal kizárólag bejelentkezett felhasználók számára érhető el!</h2>";
}
elseif(!$contextmenujogok['engedelyezettek'])
{
    echo "<h2>Nincs jogosultsága az oldal megtekintésére!</h2>";
}
else
{
    $engedelyezettlista = mySQLConnect("SELECT felhasznalok.nev AS felhasznalo,
                felhasznalok.felhasznalonev AS felhasznalonev,
                felhasznalok.id AS felhasznaloid
            FROM vizsgak_engedelyezettek
                INNER JOIN felhasznalok ON vizsgak_engedelyezettek.felhasznalo = felhasznalok.id
            WHERE vizsga = '$vizsgaid'
            ORDER BY felhasznalok.nev ASC;");

    if($contextmenujogok['engedelyezettek'])
    {
        ?><div class="szerkgombsor">
            <button type="button" onclick="location.href='<?=$RootPath?>/vizsga/<?=$vizsgaadatok['url']?>/engedelyezettszerkeszt'">Engedélyezett felhasználók szerkesztése</button>
        </div><?php
    }
    ?><div class="PrintArea">
        <div class="oldalcim">A vizsgát kitölthető felhasználók listája</div>
        <div class="normallist"><?php
            foreach($engedelyezettlista as $x)
            {
                ?><div><?=$x['felhasznalo']?> (<?=$x['felhasznalonev']?>)</div><?php
            }
        ?></div>
    </div><?php
}