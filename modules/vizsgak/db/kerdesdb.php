<?php

if(!isset($_SESSION[getenv('SESSION_NAME').'id']))
{
    echo "<h2>Nincs bejelentkezett felhasználó!</h2>" ?>
    <head><meta http-equiv="refresh" content="1; URL='?page=fooldal'" /></head><?php
}
else
{
    if(!(isset($_SESSION[getenv('SESSION_NAME').'jogosultsag']) && $_SESSION[getenv('SESSION_NAME').'jogosultsag'] > 10))
    {
        echo "<h2>Nincs jogosultságod a kérdések szerkesztésére!</h2>" ?>
        <head><meta http-equiv="refresh" content="1; URL='?page=fooldal'" /></head><?php
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

        $fajlnev = null;

        if(isset($_FILES["kerdeskep"]))
        {
            $filetypes = array('.jpg', '.jpeg', '.png', '.bmp');
            $mediatype = array('image/jpeg', 'image/png', 'image/bmp');

            $fajl = $_FILES["kerdeskep"];
            //print_r($fajl);
            if (!in_array($fajl['type'], $mediatype))
            {
                $uzenet = "A fájl típusa nem megengedett: " . $fajl['name'];
            }
            else
            {
                $fajlnev = strtolower(str_replace(".", time() . ".", $fajl['name']));
                $finalfile = $UPLOAD_FOLDER.$fajlnev;
                if(file_exists($finalfile))
                {
                    $uzenet = "A feltölteni kívánt fájl már létezik: " . $fajlnev;
                }
                else
                {
                    move_uploaded_file($fajl['tmp_name'], $finalfile);
                    $uzenet = 'A fájl feltöltése sikeresen megtörtént: ' . $fajlnev;
                }
            }
/*
            if(!isset($errorcount))
            {
                ?><h2><?=$uzenet?></h2><?php
            }
*/
        }

        if($_GET["action"] == "new")
        {
            if ($stmt = $con->prepare('INSERT INTO kerdesek (kerdes, letrehozo, kep) VALUES (?, ?, ?)'))
            {
                $stmt->bind_param('sss', $_POST['kerdes'], $_SESSION[getenv('SESSION_NAME').'id'], $fajlnev);
                $stmt->execute();
                if(mysqli_errno($con) != 0)
                {
                    echo "<h2>A kérdés hozzáadása sikertelen!<br></h2>";
                    echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                }
                else
                {
                    $query = mySQLConnect("SELECT id FROM kerdesek ORDER BY id DESC LIMIT 1");
                    $kerdes = mysqli_fetch_assoc($query);
                    $kerdesid = $kerdes['id'];
                    $i = 1;
                    while(isset($_POST["valasz$i"]) && $_POST["valasz$i"] != null)
                    {
                        $helyes = null;
                        if($i == $_POST["helyes"])
                        {
                            $helyes = 1;
                        }
                        if ($stmt = $con->prepare('INSERT INTO valaszok (kerdes, valaszszoveg, helyes) VALUES (?, ?, ?)'))
                        {
                            $stmt->bind_param('sss', $kerdesid, $_POST["valasz$i"], $helyes);
                            $stmt->execute();
                            if(mysqli_errno($con) != 0)
                            {
                                echo "<h2>A válasz hozzáadása sikertelen!<br></h2>";
                                echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                            }
                        }
                        else
                        {
                            echo "<h2>A válasz hozzáadása sikertelen!<br></h2>";
                            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                        }
                        $i++;
                    }
                    echo "<h2>A kérdés hozzáadása sikeres</h2>";
                    ?><head><meta http-equiv="refresh" content="1; URL='<?=$RootPath?>/kerdeslista'" /></head><?php
                }
            }
            else
            {
                echo "<h2>A kérdés hozzáadása sikertelen!<br></h2>";
                echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
            }
        }
        elseif($_GET["action"] == "update")
        {
            $timestamp = date('Y-m-d H:i:s');

            if(!isset($_POST["keptorol"]) && !$fajlnev)
            {
                $id = $_POST['id'];
                $kep = mySQLConnect("SELECT kep FROM kerdesek WHERE id = $id");
                
                $fajlnev = mysqli_fetch_assoc($kep)['kep'];
            }

            if ($stmt = $con->prepare('UPDATE kerdesek SET kerdes=?, modosito=?, modositasideje=?, kep=? WHERE id=?'))
            {
                $stmt->bind_param('ssssi', $_POST['kerdes'], $_SESSION[getenv('SESSION_NAME').'id'], $timestamp, $fajlnev, $_POST['id']);
                $stmt->execute();
                if(mysqli_errno($con) != 0)
                {
                    echo "<h2>A kérdés szerkesztése sikertelen!<br></h2>";
                    echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                }
                else
                {
                    $i = 1;
                    while(isset($_POST["valasz$i"]))
                    {
                        $helyes = null;
                        if($_POST["helyes"] == $_POST["vid$i"])
                        {
                            $helyes = 1;
                        }
                        if ($stmt = $con->prepare('UPDATE valaszok SET valaszszoveg=?, helyes=? WHERE id=?'))
                        {
                            $stmt->bind_param('ssi', $_POST["valasz$i"], $helyes, $_POST["vid$i"]);
                            $stmt->execute();
                            if(mysqli_errno($con) != 0)
                            {
                                echo "<h2>A válasz hozzáadása sikertelen!<br></h2>";
                                echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                            }
                        }
                        else
                        {
                            echo "<h2>A válasz hozzáadása sikertelen!<br></h2>";
                            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                        }
                        $i++;
                    }
                    
                    echo "<h2>A kérdés szerkesztése sikeres</h2>";
                    ?><head><meta http-equiv="refresh" content="1; URL='<?=$RootPath?>/kerdeslista'" /></head><?php
                }
            }
            else
            {
                echo "<h2>A kérdés szerkesztése sikertelen!<br></h2>";
                echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
            }
        }
        elseif($_GET["action"] == "delete")
        {
        }
    }
}
?>