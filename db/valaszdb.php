<?php
if(!isset($_SESSION[getenv('SESSION_NAME').'id']))
{
    echo "<h2>Nincs bejelentkezett felhasználó!</h2>" ?>
    <head><meta http-equiv="refresh" content="1; URL='<?=$RootPath?>'" /></head><?php
}
else
{
    $con = mySQLConnect(false);
    foreach($_POST as $key => $value)
    {
        if ($value == "NULL")
        {
            $_POST[$key] = NULL;
        }
    }

    if($_GET["action"] == "new")
    {
        if ($stmt = $con->prepare('INSERT INTO tesztvalaszok (kitoltes, kerdes, valasz) VALUES (?, ?, ?)'))
        {
            $stmt->bind_param('sss', $_SESSION[getenv('SESSION_NAME').'vizsga'], $_SESSION[getenv('SESSION_NAME').'kerdesid'], $_POST['valasz']);
            $stmt->execute();
            if(mysqli_errno($con) != 0)
            {
                echo "<h2>A válasz beküldése sikertelen!<br></h2>";
                echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
            }
            else
            {
                //echo "<h2>A válasz beküldése sikeres</h2>";
                unset($_SESSION[getenv('SESSION_NAME').'kerdesid']);
                $_SESSION[getenv('SESSION_NAME').'kerdessorszam']++;
                if($_SESSION[getenv('SESSION_NAME').'lezar'])
                {
                    $vizsgaid = $_SESSION[getenv('SESSION_NAME').'vizsga'];
                    // hashgyártás
                    $hashgyart = mySQLConnect("SELECT kerdes, kitoltes, valasz, felhasznalonev, kitoltesideje
                    FROM tesztvalaszok
                        INNER JOIN kitoltesek ON tesztvalaszok.kitoltes = kitoltesek.id
                        INNER JOIN felhasznalok ON kitoltesek.felhasznalo = felhasznalok.id
                    WHERE kitoltesek.id = $vizsgaid
                    ORDER BY tesztvalaszok.id;");

                    $hashalap = null;
                    foreach($hashgyart as $x)
                    {
                        if(!$hashalap)
                        {
                            $hashalap .= $x['felhasznalonev'];
                            $hashalap .= $x['kitoltesideje'];
                            $hashalap .= "|";
                            $hashalap .= $x['kitoltes'];
                        }
                        $hashalap .= "|";
                        $hashalap .= $x['kerdes'];
                        $hashalap .= "/";
                        $hashalap .= $x['valasz'];
                    }
                    $hash = hash('md5', $hashalap);
                    
                    mySQLConnect("UPDATE kitoltesek SET befejezett = 1, vizsgakod = '$hashalap', hash = '$hash' WHERE id = $vizsgaid");
                    unset($_SESSION[getenv('SESSION_NAME').'vizsga']);
                    unset($_SESSION[getenv('SESSION_NAME').'kerdessorszam']);
                    echo "<h2>A vizsga lezárult, továbbirányítjuk a kiértékeléshez</h2>";
                    ?><head><meta http-equiv="refresh" content="2; URL='<?=$RootPath?>/vizsgareszletezo/<?=$vizsgaid?>'" /></head><?php
                }
                else
                {
                    header("Location: $RootPath/vizsga");
                }
            }
        }
        else
        {
            echo "<h2>A válasz beküldése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    elseif($_GET["action"] == "update")
    {
        $kerdes = $_SESSION[getenv('SESSION_NAME').'valaszszerkeszt'];
        if ($stmt = $con->prepare('UPDATE tesztvalaszok SET valasz=? WHERE id=?'))
        {
            $stmt->bind_param('si', $_POST['valasz'], $_SESSION[getenv('SESSION_NAME').'valaszszerkeszt']);
            $stmt->execute();
            if(mysqli_errno($con) != 0)
            {
                echo "<h2>A válasz szerkesztése sikertelen!<br></h2>";
                echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
            }
            else
            {
                //echo "<h2>A válasz szerkesztése sikeres</h2>";
                header("Location: $RootPath/vizsga?kerdes=$kerdes");
            }
        }
        else
        {
            echo "<h2>A válasz szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        unset($_SESSION[getenv('SESSION_NAME').'valaszszerkeszt']);
    }
    elseif($_GET["action"] == "delete")
    {
    }
}

?>