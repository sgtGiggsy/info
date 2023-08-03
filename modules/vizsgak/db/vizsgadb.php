<?php

if(!$irhat && count($_POST) == 0)
{
    getPermissionError();
}
else
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_GET['action'] == "startnew")
    {
        $jelenkor = mySQLConnect("SELECT MAX(id) AS maxid FROM vizsgak_vizsgakorok WHERE vizsga = $vizsgaid;");
        $jelenkor = mysqli_fetch_assoc($jelenkor)['maxid'];
        if($vizsgaeles)
        {
            $utolsovizsga = mySQLConnect("SELECT folyoszam FROM vizsgak_kitoltesek WHERE vizsgakor = $jelenkor ORDER BY id DESC LIMIT 1;");
            $utolsovizsga = mysqli_fetch_assoc($utolsovizsga)['folyoszam'];
            $segments = explode("/", $utolsovizsga);
            $lastfolyoszam = $segments[2];
            $lastfolyoszam++;
            $folyoszam = date('Y') . "/" . $vizsgaid . "/" . $lastfolyoszam;
        }
        else
        {
            $folyoszam = null;
        }
        
        $stmt = $con->prepare('INSERT INTO vizsgak_kitoltesek (vizsgakor, felhasznalo, folyoszam) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $jelenkor, $felhasznaloid, $folyoszam);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Vizsga elindítása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        $lastinsert = mysqli_insert_id($con);
    }

    elseif($_GET['action'] == "answerquestion")
    {
        if(isset($_POST['valaszok'][0]) && $_POST['valaszok'][0])
        {
            $kitoltesvalaszid = $_POST['kitoltesvalaszid'];
            $valasz = $_POST['valaszok'][0];
            $valasz2 = $_POST['valaszok'][1] ?? null;
            $valasz3 = $_POST['valaszok'][2] ?? null;

            /*var_dump($valasz);
            var_dump($kitoltesvalaszid);
            echo "<br><br>";*/
            $stmt = $con->prepare('UPDATE vizsgak_kitoltesvalaszok SET valasz=?, valasz2=?, valasz3=? WHERE id =?');
            $stmt->bind_param('sssi', $valasz, $valasz2, $valasz3, $kitoltesvalaszid);
            $stmt->execute();
            if(mysqli_errno($con) != 0)
            {
                echo "<h2>Válasz beküldése sikertelen!<br></h2>";
                echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
            }
        }
        
        /*
        foreach($_POST as $p => $v)
        {
            var_dump($p);
            echo " - ";
            var_dump($v);
            echo "<br>";
        }*/
    }

    elseif($_GET['action'] == "finalize")
    {
        $befejez = 1;
        $kitid = $_POST['kitoltesid'];
        $hashalap = null;

        $stmt = $con->prepare('UPDATE vizsgak_kitoltesek SET befejezett=? WHERE id =?');
        $stmt->bind_param('si', $befejez, $kitid);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Válasz beküldése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }

        // hashgyártás
        $hashgyart = mySQLConnect("SELECT kerdes, kitoltes, valasz, felhasznalonev, kitoltesideje
        FROM vizsgak_kitoltesvalaszok
            INNER JOIN vizsgak_kitoltesek ON vizsgak_kitoltesvalaszok.kitoltes = vizsgak_kitoltesek.id
            INNER JOIN felhasznalok ON vizsgak_kitoltesek.felhasznalo = felhasznalok.id
        WHERE vizsgak_kitoltesek.id = $kitid
        ORDER BY vizsgak_kitoltesvalaszok.id;");

        foreach($hashgyart as $x)
        {
            if(!$hashalap)
            {
                $hashalap .= $x['felhasznalonev'];
                $hashalap .= $x['kitoltesideje'];
                $hashalap .= "|";
                $hashalap .= $x['kitoltes'];
            }
            $hashalap .= "|";
            $hashalap .= $x['kerdes'];
            $hashalap .= "/";
            $hashalap .= $x['valasz'];
        }
        $hash = hash('md5', $hashalap);
        
        mySQLConnect("UPDATE vizsgak_kitoltesek SET vizsgakod = '$hashalap', hash = '$hash' WHERE id = $kitid");
    }
}