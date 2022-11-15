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
        else
        {
            $_POST[$key] = quickXSSfilter($value);
        }
    }

    if($mindir && @$_POST['felhasznalo'])
    {
        $felhid = $_POST['felhasznalo'];
    }
    else
    {
        $felhid = $felhasznaloid;
    }

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO hibajegyek (felhasznalo, rovid, bovitett, fajl, eszkozneve, tipus, epulet, helyiseg) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssssss', $felhid, $_POST['rovid'], $_POST['bovitett'], $_POST['fajl'], $_POST['eszkozneve'], $_POST['tipus'], $_POST['epulet'], $_POST['helyiseg']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Hibajegy hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }

        $origid = mysqli_insert_id($con);
        $last_id = hashId($origid);        
    }

    elseif($_GET["action"] == "update")
    {
        $origid = hashId($_POST['id']);
        $stmt = $con->prepare('UPDATE hibajegyek SET felhasznalo=?, rovid=?, bovitett=?, fajl=?, eszkozneve=?, tipus=?, epulet=?, helyiseg=? WHERE id=?');
        $stmt->bind_param('ssssssssi', $_POST['felhasznalo'], $_POST['rovid'], $_POST['bovitett'], $_POST['fajl'], $_POST['eszkozneve'], $_POST['tipus'], $_POST['epulet'], $_POST['helyiseg'], $origid);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Hibajegy szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }

    elseif($_GET["action"] == "stateupdate")
    {
        $origid = hashId($_POST['hibajegy']);
        if($mindir)
        {
            $szerepkor = 3;
            $valtozastipus = $_POST['valtozastipus'];
            if($valtozastipus == 5)
            {
                mySQLConnect("UPDATE hibajegyek SET allapot = 0 WHERE id = $origid");
            }
            elseif($valtozastipus == 6)
            {
                mySQLConnect("UPDATE hibajegyek SET allapot = 1 WHERE id = $origid");
            }
        }
        elseif($csoportir)
        {
            $szerepkor = 2;
            $valtozastipus = "0";
        }
        elseif($sajatir)
        {
            $szerepkor = 1;
            $valtozastipus = "0";
        }

        $stmt = $con->prepare('INSERT INTO hibajegyallapotok (hibajegy, felhasznalo, valtozastipus, megjegyzes, szerepkor) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $origid, $felhasznaloid, $valtozastipus, $_POST['megjegyzes'], $szerepkor);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Hibajegy frissítése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }

    if(isset($_FILES["fajlok"]))
    {
        $feltoltottfajlok = array();
        
        $filetypes = array('.jpg', '.jpeg', '.png', '.bmp');
        $mediatype = array('image/jpeg', 'image/png', 'image/bmp');

        $fajlok = $_FILES["fajlok"];

        $ev = date("Y");
        $honap = date("m");
        $kiindulomappa = "./uploads/" . $_SESSION[getenv('SESSION_NAME').'hibajegymappa'] . "/";
        $mappavaltozo = "$ev/$honap/";
        $feltoltesimappa = "$kiindulomappa/$mappavaltozo";

        $db = count($fajlok["name"]);
        for($i = 0; $i < $db; $i++)
        {
            if (!in_array($fajlok['type'][$i], $mediatype))
            {
                $uzenet = "A fájl típusa nem megengedett: " . $fajlok['name'][$i];
            }
            else
            {
                if(!file_exists($feltoltesimappa))
                {
                    mkdir($feltoltesimappa, 0777, true);
                }

                $fajlnev = strtolower(str_replace(".", time() . ".", $fajlok['name'][$i]));
                $finalfile = $feltoltesimappa . $fajlnev;
                if(file_exists($finalfile))
                {
                    $uzenet = "A feltölteni kívánt fájl már létezik: " . $fajlnev;
                }
                else
                {
                    move_uploaded_file($fajlok['tmp_name'][$i], $finalfile);
                    $uzenet = 'A fájl feltöltése sikeresen megtörtént: ' . $fajlnev;
                    $feltoltottfajlok[] = "$mappavaltozo" . "$fajlnev";
                }
            }
        }

        if(count($feltoltottfajlok))
        {
            foreach($feltoltottfajlok as $fajl)
            {
                $stmt = $con->prepare('INSERT INTO hibajegyfajlok (hibajegy, fajl) VALUES (?, ?)');
                $stmt->bind_param('ss', $origid, $fajl);
                $stmt->execute();
            }
        }
    }

    
}