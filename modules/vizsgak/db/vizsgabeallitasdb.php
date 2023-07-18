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
        
        $vizsgaazonosito = $_POST['url'];
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

        $stmt = $con->prepare('INSERT INTO vizsgak_vizsgak (nev, url, udvozloszoveg, vendegudvozlo, kerdesszam, minimumhelyes, vizsgaido, ismetelheto, maxismetles, leiras, fejleckep) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssssssss', $_POST['nev'], $_POST['url'], $_POST['udvozloszoveg'], $_POST['vendegudvozlo'], $_POST['kerdesszam'], $_POST['minimumhelyes'], $_POST['vizsgaido'], $_POST['ismetelheto'], $_POST['maxismetles'], $_POST['leiras'], $fajlid);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A változás beküldése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }

    elseif(isset($_GET['action']) && $_GET['action'] == "update")
    {
        if(!isset($_POST["keptorol"]) && !@$fajllista)
        {
            $vizsgaid = $_POST['vizsgaid'];
            $kep = mySQLConnect("SELECT fejleckep FROM vizsgak_vizsgak WHERE id = $vizsgaid");
            
            $fajlid = mysqli_fetch_assoc($kep)['fejleckep'];
        }
        elseif(@$fajllista)
        {
            $fajlid = $fajllista[0];
        }

        $stmt = $con->prepare('UPDATE vizsgak_vizsgak SET nev=?, url=?, udvozloszoveg=?, vendegudvozlo=?, kerdesszam=?, minimumhelyes=?, vizsgaido=?, ismetelheto=?, maxismetles=?, leiras=?, fejleckep=? WHERE id=?');
        $stmt->bind_param('sssssssssssi', $_POST['nev'], $_POST['url'], $_POST['udvozloszoveg'], $_POST['vendegudvozlo'], $_POST['kerdesszam'], $_POST['minimumhelyes'], $_POST['vizsgaido'], $_POST['ismetelheto'], $_POST['maxismetles'], $_POST['leiras'], $fajlid, $_POST['vizsgaid']);
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