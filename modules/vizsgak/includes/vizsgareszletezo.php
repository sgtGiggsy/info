<?php
// Egyelőre kész
if(!$felhasznaloid)
{
    echo "<h2>Az oldal kizárólag bejelentkezett felhasználók számára érhető el!</h2>";
}
else
{
    if(isset($_GET['id']))
    {
        $kitoltesid = $_GET['id'];
    }

    $teszteredmenyek = mySQLConnect("SELECT vizsgak_kerdesek.kerdes as kerdes,
        vizsgak_kerdesek.id as kerdesid,
        felhasznalo,
        felhasznalonev,
        nev,
        befejezett,
        kitoltesideje,
        valasz,
        valaszszoveg,
        vizsgak_valaszlehetosegek.id AS vid,
        vizsgak_valaszlehetosegek.helyes AS helyes
    FROM vizsgak_kerdesek
    	INNER JOIN vizsgak_valaszlehetosegek ON vizsgak_kerdesek.id = vizsgak_valaszlehetosegek.kerdes
        INNER JOIN vizsgak_kitoltesvalaszok ON vizsgak_kerdesek.id = vizsgak_kitoltesvalaszok.kerdes
        INNER JOIN vizsgak_kitoltesek ON vizsgak_kitoltesvalaszok.kitoltes = vizsgak_kitoltesek.id
        INNER JOIN felhasznalok ON vizsgak_kitoltesek.felhasznalo = felhasznalok.id
    WHERE vizsgak_kitoltesek.id = $kitoltesid
    ORDER BY vizsgak_kitoltesvalaszok.id;");

    $kiolvas = mysqli_fetch_assoc($teszteredmenyek);
    if(mysqli_num_rows($teszteredmenyek) < 1)
    {
        echo "<h2>Nem létező vizsgaazonosító!</h2>";
    }
    else
    {
        if(!($contextmenujogok['admin'] || ($kiolvas['felhasznalo'] == $felhasznaloid && $kiolvas['befejezett'] == 1)))
        {
            echo "<h2>Csak a saját, befejezett eredményei megtekintésére van jogosultsága!</h2>";
        }
        else
        {
            $helyes = 0;
            $sorsz = 0;
            echo "<div class='oldalcim'>" . $kiolvas['nev'] . "<span style='font-size:medium'> (" . $kiolvas['felhasznalonev'] . ")</span></div>";
            echo "<div class='contentcenter'>";
            echo "<h4>" . $kiolvas['kitoltesideje'] . "</h4>";
            foreach($teszteredmenyek as $x)
            {
                if(!isset($kerdesid) || $x['kerdesid'] != $kerdesid)
                {
                    $sorsz++;
                    echo "<h3>" . $sorsz . ". " . $x['kerdes'] . "</h3>";
                }  
                if($x['helyes'] == 1)
                {
                    if($x['vid'] == $x['valasz'])
                    {
                        $helyes++;
                        echo "<p style='color: green; font-weight:bold'>";
                    }
                    else
                    {
                        echo "<p style='color: green'>";
                    }
                }
                else
                {
                    if($x['vid'] == $x['valasz'])
                    {
                        echo "<p style='color: red'>";
                    }
                    else
                    {
                        echo "<p>";
                    }
                }
                echo $x['valaszszoveg'] . "</p>";
                
                $kerdesid = $x['kerdesid'];
            }
            if($helyes < $vizsgaadatok['minimumhelyes'])
            {
                echo "<br><h1 style='color:red'>Sikertelen!<br>" . $helyes . "/" . $sorsz . "</h1>";
            }
            else
            {
                echo "<br><h1 style='color:green'>Sikeres<br>" . $helyes . "/" . $sorsz . "</h1>";
            }
            ?></div><?php
        }
    }
}
?>