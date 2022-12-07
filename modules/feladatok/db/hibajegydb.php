<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    if($csoportir && @$_POST['felhasznalo'])
    {
        $felhid = $_POST['felhasznalo'];
    }
    else
    {
        $felhid = $felhasznaloid;
    }

    $valosnev = $_SESSION[getenv('SESSION_NAME').'nev'];

    if(isset($_POST['id']) || isset($_POST['feladat']))
    {
        if(isset($_POST['id']))
        {
            $hibid = $_POST['id'];
        }
        else
        {
            $hibid = $_POST['feladat'];
        }
        
        $felhasznaloquery = mySQLConnect("SELECT felhasznalok.id AS id, felhasznalok.nev AS nev,
                    felhasznalok.alakulat AS alakulat, feladatok.szakid AS szak,
                    feladatok.id AS feladatid
                FROM felhasznalok
                    LEFT JOIN feladatok ON feladatok.felhasznalo = felhasznalok.id
                WHERE feladatok.pubid = $hibid");
        $felhasznalo = mysqli_fetch_assoc($felhasznaloquery);
        
        $origfelhasznaloneve = $felhasznalo['nev'];
        $origfelhasznaloid = $felhasznalo['id'];
        $origalakulat = $felhasznalo['alakulat'];
        $origszak = $felhasznalo['szak'];
        $origid = $felhasznalo['feladatid'];
    }

    if($_GET["action"] == "new")
    {
        $feladattipus = "1";
        $pubid = rand(2000000, 15000000); // Kell egy induló pubid, mert ha van null érték a pubid oszlopban, akkor a random generálás nem tud mihez hasonlítani, és ezért nem is generál számot
        $stmt = $con->prepare('INSERT INTO feladatok (felhasznalo, rovid, bovitett, fajl, eszkozneve, szakid, epulet, helyiseg, feladattipus, pubid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssssssss', $felhid, $_POST['rovid'], $_POST['bovitett'], $_POST['fajl'], $_POST['eszkozneve'], $_POST['szakid'], $_POST['epulet'], $_POST['helyiseg'], $feladattipus, $pubid);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Hibajegy hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }

        $origid = mysqli_insert_id($con);

        mySQLConnect("UPDATE feladatok SET pubid = (
                SELECT FLOOR(1 + RAND() * 999999) AS random_num 
                FROM feladatok
                WHERE 'random_num' NOT IN (SELECT pubid FROM feladatok) LIMIT 1)
            WHERE id = $origid");

        $lastrow = mySQLConnect("SELECT pubid FROM feladatok WHERE id = $origid");
        $last_id = mysqli_fetch_assoc($lastrow)['pubid'];

        $felhasznaloquery = mySQLConnect("SELECT nev FROM felhasznalok WHERE id = $felhid");
        $felhasznaloneve = mysqli_fetch_assoc($felhasznaloquery)['nev'];

        hibajegyErtesites("$felhasznaloneve új hibajegyet hozott létre", $_POST['rovid'], $last_id, $felhid, $alakulat, $_POST['szakid']);
    }

    elseif($_GET["action"] == "update")
    {
        $sorszam = $_POST['id'];
        $stmt = $con->prepare('UPDATE feladatok SET felhasznalo=?, rovid=?, bovitett=?, fajl=?, eszkozneve=?, szakid=?, epulet=?, helyiseg=? WHERE id=?');
        $stmt->bind_param('ssssssssi', $_POST['felhasznalo'], $_POST['rovid'], $_POST['bovitett'], $_POST['fajl'], $_POST['eszkozneve'], $_POST['szakid'], $_POST['epulet'], $_POST['helyiseg'], $origid);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Hibajegy szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }

        hibajegyErtesites("$valosnev szerkesztette a(z) $sorszam számú hibajegyet", $_POST['rovid'], $sorszam, $origfelhasznaloid, $origalakulat, $_POST['szakid']);
    }

    elseif($_GET["action"] == "stateupdate")
    {
        $sorszam = $_POST['feladat'];
        $megjegyzes = $_POST['megjegyzes'];

        if($csoportir)
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

            if($csoportolvas)
            {
                $szerepkor = 2;
            }
            elseif($sajatir)
            {
                $szerepkor = 1;
            }
        }

        $kijeloltfelelosok = null;
        foreach($_POST['felelos'] as $felelos)
        {
            if($felelos)
            {
                if(!$kijeloltfelelosok)
                {
                    $kijeloltfelelosok = "Kijelölt felelős(ök):<br>";
                }
                $fnev = mySQLConnect("SELECT nev FROM felhasznalok WHERE id = $felelos");
                $fnev = mysqli_fetch_assoc($fnev)['nev'];
                $kijeloltfelelosok .= "- $fnev<br>";

                $stmt = $con->prepare('INSERT INTO feladatfelelosok (feladat, felhasznalo) VALUES (?, ?)');
                $stmt->bind_param('ss', $origid, $felelos);
                $stmt->execute();
            }
        }

        if($kijeloltfelelosok)
        {
            if($megjegyzes)
            {
                $megjegyzes .= "<br>";
            }
            $megjegyzes .= $kijeloltfelelosok;
        }

        $stmt = $con->prepare('INSERT INTO feladatallapotok (feladat, felhasznalo, allapottipus, megjegyzes, szerepkor) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $origid, $felhasznaloid, $allapottipus, $megjegyzes, $szerepkor);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Hibajegy frissítése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }

        $stateupdateid = mysqli_insert_id($con);

        $allapottipusquery = mySQLConnect("SELECT folyamat FROM allapottipusok WHERE id = $allapottipus");
        $allapotneve = mysqli_fetch_assoc($allapottipusquery)['folyamat'];

        $torzs = $allapotneve;
        if($_POST['megjegyzes'])
        {
            $torzs .= ": " . $_POST['megjegyzes'];
        }

        hibajegyErtesites("$valosnev frissítette a(z) $sorszam számú hibajegy állapotát", $torzs, $sorszam, $origfelhasznaloid, $origalakulat, $origszak);
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