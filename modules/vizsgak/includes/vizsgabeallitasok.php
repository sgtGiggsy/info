<?php
if(!$felhasznaloid)
{
    echo "<h2>Nincs bejelentkezett felhasználó!</h2>";
}
else
{
    if(!$contextmenujogok['vizsgabeallitasok'])
    {
        echo "<h2>Nincs jogosultságod az oldal megtekintésére!</h2>";
    }
    else
    {
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