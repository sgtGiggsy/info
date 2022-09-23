<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    foreach($_POST as $key => $value)
    {
        if ($value == "NULL" || $value == "")
        {
            $_POST[$key] = NULL;
        }
    }

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO modellek (gyarto, modell, tipus) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $_POST['gyarto'], $_POST['modell'], $_POST['tipus']);
        $stmt->execute();

        $last_id = mysqli_insert_id($con);

        if($_POST['tipus'] == "12")
        {
            $stmt = $con->prepare('INSERT INTO nyomtatomodellek (modell, szines, scanner, fax, defadmin, defpass, maxmeret) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('sssssss', $last_id, $_POST['szines'], $_POST['scanner'], $_POST['fax'], $_POST['defadmin'], $_POST['defpass'], $_POST['maxmeret']);
            $stmt->execute();
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Modell hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE modellek SET gyarto=?, modell=?, tipus=? WHERE id=?');
        $stmt->bind_param('sssi', $_POST['gyarto'], $_POST['modell'], $_POST['tipus'], $_POST['id']);
        $stmt->execute();

        if($tipusnev == "nyomtato")
        {
            $modellid = $_POST['id'];
            $tocount = mySQLConnect("SELECT id FROM nyomtatomodellek WHERE modell = $modellid;");
            if(mysqli_num_rows($tocount) == 0)
            {
                $stmt = $con->prepare('INSERT INTO nyomtatomodellek (modell, szines, scanner, fax, defadmin, defpass, maxmeret) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('sssssss', $modellid, $_POST['szines'], $_POST['scanner'], $_POST['fax'], $_POST['defadmin'], $_POST['defpass'], $_POST['maxmeret']);
                $stmt->execute();
            }
            else
            {
                $stmt = $con->prepare('UPDATE nyomtatomodellek SET szines=?, scanner=?, fax=?, defadmin=?, defpass=?, maxmeret=? WHERE modell=?');
                $stmt->bind_param('ssssssi', $_POST['szines'], $_POST['scanner'], $_POST['fax'], $_POST['defadmin'], $_POST['defpass'], $_POST['maxmeret'], $_POST['id']);
                $stmt->execute();
            }
        }

        if($tipusnev == "mediakonverter")
        {
            $modellid = $_POST['id'];
            $tocount = mySQLConnect("SELECT id FROM mediakonvertermodellek WHERE modell = $modellid;");

            if(mysqli_num_rows($tocount) == 0)
            {
                $stmt = $con->prepare('INSERT INTO mediakonvertermodellek (modell, fizikaireteg, transzpszabvany, transzpcsatlakozo, transzpsebesseg, lanszabvany, lancsatlakozo, lansebesseg) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('ssssssss', $modellid, $_POST['fizikaireteg'], $_POST['transzpszabvany'], $_POST['transzpcsatlakozo'], $_POST['transzpsebesseg'], $_POST['lanszabvany'], $_POST['lancsatlakozo'], $_POST['lansebesseg']);
                $stmt->execute();
            }
            else
            {
                $stmt = $con->prepare('UPDATE mediakonvertermodellek SET fizikaireteg=?, transzpszabvany=?, transzpcsatlakozo=?, transzpsebesseg=?, lanszabvany=?, lancsatlakozo=?, lansebesseg=? WHERE modell=?');
                $stmt->bind_param('sssssssi', $_POST['fizikaireteg'], $_POST['transzpszabvany'], $_POST['transzpcsatlakozo'], $_POST['transzpsebesseg'], $_POST['lanszabvany'], $_POST['lancsatlakozo'], $_POST['lansebesseg'], $_POST['id']);
                $stmt->execute();
            }
        }

        if($tipusnev == "bovitomodul")
        {
            $modellid = $_POST['id'];
            $tocount = mySQLConnect("SELECT id FROM bovitomodellek WHERE modell = $modellid;");

            if(mysqli_num_rows($tocount) == 0)
            {
                $stmt = $con->prepare('INSERT INTO bovitomodellek (modell, fizikaireteg, transzpszabvany, transzpcsatlakozo, transzpsebesseg) VALUES (?, ?, ?, ?, ?)');
                $stmt->bind_param('sssss', $modellid, $_POST['fizikaireteg'], $_POST['transzpszabvany'], $_POST['transzpcsatlakozo'], $_POST['transzpsebesseg']);
                $stmt->execute();
            }
            else
            {
                $stmt = $con->prepare('UPDATE bovitomodellek SET fizikaireteg=?, transzpszabvany=?, transzpcsatlakozo=?, transzpsebesseg=? WHERE modell=?');
                $stmt->bind_param('ssssi', $_POST['fizikaireteg'], $_POST['transzpszabvany'], $_POST['transzpcsatlakozo'], $_POST['transzpsebesseg'], $_POST['id']);
                $stmt->execute();
            }
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Rack szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
}