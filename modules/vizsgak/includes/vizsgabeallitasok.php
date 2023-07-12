<?php
if(!$felhasznaloid)
{
    echo "<h2>Nincs bejelentkezett felhasználó!</h2>";
}
else
{
    if(!$mindir && !$contextmenujogok['vizsgabeallitasok'])
    {
        echo "<h2>Nincs jogosultságod az oldal megtekintésére!</h2>";
    }
    else
    {
        if(count($_POST) > 0)
        {
            $irhat = true;
            include("./modules/vizsgak/db/vizsgabeallitasdb.php");

            if(isset($vizsgaadatok))
            {
                $vizsgabeallitasurl = "$RootPath/vizsga/" . $vizsgaadatok['url'] . "/vizsgabeallitasok";
                header("Location: $vizsgabeallitasurl");
            }
        }
        
        $vizsgaid = $nev = $url = $udvozloszoveg = $kerdesszam = $minimumhelyes = $vizsgaido = $ismetelheto = $maxismetles = $leiras = $fejleckep = null;
        $vizsgabeallitasurl = "$RootPath/vizsga?action=addnew";

        if(isset($vizsgaadatok))
        {
            $vizsgaid = $vizsgaadatok['id'];
            $nev = $vizsgaadatok['nev'];
            $url = $vizsgaadatok['url'];
            $udvozloszoveg = $vizsgaadatok['udvozloszoveg'];
            $kerdesszam = $vizsgaadatok['kerdesszam'];
            $minimumhelyes = $vizsgaadatok['minimumhelyes'];
            $vizsgaido = $vizsgaadatok['vizsgaido'];
            $ismetelheto = $vizsgaadatok['ismetelheto'];
            $maxismetles =  $vizsgaadatok['maxismetles'];
            $leiras =  $vizsgaadatok['leiras'];
            $fejleckep =  $vizsgaadatok['fejleckep'];
            $vizsgabeallitasurl = "$RootPath/vizsga/" . $vizsgaadatok['url'] . "/vizsgabeallitasok";
        }
        $button = "Beállítások mentése";
        $irhat = true;
        $form = "modules/vizsgak/forms/beallitasform";
        $oldalcim = "Vizsga beállításai";
        $magyarazat = null;
        include('././templates/edit.tpl.php');
        
        if(isset($_GET['vizsgareset']))
        {
            mySQLConnect("DELETE FROM `tesztvalaszok`;");
            mySQLConnect("DELETE FROM `kitoltesek`;");
            mySQLConnect("ALTER TABLE `kitoltesek` AUTO_INCREMENT = 1;");
            mySQLConnect("ALTER TABLE `tesztvalaszok` AUTO_INCREMENT = 1");
            //header("Location: $RootPath/beallitasok");
        }
    }
}
?>