<?php

$qs = 'SELECT szervezet
    FROM feladatterv_feladatok INNER JOIN felhasznalok ON feladatterv_feladatok.felvitte = felhasznalok.id
    WHERE feladatterv_feladatok = ?';
$irhat = isVerifiedToWrite($qs, $szervezet, 'szervezet');

if($irhat && isset($_GET['action']))
{
    $feladatdb = new mySQLHandler();
    $feladatdb->KeepAlive();
    $timestamp = timeStampForSQL();
    $targetid = null;
    
    if(isset($_POST['leiras']))
        $leirasHTML = $_POST['leiras'];
    purifyPost();

    if($_GET["action"] == "new")
    {
        $feladatdb->Query('INSERT INTO feladatterv_feladatok (rovid, leiras, prioritas, szulo, szakid, epulet, felvitte, ido_tervezett, ido_hatarido) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            $_POST['rovid'], $leirasHTML, $_POST['prioritas'], $_POST['szulo'], $_POST['szakid'], $_POST['epulet'], $_SESSION['id'], $_POST['ido_tervezett'], $_POST['ido_hatarido']);

        $feladatid = $feladatdb->last_insert_id;
        $targetid = ($_POST['szulo']) ? $_POST['szulo'] : $feladatid;
    }

    elseif($_GET["action"] == "update")
    {
        $feladatid = $_POST['id'];
        $feladatdb->Query('UPDATE feladatterv_feladatok SET rovid=?, leiras=?, prioritas=?, szakid=?, epulet=?, modositotta=?, ido_tervezett=?, ido_hatarido=?, ido_modositas=? WHERE feladat_id=?',
            $_POST['rovid'], $leirasHTML, $_POST['prioritas'], $_POST['szakid'], $_POST['epulet'], $_SESSION['id'], $_POST['ido_tervezett'], $_POST['ido_hatarido'], $timestamp, $feladatid);
    }

    elseif($_GET["action"] == "delete")
    {
        $feladatid = $_GET['id'];
        $feladatdb->Query('UPDATE feladatterv_feladatok SET aktiv = 0 WHERE feladat_id=?', $feladatid);
    }

    elseif($_GET["action"] == "stateupdate" && isset($_GET["state"]))
    {
        //TODO Kibővíteni ellenőrzésekkel: ha kész és főfeladat, minden folyamatban lévő, vagy megkezdett folyamat készre állítása (a sikertelen állapotúak nem)
        //TODO Módosítás/tényleges végrehajtás idejének hozzáadása

        $feladatid = $_GET['id'];
        $feladatdb->Query('UPDATE feladatterv_feladatok SET allapot=?, ido_tenyleges=? WHERE feladat_id=?', $_GET["state"], $timestamp, $feladatid);
    }

    elseif($_GET["action"] == "komment")
    {
        $feladatid = $_GET['id'];
        $feladatdb->Query('INSERT INTO feladatterv_kommentek (felhasznalo_id, feladat_id, szoveg) VALUES (?, ?, ?)', $_SESSION['id'], $feladatid, $_POST['szoveg']);
    }

    if($_GET["action"] != "new")
    {
        $feladatdb->Query('SELECT szulo FROM feladatterv_feladatok WHERE feladat_id = ?', $feladatid);
        $targetid = $feladatdb->Fetch()['szulo'];
        $targetid = ($targetid) ? $targetid : $_GET['id'];
    }

    if(isset($_POST['felelosok']) && $_POST['felelosok'])
    {
        $feladatdb->Query('DELETE FROM feladatterv_felelosok WHERE feladat_id = ?', $feladatid);
        
        $feladatdb->Prepare('INSERT INTO feladatterv_felelosok (felhasznalo_id, feladat_id) VALUES (?, ?)');
        
        foreach($_POST['felelosok'] as $felelos)
        {
            $feladatdb->Run($felelos, $feladatid);
        }
    }

    if(isset($_FILES["fajlok"]))
    {        
        $fajlok = $_FILES["fajlok"];
        $filetypes = array('.jpg', '.jpeg', '.png', '.bmp', 'doc', 'docx', 'xls', 'xlsx', 'pdf', 'zip', 'rar');
        $mediatype = array('image/jpeg', 'image/png',
            'image/bmp', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/pdf',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
            'application/x-zip-compressed',
            'application/vnd.rar');

        $gyokermappa = "./uploads/";
        $egyedimappa = "feladattervek/$feladatid";

        $fajllista = fajlFeltoltes($fajlok, $filetypes, $mediatype, $gyokermappa, $egyedimappa);

        $feladatdb->Prepare('INSERT INTO feladatterv_fajlok (feladat_id, feltoltes_id, felhasznalo_id) VALUES (?, ?, ?)');
        foreach($fajllista as $fajl)
        {
            $feladatdb->Run($feladatid, $fajl, $_SESSION['id']);
        }
    }

    $felurl = $RootPath . "/feladatterv/" . $targetid;
    $feladatdb->Close($felurl);
}