<?php
if(!$globaltelefonkonyvadmin)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/telefonkonyv/db/megfeleltetdb.php");

        header("Location: $RootPath/telefonkonyv/admegfeleltetes");
        die();
    }
    else
    {
        $felhasznalok = mySQLConnect("SELECT id, nev, felhasznalonev, telefon, beosztas, osztaly, descript AS rendfokozat FROM felhasznalok WHERE aktiv = 1 ORDER BY nev");
        mysqliToArray($felhasznalok);

        $tkonyvlista = mySQLConnect("SELECT telefonkonyvfelhasznalok.id AS id, telefonkonyvfelhasznalok.nev AS nev,
            rendfokozatok.nev AS rendfokozat, belsoszam, belsoszam2, telefonkonyvcsoportok.nev AS osztaly,
            telefonkonyvbeosztasok.nev AS beosztas
        FROM telefonkonyvfelhasznalok
            INNER JOIN rendfokozatok ON telefonkonyvfelhasznalok.rendfokozat = rendfokozatok.id
            INNER JOIN telefonkonyvbeosztasok ON telefonkonyvbeosztasok.felhid = telefonkonyvfelhasznalok.id
            INNER JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
        WHERE telefonkonyvfelhasznalok.felhasznalo IS NULL
        ORDER BY telefonkonyvfelhasznalok.nev ASC;");

        ?><table>
            <thead>
                <tr>
                    <th>Név</th>
                    <th>Rendfokozat</th>
                    <th>Osztály</th>
                    <th>Beosztás</th>
                    <th>Belsőszám</th>
                    <th>AD találat</th>
                </tr>
            </thead>
            <tbody>
                <form action="<?=$RootPath?>/telefonkonyv/admegfeleltetes?action=megfeleltet" method="POST" onsubmit="beKuld.disabled = true; return true;"><?php
                    foreach($tkonyvlista as $tkonyvfelh)
                    {
                        $talalarray = array();
                        foreach($felhasznalok as $adfelh)
                        {
                            if(str_contains(mb_strtolower($adfelh['nev']), mb_strtolower($tkonyvfelh['nev'])) || str_contains(mb_strtolower($tkonyvfelh['nev']), rendfLevag(mb_strtolower($adfelh['nev']))))
                                $talalarray[] = $adfelh;
                        }
                        if(count($talalarray) > 0)
                        {
                            ?><input type="hidden" name="tkonyvfelhid[]" value="<?=$tkonyvfelh['id']?>">
                            <tr>
                                <td><?=$tkonyvfelh['nev']?></td>
                                <td><?=$tkonyvfelh['rendfokozat']?></td>
                                
                                <td><?=$tkonyvfelh['belsoszam']?><?=($tkonyvfelh['belsoszam2']) ? "; " . $tkonyvfelh['belsoszam2'] : "" ?></td>
                                <td>
                                    <select name="adfelhid[]"><?php
                                        foreach($talalarray as $talalat)
                                        {
                                            ?><option value="<?=$talalat['id']?>"><?=$talalat['nev']?> (<?=$talalat['beosztas']?>) - <?=$talalat['telefon']?></option><?php
                                        }
                                    ?><option value=""></option>
                                    </select>
                                </td>
                                <td><?=$tkonyvfelh['beosztas']?></td>
                                <td><?=$tkonyvfelh['osztaly']?></td>
                            </tr><?php
                        }
                    }
                ?><tr>
                    <td colspan="6"><input type="submit" name="beKuld" value='Eredmények megfeleltetése'></td>
                </tr>
                </form>
            </tbody>
        </table><?php
    }
}