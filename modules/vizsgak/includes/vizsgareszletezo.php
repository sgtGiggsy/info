﻿<?php
// Egyelőre kész
if(!$felhasznaloid)
{
    echo "<h2>Az oldal kizárólag bejelentkezett felhasználók számára érhető el!</h2>";
}
else
{
    $teszteredmenyek = mySQLConnect("SELECT vizsgak_kerdesek.kerdes as kerdes,
        vizsgak_kerdesek.id as kerdesid,
        felhasznalo,
        felhasznalonev,
        nev,
        befejezett,
        kitoltesideje,
        valasz,
        valasz2,
        valasz3,
        vizsgak_valaszlehetosegek.valaszszoveg AS valaszszoveg,
        vizsgak_valaszlehetosegek.id AS vid,
        ROUND(vizsgak_valaszlehetosegek.helyes, 2) AS helyes
    FROM vizsgak_kerdesek
    	INNER JOIN vizsgak_valaszlehetosegek ON vizsgak_kerdesek.id = vizsgak_valaszlehetosegek.kerdes
        INNER JOIN vizsgak_kitoltesvalaszok ON vizsgak_kerdesek.id = vizsgak_kitoltesvalaszok.kerdes
        INNER JOIN vizsgak_kitoltesek ON vizsgak_kitoltesvalaszok.kitoltes = vizsgak_kitoltesek.id
        INNER JOIN felhasznalok ON vizsgak_kitoltesek.felhasznalo = felhasznalok.id
    WHERE vizsgak_kitoltesek.id = $id
    ORDER BY vizsgak_kitoltesvalaszok.id;");

    $vizsgareszletezes = mysqli_fetch_assoc($teszteredmenyek);
    if(mysqli_num_rows($teszteredmenyek) < 1)
    {
        echo "<h2>Nem létező vizsgaazonosító!</h2>";
    }
    else
    {
        if(!($contextmenujogok['admin'] || ($vizsgareszletezes['felhasznalo'] == $felhasznaloid && $vizsgareszletezes['befejezett'] == 1)))
        {
            echo "<h2>Csak a saját, befejezett eredményei megtekintésére van jogosultsága!</h2>";
        }
        else
        {
            $helyes = 0;
            $sorsz = 0;
            ?><div class="PrintArea">
                <div class='oldalcim'><?=$vizsgareszletezes['nev']?> (<?=$vizsgareszletezes['felhasznalonev']?>)</div>
                <div class='contentcenter'>
                    <h4><?=$vizsgareszletezes['kitoltesideje']?></h4><?php
                    foreach($teszteredmenyek as $x)
                    {
                        $stilus = "";
                        if(!isset($kerdesid) || $x['kerdesid'] != $kerdesid)
                        {
                            $sorsz++;
                            ?><h3><?=$sorsz?>. <?=$x['kerdes']?></h3><?php
                        }

                        $kerdesid = $x['kerdesid'];

                        if($x['helyes'])
                        {
                            if($x['vid'] == $x['valasz'] || $x['vid'] == $x['valasz2'] || $x['vid'] == $x['valasz3'])
                            {
                                $helyes += $x['helyes'];
                                $stilus = "talalat";
                            }
                            else
                            {
                                $stilus = "helyes";
                            }
                        }
                        else
                        {
                            if($x['vid'] == $x['valasz'] || $x['vid'] == $x['valasz2'] || $x['vid'] == $x['valasz3'])
                            {
                                $stilus = "hibastipp";
                            }
                        }

                        ?><p <?=($stilus) ? "class=" . $stilus : "" ?>><?=$x['valaszszoveg']?></p><?php
                    }

                    ?><br><h1 style="<?=($helyes < $vizsgaadatok['minimumhelyes']) ? 'color:red' : 'color:green' ?>">
                        <?=($helyes < $vizsgaadatok['minimumhelyes']) ? "Sikertelen" : "Sikeres" ?>
                        <br><?=roundUp99($helyes)?>/<?=$sorsz?>
                    </h1>
                </div>
            </div><?php
        }
    }
}
?>