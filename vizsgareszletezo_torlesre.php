<div class="contentcenter">
<?php
$start_time = microtime(true);
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
   /* $teszteredmenyek = mySQLConnect("SELECT kerdesek.kerdes as kerdes, valaszok.helyes, valaszszoveg, felhasznalo, felhasznalonev, nev, befejezett, kitoltesideje, tesztvalaszok.valasz as valasz
    FROM kerdesek
    	INNER JOIN valaszok ON kerdesek.id = valaszok.kerdes
        INNER JOIN tesztvalaszok ON kerdesek.id = tesztvalaszok.kerdes
        INNER JOIN kitoltesek ON tesztvalaszok.kitoltes = kitoltesek.id
        INNER JOIN felhasznalok ON kitoltesek.felhasznalo = felhasznalok.id
    WHERE kitoltesek.id = $kitoltesid
    ORDER BY tesztvalaszok.id;");*/

    $teszteredmenyek = mySQLConnect("SELECT kerdesek.kerdes as kerdes, kerdesek.id as kerdesid, felhasznalo, felhasznalonev, nev, befejezett, kitoltesideje, valasz
    FROM kerdesek
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
            echo "<h2>Csak a saját eredményeid megnézésére van jogod!</h2>";
        }
        else
        {
            $helyes = 0;
            $sorsz = 0;
            echo "<h1>" . $kiolvas['nev'] . "</h1>";
            echo "<h4>(" . $kiolvas['felhasznalonev'] . ")&nbsp;&nbsp;&nbsp;&nbsp;" . $kiolvas['kitoltesideje'] . "</h4>";
            foreach($teszteredmenyek as $x)
            {
                $sorsz++;
                $kerdesid = $x['kerdesid'];
                echo "<h3>" . $sorsz . ". " . $x['kerdes'] . "</h3>";
                $valaszok = mySQLConnect("SELECT *
                FROM valaszok
                WHERE kerdes = $kerdesid
                ORDER BY valaszok.id;");
                foreach($valaszok as $y)
                {
                    if($y['helyes'] == 1)
                    {
                        if($y['id'] == $x['valasz'])
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
                        if($y['id'] == $x['valasz'])
                        {
                            echo "<p style='color: red'>";
                        }
                        else
                        {
                            echo "<p>";
                        }
                    }
                    echo $y['valaszszoveg'] . "</p>";
                }
            }
            if($helyes < $_SESSION[getenv('SESSION_NAME').'minimumhelyes'])
            {
                echo "<br><h1 style='color:red'>Sikertelen!<br>" . $helyes . "/" . $sorsz . "</h1>";
            }
            else
            {
                echo "<br><h1 style='color:green'>Sikeres<br>" . $helyes . "/" . $sorsz . "</h1>";
            } 
        }
    }
}
$end_time = microtime(true);
  
// Calculate script execution time
$execution_time = ($end_time - $start_time);
  
echo " Execution time of script = ".$execution_time." sec";
?>
</div>