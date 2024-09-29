<?php

if(isset($irhat) && $irhat)
{
    purifyPost(true);

    //var_dump($_FILES["fejleckep"]["size"]);
    if(isset($_FILES["fejleckep"]) && $_FILES["fejleckep"]["size"] > 0)
    {        
        $fajlok = $_FILES["fejleckep"];
        $filetypes = array('.jpg', '.jpeg', '.png', '.bmp');
        $mediatype = array('image/jpeg', 'image/png', 'image/bmp');
        
        $gyokermappa = "./uploads/";
        $egyedimappa = "vizsgak/$vizsgaazonosito";

        $fajllista = fajlFeltoltes($fajlok, $filetypes, $mediatype, $gyokermappa, $egyedimappa);
    }

    if(isset($_GET['action']) && $_GET['action'] == "addnew")
    {
        $fajlid = null;
        if(@$fajllista)
        {
            $fajlid = $fajllista[0];
        }

        $vizsgabeallitas = new MySQLHandler('INSERT INTO vizsgak_vizsgak (nev, url, udvozloszoveg, vendegudvozlo, kerdesszam, minimumhelyes, vizsgaido, ismetelheto, maxismetles, leiras, lablec, fejleckep, korlatozott) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            array($_POST['nev'], $_POST['url'], $_POST['udvozloszoveg'], $_POST['vendegudvozlo'], $_POST['kerdesszam'], $_POST['minimumhelyes'], $_POST['vizsgaido'], $_POST['ismetelheto'], $_POST['maxismetles'], $_POST['leiras'], $_POST['lablec'], $fajlid, $_POST['korlatozott']));
        if(!$vizsgabeallitas->siker)
        {
            echo "<h2>A változás beküldése sikertelen!<br></h2>";
        }
        $vizsgaadatok['url'] = $_POST['url'];
        $last_id = $vizsgabeallitas->last_insert_id;

        $ujkor = new MySQLHandler("INSERT INTO vizsgak_vizsgakorok (vizsga, sorszam) VALUES(?, ?)",
            array($last_id, 1));
    }

    elseif(isset($_GET['action']) && $_GET['action'] == "update")
    {
        $vizsgaorig = new MySQLHandler("SELECT fejleckep FROM vizsgak_vizsgak WHERE id = ?", $vizsgaid);
        $vizsgaorig = $vizsgaorig->Bind($fajlid);
        if(!isset($_POST["keptorol"]) && !@$fajllista)
        {
            $vizsgaid = $_POST['vizsgaid'];
        }
        elseif(@$fajllista)
        {
            $fajlid = $fajllista[0];
        }

        if(isset($_POST['url']) && $_POST['url'] != "")
        {
            if($_POST['url'] != $vizsgaazonosito)
            {
                $vizsgaazonosito = $_POST['url'];
                $gyokermappa = "./uploads/";
                $egyedimappa = "vizsgak/$vizsgaazonosito";
                rename($gyokermappa . $vizsgaazonosito, $gyokermappa . $egyedimappa);
            }
        }

        $vizsgaDB = new MySQLHandler();
        $vizsgaDB->Prepare('UPDATE vizsgak_vizsgak SET nev=?, url=?, udvozloszoveg=?, vendegudvozlo=?, kerdesszam=?, minimumhelyes=?, vizsgaido=?, ismetelheto=?, maxismetles=?, leiras=?, fejleckep=?, eles=?, lablec=?, korlatozott=? WHERE id=?');
        $vizsgaDB->Run(array($_POST['nev'], $vizsgaazonosito, $_POST['udvozloszoveg'], $_POST['vendegudvozlo'], $_POST['kerdesszam'], $_POST['minimumhelyes'], $_POST['vizsgaido'], $_POST['ismetelheto'], $_POST['maxismetles'], $_POST['leiras'], $fajlid, $_POST['eles'], $_POST['lablec'], $_POST['korlatozott'], $_POST['vizsgaid']));

        if(!$vizsgaDB->siker)
        {
            echo "<h2>A viszga szerkesztése sikertelen!<br></h2>";
        }

        if(isset($_POST['ujornyitas']))
        {
            $vizsgaDB->Prepare("SELECT * FROM vizsgak_vizsgakorok WHERE vizsga = ? ORDER BY id DESC;");
            $vizsgaDB->Run($vizsgaid);
            $jelenkor = $vizsgaDB->Fetch();

            $lezardate = timeStampForSQL();
            $jelenkorid = $jelenkor['id'];
            $ujkorsorszam = $jelenkor['sorszam'] + 1;

            $vizsgaDB->Prepare("UPDATE vizsgak_vizsgakorok SET veg = ? WHERE id = ?");
            $vizsgaDB->Run(array($lezardate, $jelenkorid));
            
            $vizsgaDB->Prepare("INSERT INTO vizsgak_vizsgakorok (vizsga, sorszam) VALUES(?, ?)");
            $vizsgaDB->Run(array($vizsgaid, $ujkorsorszam));
        }
    }

    elseif(isset($_GET['action']) && $_GET['action'] == 'vizsgareset')
    {
        /*mySQLConnect("DELETE FROM `tesztvalaszok`;");
        mySQLConnect("DELETE FROM `kitoltesek`;");
        mySQLConnect("ALTER TABLE `kitoltesek` AUTO_INCREMENT = 1;");
        mySQLConnect("ALTER TABLE `tesztvalaszok` AUTO_INCREMENT = 1");*/
        //header("Location: $RootPath/beallitasok");
    }
}