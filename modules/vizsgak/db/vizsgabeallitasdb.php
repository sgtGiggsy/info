<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);
    purifyPost(true);

    if(isset($_FILES["fejleckep"]))
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

        $stmt = $con->prepare('INSERT INTO vizsgak_vizsgak (nev, url, udvozloszoveg, vendegudvozlo, kerdesszam, minimumhelyes, vizsgaido, ismetelheto, maxismetles, leiras, lablec, fejleckep, korlatozott) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssssssssss', $_POST['nev'], $_POST['url'], $_POST['udvozloszoveg'], $_POST['vendegudvozlo'], $_POST['kerdesszam'], $_POST['minimumhelyes'], $_POST['vizsgaido'], $_POST['ismetelheto'], $_POST['maxismetles'], $_POST['leiras'], $_POST['lablec'], $fajlid, $_POST['korlatozott']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A változás beküldése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }

    elseif(isset($_GET['action']) && $_GET['action'] == "update")
    {
        $vizsgaorig = mySQLConnect("SELECT fejleckep FROM vizsgak_vizsgak WHERE id = $vizsgaid");
        if(!isset($_POST["keptorol"]) && !@$fajllista)
        {
            $vizsgaid = $_POST['vizsgaid'];
            $fajlid = mysqli_fetch_assoc($vizsgaorig)['fejleckep'];
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

        $stmt = $con->prepare('UPDATE vizsgak_vizsgak SET nev=?, url=?, udvozloszoveg=?, vendegudvozlo=?, kerdesszam=?, minimumhelyes=?, vizsgaido=?, ismetelheto=?, maxismetles=?, leiras=?, fejleckep=?, eles=?, lablec=?, korlatozott=? WHERE id=?');
        $stmt->bind_param('ssssssssssssssi', $_POST['nev'], $vizsgaazonosito, $_POST['udvozloszoveg'], $_POST['vendegudvozlo'], $_POST['kerdesszam'], $_POST['minimumhelyes'], $_POST['vizsgaido'], $_POST['ismetelheto'], $_POST['maxismetles'], $_POST['leiras'], $fajlid, $_POST['eles'], $_POST['lablec'], $_POST['korlatozott'], $_POST['vizsgaid']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A viszga szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }

        if(isset($_POST['ujornyitas']))
        {
            $jelenkor = mySQLConnect("SELECT * FROM vizsgak_vizsgakorok WHERE vizsga = $vizsgaid ORDER BY id DESC;");
            $jelenkor = mysqli_fetch_assoc($jelenkor);

            $lezardate = timeStampForSQL();
            $jelenkorid = $jelenkor['id'];
            $ujkorsorszam = $jelenkor['sorszam'] + 1;

            mySQLConnect("UPDATE vizsgak_vizsgakorok SET veg = '$lezardate' WHERE id = $jelenkorid;");
            mySQLConnect("INSERT INTO vizsgak_vizsgakorok (vizsga, sorszam) VALUES($vizsgaid, $ujkorsorszam);");
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