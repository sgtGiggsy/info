<?php
if($_SESSION[getenv('SESSION_NAME').'jogosultsag'] == 0)
{
    echo "<h2>Az oldal kizárólag bejelentkezett felhasználók számára érhető el!</h2>";
}
else
{
    if(isset($_GET['id']))
    {
        $kitoltesid = $_GET['id'];
    }

    $teszteredmenyek = mySQLConnect("SELECT kerdesek.kerdes as kerdes, kerdesek.id as kerdesid, felhasznalo, felhasznalonev, nev, befejezett, kitoltesideje, valasz, valaszszoveg, valaszok.id as vid, valaszok.helyes as helyes
    FROM kerdesek
    	INNER JOIN valaszok ON kerdesek.id = valaszok.kerdes
        INNER JOIN tesztvalaszok ON kerdesek.id = tesztvalaszok.kerdes
        INNER JOIN kitoltesek ON tesztvalaszok.kitoltes = kitoltesek.id
        INNER JOIN felhasznalok ON kitoltesek.felhasznalo = felhasznalok.id
    WHERE kitoltesek.id = $kitoltesid
    ORDER BY tesztvalaszok.id;");

    $kiolvas = mysqli_fetch_assoc($teszteredmenyek);
    if(mysqli_num_rows($teszteredmenyek) < 1)
    {
        echo "<h2>Nem létező vizsgaazonosító!</h2>";
    }
    else
    {
        if(!($_SESSION[getenv('SESSION_NAME').'jogosultsag'] >= 10 || ($kiolvas['felhasznalo'] == $_SESSION[getenv('SESSION_NAME').'id'] && $kiolvas['befejezett'] == 1)))
        {
            echo "<h2>Csak a saját eredményei megnézésére van joga!</h2>";
        }
        else
        {
            $helyes = 0;
            $sorsz = 0;
            echo "<div class='oldalcim'>" . $kiolvas['nev'] . "<p style='font-size:medium'> (" . $kiolvas['felhasznalonev'] . ")</p></div>";
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
            if($helyes < $_SESSION[getenv('SESSION_NAME').'minimumhelyes'])
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