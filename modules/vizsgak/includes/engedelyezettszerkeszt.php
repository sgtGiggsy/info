
<?php

if(!$contextmenujogok['engedelyezettek'])
{
    getPermissionError();
}
else
{
    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/vizsgak/db/engedelyezettdb.php");

        $targeturl = "$RootPath/vizsga/" . $vizsgaadatok['url'] . "/engedelyezettek";

        header("Location: $targeturl");
    }

    $irhat = true;
    $magyarazat = null;
    $action = "update";
    $oldalcim = "Felhasználók engedélyezése a vizsgához";
    $button = "Jogosultságok módosítása";
    $form = "modules/vizsgak/forms/engedelyezettform";

    $felhasznalolist = new MySQLHandler("SELECT felhasznalok.id AS id,
                felhasznalok.nev AS nev,
                felhasznalok.felhasznalonev AS usernev,
                vizsgak_engedelyezettek.felhasznalo AS engedelyezve,
                vizsgak_engedelyezettek.vizsga AS vizsga
            FROM felhasznalok
                LEFT JOIN vizsgak_engedelyezettek ON vizsgak_engedelyezettek.felhasznalo = felhasznalok.id
            ORDER BY felhasznalok.nev;");
    $felhasznalolist = $felhasznalolist->Result();

    if(!$irhat)
    {
        getPermissionError();
    }
    else
    {
        include('././templates/edit.tpl.php');
    }
}