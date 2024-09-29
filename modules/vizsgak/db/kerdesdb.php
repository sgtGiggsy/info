<?php

if(!$irhat)
{
    getPermissionError();
}
else
{
    $helyesertek = null;

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

    if(isset($_POST['helyes']))
    {
        $helyesdb = count($_POST['helyes']);
        $helyesertek = 1 / $helyesdb;
    }

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
        
        $kerdesek = new MySQLHandler('INSERT INTO vizsgak_kerdesek (vizsga, kerdes, letrehozo, kep) VALUES (?, ?, ?, ?)',
            array($vizsgaid, $_POST['kerdes'], $felhasznaloid, $fajlid));

        if(!$kerdesek->siker)
        {
            echo "<h2>A kérdés hozzáadása sikertelen!<br></h2>";
        }
        else
        {
            $kerdesid = $kerdesek->last_insert_id;
            $valasszam = count($_POST['valasz']);
            $valaszlehetosegek = new MySQLHandler();
            $valaszlehetosegek->Prepare('INSERT INTO vizsgak_valaszlehetosegek (kerdes, valaszszoveg, helyes) VALUES (?, ?, ?)');
            for($i = 1; $i <= $valasszam; $i++)
            {
                $helyes = null;
                if(isset($_POST['helyes'][$i]) && $_POST['helyes'][$i] == $i)
                {
                    $helyes = $helyesertek;
                }
                
                if($_POST['valasz'][$i-1])
                {
                    $valaszlehetosegek->Run(array($kerdesid, $_POST['valasz'][$i-1], $helyes));
                    if(!$valaszlehetosegek->siker)
                    {
                        echo "<h2>A válasz hozzáadása sikertelen!<br></h2>";
                    }
                }
            }
            $valaszlehetosegek->Close();
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

        $vizsgakerdesek = new MySQLHandler("UPDATE vizsgak_kerdesek SET kerdes=?, modosito=?, modositasideje=?, kep=? WHERE id=?",
            array($_POST['kerdes'], $felhasznaloid, $timestamp, $fajlid, $_POST['id']));
        
        if(!$vizsgakerdesek->siker)
        {
            echo "<h2>A kérdés szerkesztése sikertelen!<br></h2>";
        }

        $valasszam = count($_POST['valasz']);
        $kerdesid = $_POST['id'];
        $torol = new MySQLHandler();
        $torol->Prepare('DELETE FROM vizsgak_valaszlehetosegek WHERE id=?');
        $frissit = new MySQLHandler();
        $frissit->Prepare('UPDATE vizsgak_valaszlehetosegek SET valaszszoveg=?, helyes=? WHERE id=?');
        $beszur = new MySQLHandler();
        $beszur->Prepare('INSERT INTO vizsgak_valaszlehetosegek (kerdes, valaszszoveg, helyes) VALUES (?, ?, ?)');
        for($i = 1; $i <= $valasszam; $i++)
        {
            $helyes = null;
            if(isset($_POST['helyes'][$i]))
            {
                $helyes = $helyesertek;
            }
            if(isset($_POST['vid'][$i]))
            {
                if(isset($_POST['torol'][$i]))
                {
                    $torol->Run($_POST['torol'][$i]);
                }
                else
                {
                    $frissit->Run(array($_POST['valasz'][$i-1], $helyes, $_POST['vid'][$i]));
                    if(!$frissit->siker)
                    {
                        echo "<h2>A válasz hozzáadása sikertelen!<br></h2>";
                    }
                }
            }
            else
            {
                $beszur->Run(array($kerdesid, $_POST['valasz'][$i-1], $helyes));
                if(!$beszur->siker)
                {
                    echo "<h2>A válasz hozzáadása sikertelen!<br></h2>";
                }
            }
        }
        $beszur->Close();
        $frissit->Close();
        $torol->Close();
    }
    elseif($_GET["action"] == "delete")
    {
    }
}
?>