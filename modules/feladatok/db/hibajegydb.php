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
        $feladattipus = "1";
        $stmt = $con->prepare('INSERT INTO feladatok (felhasznalo, rovid, bovitett, fajl, eszkozneve, szakid, epulet, helyiseg, feladattipus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssssss', $felhid, $_POST['rovid'], $_POST['bovitett'], $_POST['fajl'], $_POST['eszkozneve'], $_POST['szakid'], $_POST['epulet'], $_POST['helyiseg'], $feladattipus);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Hibajegy hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }

        $origid = mysqli_insert_id($con);
        $last_id = cryptId($origid);        
    }

    elseif($_GET["action"] == "update")
    {
        $origid = encryptId($_POST['id']);
        $stmt = $con->prepare('UPDATE feladatok SET felhasznalo=?, rovid=?, bovitett=?, fajl=?, eszkozneve=?, szakid=?, epulet=?, helyiseg=? WHERE id=?');
        $stmt->bind_param('ssssssssi', $_POST['felhasznalo'], $_POST['rovid'], $_POST['bovitett'], $_POST['fajl'], $_POST['eszkozneve'], $_POST['szakid'], $_POST['epulet'], $_POST['helyiseg'], $origid);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Hibajegy szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }

    elseif($_GET["action"] == "stateupdate")
    {
        $origid = encryptId($_POST['feladat']);
        if($mindir)
        {
            $allapottipus = $_POST['allapottipus'];
            $szerepkor = 3;
            if($allapottipus == 30)
            {
                mySQLConnect("UPDATE feladatok SET allapot = 0 WHERE id = $origid;");
            }
            elseif($allapottipus == 2)
            {
                mySQLConnect("UPDATE feladatok SET allapot = 1 WHERE id = $origid;");
            }

            elseif($allapottipus == 27)
            {
                $hatarido = $_POST['hatarido'];
                mySQLConnect("UPDATE feladatok SET hatarido = '$hatarido' WHERE id = $origid;");
            }
            elseif($allapottipus == 28)
            {
                $elhalasztva = $_POST['elhalasztva'];
                mySQLConnect("UPDATE feladatok SET elhalasztva = '$elhalasztva', prioritas = 1 WHERE id = $origid;");
            }
        }
        else
        {
            if(isset($_POST['allapottipus']))
            {
                $allapottipus = $_POST['allapottipus'];
            }
            elseif(isset($_FILES["fajlok"]) && count(array_filter($_FILES["fajlok"]['name'])) > 0)
            {
                $allapottipus = 26;
            }
            else
            {
                $allapottipus = 0;
            }

            if($csoportir)
            {
                $szerepkor = 2;
            }
            elseif($sajatir)
            {
                $szerepkor = 1;
            }
        }

        $stmt = $con->prepare('INSERT INTO feladatallapotok (feladat, felhasznalo, allapottipus, megjegyzes, szerepkor) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $origid, $felhasznaloid, $allapottipus, $_POST['megjegyzes'], $szerepkor);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Hibajegy frissítése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }

        $stateupdateid = mysqli_insert_id($con);
    }

    if(isset($_FILES["fajlok"]) && $origid)
    {        
        $filetypes = array('.jpg', '.jpeg', '.png', '.bmp');
        $mediatype = array('image/jpeg', 'image/png', 'image/bmp');
        $fajlok = $_FILES["fajlok"];

        $ev = date("Y");
        $honap = date("m");
        $gyokermappa = "./uploads/";
        $mappagyokernelkul = $_SESSION[getenv('SESSION_NAME').'hibajegymappa'] . "/$ev/$honap/";
        $feltoltesimappa = "$gyokermappa/$mappagyokernelkul";

        $include = true;
        include('./modules/alap/db/feltoltesdb.php');

        if(count($uploadids) > 0)
        {
            foreach($uploadids as $fajl)
            {
                $stmt = $con->prepare('INSERT INTO feladatfajlok (feladat, feltoltes) VALUES (?, ?)');
                $stmt->bind_param('ss', $origid, $fajl);
                $stmt->execute();
            }
        }
    }

    if(isset($stateupdateid) && isset($uploadids) && isset($allapottipus) && $allapottipus == 26)
    {
        $fajlnevek = "";
        if($_POST['megjegyzes'])
        {
            $fajlnevek .= $_POST['megjegyzes'] . "<br>";
        }
        $fajlnevek .= "A következő fájl(ok) került(ek) hozzáadásra:<br>";
        
        foreach($uploadids as $fajl)
        {
            $fajlquery = mySQLConnect("SELECT fajl FROM feltoltesek WHERE id = $fajl");
            $fajl = mysqli_fetch_assoc($fajlquery)['fajl'];
            $fajlnevek .= $fajl . "<br>";
        }

        mySQLConnect("UPDATE feladatallapotok SET megjegyzes = '$fajlnevek' WHERE id = $stateupdateid;");
    }

    
}