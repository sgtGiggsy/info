<?php

if(isset($irhat) && $irhat)
{
    $feladatdb = new mySQLHandler();
    $feladatdb->KeepAlive();

    purifyPost();

    print_r($_POST);

    if($_GET["action"] == "new")
    {
        $feladatdb->Query('INSERT INTO feladatterv_feladatok (rovid, leiras, prioritas, szulo, szakid, epulet, felvitte, ido_tervezett, ido_hatarido) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            $_POST['rovid'], $_POST['leiras'], $_POST['prioritas'], $_POST['szulo'], $_POST['szakid'], $_POST['epulet'], $_SESSION['id'], $_POST['ido_tervezett'], $_POST['ido_hatarido']);

        $feladatid = $feladatdb->last_insert_id;
    }

    elseif($_GET["action"] == "update")
    {
        $feladatdb->Query('UPDATE feladatterv_feladatok SET rovid=?, leiras=?, prioritas=?, szakid=?, epulet=?, modositotta=?, ido_tervezett=?, ido_hatarido=? WHERE feladat_id=?',
            $_POST['rovid'], $_POST['leiras'], $_POST['prioritas'], $_POST['szakid'], $_POST['epulet'], $_SESSION['id'], $_POST['ido_tervezett'], $_POST['ido_hatarido'], $_POST['id']);
        $feladatid = $_POST['id'];
    }

    elseif($_GET["action"] == "delete")
    {
    }

    elseif($_GET["action"] == "komment")
    {
        $feladatdb->Query('INSERT INTO feladatterv_kommentek (felhasznalo_id, feladat_id, szoveg) VALUES (?, ?, ?)', $_SESSION['id'], $_GET['id'], $_POST['szoveg']);
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

    $feladatdb->Close($backtosender);
}