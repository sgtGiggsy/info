<?php

if(!$irhat && count($_POST) == 0)
{
    getPermissionError();
}
else
{
    purifyPost();

    if($_GET['action'] == "startnew")
    {
        $jelenk = new MySQLHandler("SELECT MAX(id) AS maxid FROM vizsgak_vizsgakorok WHERE vizsga = ?", $vizsgaid);
        $jelenk->Bind($jelenkor);

        if($vizsgaeles)
        {
            $utolsovizsga = new MySQLHandler("SELECT folyoszam,
                    kezdet
                FROM vizsgak_kitoltesek
                    LEFT JOIN vizsgak_vizsgakorok ON vizsgak_kitoltesek.vizsgakor = vizsgak_vizsgakorok.id
                WHERE vizsgakor = ? ORDER BY vizsgak_kitoltesek.id DESC LIMIT 1;", $jelenkor);
            $utolsovizsga->Bind($utfolyoszam, $kezdet);
            $segments = explode("/", $utfolyoszam);
            $lastfolyoszam = $segments[2];
            $lastfolyoszam++;
            $ev = date('Y', strtotime($kezdet));
            $folyoszam = $ev . "/" . $vizsgaid . "/" . $lastfolyoszam;
        }
        else
        {
            $folyoszam = null;
        }
        
        $vizsgakitoltes = new MySQLHandler('INSERT INTO vizsgak_kitoltesek (vizsgakor, felhasznalo, folyoszam) VALUES (?, ?, ?)',
            array($jelenkor, $felhasznaloid, $folyoszam));
        if(!$vizsgakitoltes->siker)
        {
            echo "<h2>Vizsga elindítása sikertelen!<br></h2>";
        }
        $lastinsert = $vizsgakitoltes->last_insert_id;
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
            
            $kitoltesvalasz = new MySQLHandler('UPDATE vizsgak_kitoltesvalaszok SET valasz=?, valasz2=?, valasz3=? WHERE id =?',
                array($valasz, $valasz2, $valasz3, $kitoltesvalaszid));
            if(!$kitoltesvalasz->siker)
            {
                echo "<h2>Válasz beküldése sikertelen!<br></h2>";
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

        $kitoltes = new MySQLHandler('UPDATE vizsgak_kitoltesek SET befejezett=? WHERE id =?',
            array($befejez, $kitid));
        
        if(!$kitoltes->siker)
        {
            echo "<h2>Válasz beküldése sikertelen!<br></h2>";
        }

        // hashgyártás
        $hashgyart = new MySQLHandler("SELECT kerdes, kitoltes, valasz, felhasznalonev, kitoltesideje
            FROM vizsgak_kitoltesvalaszok
                INNER JOIN vizsgak_kitoltesek ON vizsgak_kitoltesvalaszok.kitoltes = vizsgak_kitoltesek.id
                INNER JOIN felhasznalok ON vizsgak_kitoltesek.felhasznalo = felhasznalok.id
            WHERE vizsgak_kitoltesek.id = ?
            ORDER BY vizsgak_kitoltesvalaszok.id;", $kitid);
        $hashgyart = $hashgyart->Result();

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
        
        $hashinsert = new MySQLHandler("UPDATE vizsgak_kitoltesek SET vizsgakod = ?, hash = ? WHERE id = ?",
            array($hashalap, $hash, $kitid));
    }
}