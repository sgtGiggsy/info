<?php

$qs = 'SELECT szervezet
    FROM feladatterv_feladatok INNER JOIN felhasznalok ON feladatterv_feladatok.felvitte = felhasznalok.id
    WHERE feladatterv_feladatok = ?';
$irhat = isVerifiedToWrite($qs, $szervezet, 'szervezet');

if($irhat)
{
    $feladatdb = new mySQLHandler();
    $feladatdb->KeepAlive();

    $timestamp = timeStampForSQL();
    
    if(isset($_POST['leiras']))
        $leirasHTML = $_POST['leiras'];
    purifyPost();

    if($_GET["action"] == "new")
    {
        $feladatdb->Query('INSERT INTO feladatterv_feladatok (rovid, leiras, prioritas, szulo, szakid, epulet, felvitte, ido_tervezett, ido_hatarido) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            $_POST['rovid'], $leirasHTML, $_POST['prioritas'], $_POST['szulo'], $_POST['szakid'], $_POST['epulet'], $_SESSION['id'], $_POST['ido_tervezett'], $_POST['ido_hatarido']);

        $feladatid = $feladatdb->last_insert_id;
    }

    elseif($_GET["action"] == "update")
    {
        $feladatdb->Query('UPDATE feladatterv_feladatok SET rovid=?, leiras=?, prioritas=?, szakid=?, epulet=?, modositotta=?, ido_tervezett=?, ido_hatarido=?, ido_modositas=? WHERE feladat_id=?',
            $_POST['rovid'], $leirasHTML, $_POST['prioritas'], $_POST['szakid'], $_POST['epulet'], $_SESSION['id'], $_POST['ido_tervezett'], $_POST['ido_hatarido'], $timestamp, $_POST['id']);
        $feladatid = $_POST['id'];
    }

    elseif($_GET["action"] == "delete")
    {
        $feladatdb->Query('UPDATE feladatterv_feladatok SET aktiv = 0 WHERE feladat_id=?', $_GET['id']);
    }

    elseif($_GET["action"] == "stateupdate" && isset($_GET["state"]))
    {
        //TODO Kibővíteni ellenőrzésekkel: ha kész és főfeladat, minden folyamatban lévő, vagy megkezdett folyamat készre állítása (a sikertelen állapotúak nem)
        //TODO Módosítás/tényleges végrehajtás idejének hozzáadása
        $feladatdb->Query('UPDATE feladatterv_feladatok SET allapot = ? WHERE feladat_id=?', $_GET["state"], $_GET['id']);
    }

    elseif($_GET["action"] == "komment")
    {
        $feladatdb->Query('INSERT INTO feladatterv_kommentek (felhasznalo_id, feladat_id, szoveg) VALUES (?, ?, ?)', $_SESSION['id'], $_GET['id'], $_POST['szoveg']);
    }

    //TODO Fájlfeltöltés megoldása

    if(isset($_POST['felelosok']) && $_POST['felelosok'])
    {
        $feladatdb->Query('DELETE FROM feladatterv_felelosok WHERE feladat_id = ?', $feladatid);
        
        $feladatdb->Prepare('INSERT INTO feladatterv_felelosok (felhasznalo_id, feladat_id) VALUES (?, ?)');
        
        foreach($_POST['felelosok'] as $felelos)
        {
            $feladatdb->Run($felelos, $feladatid);
        }
    }

    //TODO Jobb átiránytás.
    $feladatdb->Close($backtosender);
}