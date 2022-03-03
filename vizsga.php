<?php
if($_SESSION[getenv('SESSION_NAME').'jogosultsag'] == 0)
{
    echo "<h2>Az oldal kizárólag bejelentkezett felhasználók számára érhető el!</h2>";
}
else
{
    ?><div class="oldalcim">Vizsgázás</div><?php
    if(isset($_SESSION[getenv('SESSION_NAME').'id']))
    {
        $uid = $_SESSION[getenv('SESSION_NAME').'id'];
        $korabbikitoltesek = mySQLConnect("SELECT * FROM kitoltesek WHERE felhasznalo = $uid ORDER BY id DESC");
    }

    if(!isset($_SESSION[getenv('SESSION_NAME').'vizsga']))
    {
        if(mysqli_num_rows($korabbikitoltesek) > 0)
        {
            $vizsga = mysqli_fetch_assoc($korabbikitoltesek);
            $_SESSION[getenv('SESSION_NAME').'vizsga'] = $vizsga['id'];
            $ujvizsga = false;
        }
        else
        {
            $ujvizsga = true;
        }

        if($ujvizsga)
        {
            echo "<form action='?page=vizsgadb&action=new' method='post'>";
            echo "<div class='submit'><input type='submit' value='Vizsga megkezdése'></div>";
            echo "</form>";
        }
    }

    if(isset($_SESSION[getenv('SESSION_NAME').'vizsga']))
    {   
        if(isset($korabbikitoltesek))
        {
            if(!isset($vizsga))
            {
                $vizsga = mysqli_fetch_assoc($korabbikitoltesek);
            }
            if($vizsga['befejezett'] == 1)
            {
                echo "<h2>Korábbi kitöltések</h2>";
                foreach($korabbikitoltesek as $x)
                {
                    ?><a href= './vizsgareszletezo/<?=$x['id']?>'><?=$x['kitoltesideje']?></a><?php
                }
            }
        }
        
        $vizsga = $_SESSION[getenv('SESSION_NAME').'vizsga'];
        $con = mySQLConnect("SELECT * FROM kitoltesek WHERE id = $vizsga LIMIT 1");
        $vizsgaallapot = mysqli_fetch_assoc($con);
        if($vizsgaallapot['befejezett'] == 1)
        {
            if($_SESSION[getenv('SESSION_NAME').'ismetelheto'])
            {
                $con = mySQLConnect("SELECT felhasznalok.id
                FROM kitoltesek LEFT JOIN felhasznalok ON kitoltesek.felhasznalo = felhasznalok.id
                WHERE felhasznalok.id = $uid;");
                if(mysqli_num_rows($con) >= $_SESSION[getenv('SESSION_NAME').'maxismetles'])
                {
                    echo "<h2>Ön mind az 5 vizsgalehetőségét felhasználta. A vizsgateszt újabb kitöltése nem lehetséges.</h2>";
                }
                else
                {
                    $lehetosegszam = $_SESSION[getenv('SESSION_NAME').'maxismetles'] - mysqli_num_rows($con);
                    echo "<h2>Újrapróbálhatja a vizsgát még $lehetosegszam alkalommal</h2>";
                    echo "<form action='?page=vizsgadb&action=new' method='post'>";
                    echo "<div class='submit'><input type='submit' value='Vizsga megismétlése'></div>";
                    echo "</form>";
                }
            }
        }
        else
        {
            if(!isset($_SESSION[getenv('SESSION_NAME').'kerdessorszam']))
            {
                $_SESSION[getenv('SESSION_NAME').'kerdessorszam'] = 1;
            }

            $nextout = false;
            if(isset($_GET['kerdes']))
            {
                $megvalaszoltquery = mySQLConnect("SELECT * FROM tesztvalaszok WHERE kitoltes = $vizsga ORDER BY id ASC");
                $sorsz = 0;
                foreach($megvalaszoltquery as $x)
                {
                    if($nextout)
                    {
                        $kovetkezo = $x['id'];
                        $nextout = false;
                        break;
                    }
                    if($_GET['kerdes'] == $x['id'])
                    {
                        $nextout = true;
                    }
                    $sorsz++;
                }
                $_SESSION[getenv('SESSION_NAME').'kerdessorszam'] = $sorsz;
            }

            $megvalaszoltquery = mySQLConnect("SELECT * FROM tesztvalaszok WHERE kitoltes = $vizsga ORDER BY id DESC");
            $osszvalaszszam = mysqli_num_rows($megvalaszoltquery);
            echo "<div>";
            if($_SESSION[getenv('SESSION_NAME').'kerdessorszam'] > 1)
            {
                if(!isset($_GET['kerdes']))
                {
                    $elozo = (mysqli_fetch_assoc($megvalaszoltquery))['id'];
                }
                else
                {
                    $nextbreak = false;
                    foreach($megvalaszoltquery as $x)
                    {
                        if($nextbreak)
                        {
                            $elozo = $x['id'];
                            break;
                        }
                        if($_GET['kerdes'] == $x['id'])
                        {
                            $nextbreak = true;
                        }
                    }
                }
                ?><div class='left'><a href= './vizsga?kerdes=<?=$elozo?>'>Előző kérdés</a></div><?php
            }
            
            if(isset($_GET['kerdes']))
            {
                echo "<div class='right'>";
                if($nextout)
                {
                    ?><a href= './vizsga'>Következő kérdés</a><?php
                }
                else
                {
                    ?><a href= './vizsga?kerdes=<?=$kovetkezo?>'>Következő kérdés</a><?php
                }
                echo "</div>";
            }
            echo "</div>";

            if(!isset($_GET['kerdes']))
            {
                $_SESSION[getenv('SESSION_NAME').'kerdessorszam'] = $osszvalaszszam + 1;
            }
            
            ?>
            <h2><?=$_SESSION[getenv('SESSION_NAME').'kerdessorszam']?>/<?=$_SESSION[getenv('SESSION_NAME').'vizsgahossz']?> kérdés</h2>
            <?php
            if(isset($_SESSION[getenv('SESSION_NAME').'kerdesid']))
            {
                $kerdesid = $_SESSION[getenv('SESSION_NAME').'kerdesid'];
            }
            else
            {
                $tesztvalaszok = mySQLConnect("SELECT * FROM tesztvalaszok WHERE kitoltes = $vizsga ORDER BY kerdes ASC");
                $_SESSION[getenv('SESSION_NAME').'kerdessorszam'] = $osszvalaszszam + 1;
                $kerdesek = mySQLConnect("SELECT id FROM kerdesek");
                $osszkerdesszam = mysqli_num_rows($kerdesek);
                $randomize = $osszkerdesszam - $osszvalaszszam;
                $rand = rand(1, $randomize);
                $talalatszam = 1;

                foreach($kerdesek as $x)
                {
                    $talalat = false;
                    foreach($tesztvalaszok as $y)
                    {
                        if($y['kerdes'] == $x['id'])
                        {
                            $talalat = true;
                            break;
                        }
                        elseif($y['kerdes'] > $x['id'])
                        {
                            break;
                        }
                    }
                    if(!$talalat)
                    {
                        if($talalatszam == $rand)
                        {
                            $kerdesid = $x['id'];
                            break;
                        }
                        else
                        {
                            $talalatszam++;
                        }
                    }
                }
                $_SESSION[getenv('SESSION_NAME').'kerdesid'] = $kerdesid;
            }

            $utolso = false;
            if($_SESSION[getenv('SESSION_NAME').'kerdessorszam'] == $_SESSION[getenv('SESSION_NAME').'vizsgahossz'])
            {
                $utolso = true;
            }
            
            include("kerdes.php");
        }
    }
}