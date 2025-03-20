<?php
if(!$felhasznaloid)
{
    echo "<h2>Nincs bejelentkezett felhasználó!</h2>";
    include("./belepes.php");
}
else
{
    if(!$mindir && !@$contextmenujogok['vizsgabeallitasok'])
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
        
        $vizsgaid = $nev = $url = $udvozloszoveg = $vendegudvozlo = $kerdesszam = $minimumhelyes = $korlatozott =
        $vizsgaido = $ismetelheto = $maxismetles = $leiras = $fejleckep = $ujkorurl = $lablecszoveg = $vizsgaeles = $kiertmutat = null;
        $vizsgabeallitasurl = "$RootPath/vizsga?action=addnew";

        if(isset($vizsgaadatok))
        {
            $vizsgaid = $vizsgaadatok['id'];
            $nev = $vizsgaadatok['nev'];
            $url = $vizsgaadatok['url'];
            $udvozloszoveg = $vizsgaadatok['udvozloszoveg'];
            $vendegudvozlo = $vizsgaadatok['vendegudvozlo'];
            $kerdesszam = $vizsgaadatok['kerdesszam'];
            $minimumhelyes = $vizsgaadatok['minimumhelyes'];
            $vizsgaido = $vizsgaadatok['vizsgaido'];
            $ismetelheto = $vizsgaadatok['ismetelheto'];
            $maxismetles =  $vizsgaadatok['maxismetles'];
            $leiras =  $vizsgaadatok['leiras'];
            $lablecszoveg =  $vizsgaadatok['lablec'];
            $fejleckep =  $vizsgaadatok['fejleckep'];
            $korlatozott = $vizsgaadatok['korlatozott'];
            $vizsgaeles = $vizsgaadatok['eles'];
            $kiertmutat = $vizsgaadatok['kiertmutat'];
            $vizsgabeallitasurl = "$RootPath/vizsga/" . $vizsgaadatok['url'] . "/vizsgabeallitasok?action=update";
            if($contextmenujogok['ujkornyitas'])
            {
                $ujkorurl = "$RootPath/vizsga/" . $vizsgaadatok['url'] . "/vizsgabeallitasok?action=newround";
            }
        }
        $button = "Beállítások mentése";
        $irhat = true;
        $form = "modules/vizsgak/forms/beallitasform";
        $oldalcim = "Vizsga beállításai";
        $magyarazat = null;

        include('././templates/edit.tpl.php');
    }
}
?>