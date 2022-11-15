<?php

if(isset($felhasznaloid))
{
    $con = mySQLConnect(false);
    $timestamp = date('Y-m-d H:i:s');

    if($_GET["action"] == "checkednotif")
    {
        $stmt = $con->prepare('UPDATE felhasznalok SET lastseennotif=? WHERE id=?');
        $stmt->bind_param('si', $timestamp, $felhasznaloid);
        $stmt->execute();
    }
    elseif($_GET["action"] == "seennotif")
    {
        $latta = 1;
        $stmt = $con->prepare('UPDATE ertesites_megjelenik SET latta=? WHERE felhasznalo=? AND ertesites=?');
        $stmt->bind_param('iii', $latta, $felhasznaloid, $_GET['notifid']);
        $stmt->execute();
    }
    elseif($_GET["action"] == "seenallnotif")
    {
        $latta = 1;
        $stmt = $con->prepare('UPDATE ertesites_megjelenik SET latta=? WHERE felhasznalo=?');
        $stmt->bind_param('ii', $latta, $felhasznaloid);
        $stmt->execute();
    }
}