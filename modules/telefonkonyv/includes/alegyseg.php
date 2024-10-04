<?php
if(!$globaltelefonkonyvadmin)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $id = $nev = $sorrend = $magyarazat = null;

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/telefonkonyv/db/alegysegdb.php");

        redirectToGyujto("telefonkonyvalegysegek");
    }
    
    $button = "Új alegység";
    $irhat = true;
    $form = "modules/telefonkonyv/forms/alegysegszerkesztform";
    $oldalcim = "Új alegység hozzáadása";

    $alegysegek = mySQLConnect("SELECT * FROM telefonkonyvcsoportok WHERE id > 1 AND torolve IS NULL ORDER by sorrend ASC;");

    if(isset($_GET['id']))
    {
        $alegysegid = $_GET['id'];
        $alegysegszerk = mySQLConnect("SELECT * FROM telefonkonyvcsoportok WHERE id = $alegysegid AND torolve IS NULL;");
        $alegysegszerk = mysqli_fetch_assoc($alegysegszerk);

        @$id = $alegysegszerk['id'];
        @$nev = $alegysegszerk['nev'];
        @$sorrend = $alegysegszerk['sorrend'];

        $button = "Alegység szerkesztése";
        $oldalcim = "Alegység szerkesztése";
    }

    if($nev || (isset($_GET['action']) && $_GET['action'] == "addnew"))
    {
        include('././templates/edit.tpl.php');
    }
    else
    {
        echo "<h2>Nincs ilyen azonosítójú alegység az adatbázisban!</h2>";
    }
}