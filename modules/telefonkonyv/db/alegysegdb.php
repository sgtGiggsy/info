<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    $sorrend = $_POST['sorrend'];

    if($_GET["action"] == "new")
    {
        mySQLConnect("UPDATE telefonkonyvcsoportok SET sorrend = sorrend + 1 WHERE sorrend > $sorrend;");

        if(!verifyWholeNum($sorrend))
        {
            $sorrend = $sorrend + 0.5;
        }
        $stmt = $con->prepare('INSERT INTO telefonkonyvcsoportok (nev, sorrend) VALUES (?, ?)');
        $stmt->bind_param('ss', $_POST['nev'], $sorrend);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Alegység hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    elseif($_GET["action"] == "update")
    {
        $alegysid = $_POST['id'];

        if(!verifyWholeNum($sorrend))
        {
            $origsorrend = mySQLConnect("SELECT sorrend FROM telefonkonyvcsoportok WHERE id = $alegysid;");
            $origsorrend = mysqli_fetch_assoc($origsorrend)['sorrend'];
            mySQLConnect("UPDATE telefonkonyvcsoportok SET sorrend = sorrend + 1 WHERE sorrend > $sorrend AND torolve IS NULL;");
        }

        var_dump($sorrend);
        var_dump($_POST['nev']);
        var_dump($_POST['id']);
        
        $stmt = $con->prepare('UPDATE telefonkonyvcsoportok SET nev=?, sorrend=? WHERE id=?');
        $stmt->bind_param('ssi', $_POST['nev'], $sorrend, $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Alegység szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        elseif(isset($origsorrend) && $origsorrend)
        {
            mySQLConnect("UPDATE telefonkonyvcsoportok SET sorrend = sorrend - 1 WHERE sorrend > $origsorrend AND torolve IS NULL;");
        }
    }
    elseif($_GET["action"] == "delete")
    {
        $sorrend = $_POST['sorrend'];
        $torolve = 1;
        $nulled = null;
        if($mindir)
        {
            $stmt = $con->prepare('UPDATE telefonkonyvcsoportok SET torolve=?, sorrend=? WHERE id=?');
            $stmt->bind_param('ssi', $torolve, $nulled, $_POST['id']);
            $stmt->execute();
            print_r("UPDATE telefonkonyvcsoportok SET sorrend = sorrend - 1 WHERE sorrend > $sorrend AND torolve IS NULL;");
        }
        else
        {
            echo "<h2>Nincs jogosultsága az alegység törlésére!</h2>";
        }
    }
}