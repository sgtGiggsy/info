<?php

if(!$contextmenujogok['adminkijeloles'])
{
    getPermissionError();
}
else
{
    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/vizsgak/db/vizsgaadmindb.php");

        $targeturl = "$RootPath/vizsga/" . $vizsgaadatok['url'] . "/adminlista";

        header("Location: $targeturl");
    }

    $magyarazat = $felhasznalo = $beallitasok = $kerdesek = $adminkijeloles = $ujkornyitas = null;
    $irhat = true;
    $action = "update";
    $oldalcim = "Új admin hozzáadása a vizsgához";
    $button = "Jogosultságok módosítása";
    $form = "modules/vizsgak/forms/vizsgaadminform";

    if(isset($_GET['id']))
    {
        $adminadatok = new MySQLHandler("SELECT beallitasok, kerdesek, adminkijeloles, ujkornyitas,
                felhasznalok.nev AS felhasznalo,
                felhasznalok.felhasznalonev AS felhasznalonev,
                felhasznalok.id AS felhasznaloid
            FROM vizsgak_adminok
                INNER JOIN felhasznalok ON vizsgak_adminok.felhasznalo = felhasznalok.id
            WHERE vizsga = ? AND felhasznalok.id = ?
            ORDER BY felhasznalok.nev ASC;", $vizsgaid, $id);

        if($adminadatok->sorokszama == 1)
        {
            $adminadatok = $adminadatok->Fetch();
            $beallitasok = $adminadatok['beallitasok'];
            $kerdesek = $adminadatok['kerdesek'];
            $adminkijeloles = $adminadatok['adminkijeloles'];
            $felhasznalo = $adminadatok['felhasznalo'];
            $ujkornyitas = $adminadatok['ujkornyitas'];

            $oldalcim = "$felhasznalo vizsga adminisztrációs jogainak módosítása";
        }
        else
        {
            $irhat = false;
        }
    }

    if(!$irhat)
    {
        getPermissionError();
    }
    else
    {
        include('././templates/edit.tpl.php');
    }
}