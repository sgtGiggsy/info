<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);
    $sql = new MySQLHandler();
    $sql->KeepAlive();

    purifyPost();

    if($csoportir && @$_POST['felhasznalo'])
    {
        $felhid = $_POST['felhasznalo'];
    }
    else
    {
        $felhid = $felhasznaloid;
    }

    $valosnev = $_SESSION['nev'];

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
        
        $sql->Query("SELECT felhasznalok.id AS id, felhasznalok.nev AS nev,
                    felhasznalok.szervezet AS szervezet, feladatok.szakid AS szak,
                    feladatok.id AS feladatid
                FROM felhasznalok
                    LEFT JOIN feladatok ON feladatok.felhasznalo = felhasznalok.id
                WHERE feladatok.pubid = ?;", $hibid);
        $felhasznalo = $sql->Fetch();
        
        $origfelhasznaloneve = $felhasznalo['nev'];
        $origfelhasznaloid = $felhasznalo['id'];
        $origszervezet = $felhasznalo['szervezet'];
        $origszak = $felhasznalo['szak'];
        $origid = $felhasznalo['feladatid'];
    }

    if($_GET["action"] == "new")
    {
        $feladattipus = "1";
        $pubid = rand(2000000, 15000000); // Kell egy induló pubid, mert ha van null érték a pubid oszlopban, akkor a random generálás nem tud mihez hasonlítani, és ezért nem is generál számot
        $sql->Prepare('INSERT INTO feladatok (felhasznalo, rovid, bovitett, fajl, eszkozneve, szakid, epulet, helyiseg, feladattipus, pubid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $sql->Run($felhid, $_POST['rovid'], $_POST['bovitett'], $_POST['fajl'], $_POST['eszkozneve'], $_POST['szakid'], $_POST['epulet'], $_POST['helyiseg'], $feladattipus, $pubid);

        if(!$sql->siker)
        {
            echo "<h2>Hibajegy hozzáadása sikertelen!</h2>";
        }

        $origid = $sql->last_insert_id;

        $sql->Query("UPDATE feladatok SET pubid = (
                SELECT FLOOR(1 + RAND() * 999999) AS random_num 
                FROM feladatok
                WHERE 'random_num' NOT IN (SELECT pubid FROM feladatok) LIMIT 1)
            WHERE id = ?;", $origid);

        $ertesitesadatok = (new MySQLHandler("SELECT pubid, felhasznalok.nev AS nev
                FROM feladatok
                    INNER JOIN felhasznalok ON feladatok.felhasznalo = felhasznalok.id
                WHERE feladatok.id = ?;", $origid))->Bind($pubid, $felhasznaloneve);

        $ertesites = new Ertesites("$felhasznaloneve új hibajegyet hozott létre", $_POST['rovid'], "hibajegy/$hibajegyid", 2);
        $ertesites->AddFelhasznalo($felhid);
        $ertesites->Ment();
    }

    elseif($_GET["action"] == "update")
    {
        $sorszam = $_POST['id'];

        $sql->Prepare('UPDATE feladatok SET felhasznalo=?, rovid=?, bovitett=?, fajl=?, eszkozneve=?, szakid=?, epulet=?, helyiseg=? WHERE id=?');
        $sql->Run($_POST['felhasznalo'], $_POST['rovid'], $_POST['bovitett'], $_POST['fajl'], $_POST['eszkozneve'], $_POST['szakid'], $_POST['epulet'], $_POST['helyiseg'], $origid);
    
        if(!$sql->siker)
        {
            echo "<h2>Hibajegy szerkesztése sikertelen!</h2>";
        }

        $ertesitesadatok = (new MySQLHandler("SELECT felhasznalok.id AS felhasznid, felhasznalok.email AS email
                FROM feladatok
                    INNER JOIN felhasznalok ON feladatok.felhasznalo = felhasznalok.id
                WHERE feladatok.pubid = ?;", $sorszam))->Bind($felhasznid, $email);

        $ertesites = new Ertesites("$valosnev szerkesztette a(z) $sorszam számú hibajegyet", $_POST['rovid'], "hibajegy/$sorszam", 2);
        $ertesites->AddFelhasznalo(array("felhasznalo" => $felhasznid, "email" => $email));
        $ertesites->Ment();
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
                $sql->Query("UPDATE feladatok SET allapot = 0 WHERE id = ?;", $origid);
            }
            elseif($allapottipus == 2)
            {
                $sql->Query("UPDATE feladatok SET allapot = 1 WHERE id = ?;", $origid);
            }

            elseif($allapottipus == 27)
            {
                $hatarido = $_POST['hatarido'];
                mySQLConnect("UPDATE feladatok SET hatarido = ? WHERE id = ?;", $hatarido, $origid);
            }
            elseif($allapottipus == 28)
            {
                $elhalasztva = $_POST['elhalasztva'];
                mySQLConnect("UPDATE feladatok SET elhalasztva = ?, prioritas = 1 WHERE id = ?;", $elhalasztva, $origid);
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

        hibajegyErtesites("$valosnev frissítette a(z) $sorszam számú hibajegy állapotát", $torzs, $sorszam, $origfelhasznaloid, $origszervezet, $origszak);
    }

    if(isset($_FILES["fajlok"]) && $origid)
    {        
        $filetypes = array('.jpg', '.jpeg', '.png', '.bmp');
        $mediatype = array('image/jpeg', 'image/png', 'image/bmp');
        $fajlok = $_FILES["fajlok"];

        $ev = date("Y");
        $honap = date("m");
        $gyokermappa = "./uploads/";
        $mappagyokernelkul = $_SESSION['hibajegymappa'] . "/$ev/$honap/";
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