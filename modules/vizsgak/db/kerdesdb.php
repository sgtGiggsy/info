<?php

if(!$irhat)
{
    getPermissionError();
}
else
{
    $con = mySQLConnect(false);

    purifyPost();
    
    if(isset($_FILES["kerdeskep"]))
    {        
        $fajlok = $_FILES["kerdeskep"];
        $filetypes = array('.jpg', '.jpeg', '.png', '.bmp');
        $mediatype = array('image/jpeg', 'image/png', 'image/bmp');

        $gyokermappa = "./uploads/";
        $egyedimappa = "vizsgak/$vizsgaazonosito/kerdeskepek";

        $fajllista = fajlFeltoltes($fajlok, $filetypes, $mediatype, $gyokermappa, $egyedimappa);
    }

    $helyesdb = count($_POST['helyes']);
    $helyesertek = 1 / $helyesdb;

    if($_GET["action"] == "addnew")
    {
        if(isset($_POST["keptorol"]) || !@$fajllista)
        {
            $fajlid = null;
        }
        elseif($fajllista)
        {
            $fajlid = $fajllista[0];
        }
        
        $stmt = $con->prepare('INSERT INTO vizsgak_kerdesek (vizsga, kerdes, letrehozo, kep) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $vizsgaid, $_POST['kerdes'], $felhasznaloid, $fajlid);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A kérdés hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            $kerdesid = mysqli_insert_id($con);

            $valasszam = count($_POST['valasz']);
            for($i = 1; $i <= $valasszam; $i++)
            {
                $helyes = null;
                if(isset($_POST['helyes'][$i]) && $_POST['helyes'][$i] == $i)
                {
                    $helyes = $helyesertek;
                }
                
                $stmt = $con->prepare('INSERT INTO vizsgak_valaszlehetosegek (kerdes, valaszszoveg, helyes) VALUES (?, ?, ?)');
                $stmt->bind_param('sss', $kerdesid, $_POST['valasz'][$i-1], $helyes);
                $stmt->execute();
                if(mysqli_errno($con) != 0)
                {
                    echo "<h2>A válasz hozzáadása sikertelen!<br></h2>";
                    echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                }
            }
        }
    }

    elseif($_GET["action"] == "update")
    {
        $timestamp = date('Y-m-d H:i:s');
        
        if(isset($_POST["keptorol"]) || !@$fajllista)
        {
            $fajlid = null;
        }
        elseif($fajllista)
        {
            $fajlid = $fajllista[0];
        }

        $stmt = $con->prepare('UPDATE vizsgak_kerdesek SET kerdes=?, modosito=?, modositasideje=?, kep=? WHERE id=?');
        $stmt->bind_param('ssssi', $_POST['kerdes'], $felhasznaloid, $timestamp, $fajlid, $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A kérdés szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }

        $valasszam = count($_POST['valasz']);
        $kerdesid = $_POST['id'];
        for($i = 1; $i <= $valasszam; $i++)
        {
            $helyes = null;
            if(isset($_POST['helyes'][$i]))
            {
                $helyes = $helyesertek;
            }
            
            $stmt = $con->prepare('UPDATE vizsgak_valaszlehetosegek SET valaszszoveg=?, helyes=? WHERE id=?');
            $stmt->bind_param('ssi', $_POST['valasz'][$i-1], $helyes, $_POST['vid'][$i]);
            $stmt->execute();
            if(mysqli_errno($con) != 0)
            {
                echo "<h2>A válasz hozzáadása sikertelen!<br></h2>";
                echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
            }
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
}
?>