<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO munkalapok (hely, igenylo, igenylesideje, vegrehajtasideje, munkavegzo1, munkavegzo2, leiras, eszkoz, ugyintezo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssssss', $_POST['hely'], $_POST['igenylo'], $_POST['igenylesideje'], $_POST['vegrehajtasideje'], $_POST['munkavegzo1'], $_POST['munkavegzo2'], $_POST['leiras'], $_POST['eszkoz'], $_POST['ugyintezo']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Munka hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            $last_id = mysqli_insert_id($con);

            $filltable = mySQLConnect("SELECT munkavegzo1.nev AS mv1nev, munkavegzo1.telefon AS mv1tel, munkavegzo1.beosztas AS mv1beo,
                munkavegzo2.nev AS mv2nev, munkavegzo2.telefon AS mv2tel, munkavegzo2.beosztas AS mv2beo,
                igenylo.nev AS ignev, igenylo.telefon AS igtel, szervezetek.nev AS igszervezet
                    FROM munkalapok
                        LEFT JOIN felhasznalok munkavegzo1 ON munkavegzo1.id = munkalapok.munkavegzo1
                        LEFT JOIN felhasznalok munkavegzo2 ON munkavegzo2.id = munkalapok.munkavegzo2
                        LEFT JOIN felhasznalok igenylo ON igenylo.id = munkalapok.igenylo
                        LEFT JOIN szervezetek ON igenylo.szervezet = szervezetek.id
                    WHERE munkalapok.id = $last_id;");
            $filltable = mysqli_fetch_assoc($filltable);

            $stmt = $con->prepare('UPDATE munkalapok SET igenylonev=?, igenylotelefon=?, igenyloszervezet=?, munkavegzo1nev=?, munkavegzo1telefon=?, munkavegzo1beosztas=?, munkavegzo2nev=?, munkavegzo2telefon=?, munkavegzo2beosztas=? WHERE id=?');
            $stmt->bind_param('sssssssssi', $filltable['ignev'], $filltable['igtel'], $filltable['igszervezet'], $filltable['mv1nev'], $filltable['mv1tel'], $filltable['mv1beo'], $filltable['mv2nev'], $filltable['mv2tel'], $filltable['mv2beo'], $last_id);
            $stmt->execute();

            if(isset($_GET['print']))
            {
                ?><script>
                    window.open('<?=$RootPath?>/munkaprint/<?=$last_id?>')
                </script>
                <head><meta http-equiv="refresh" content="0; URL='<?=$backtosender?>'" /></head>
                <?php
            }
        }
    }

    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE munkalapok SET hely=?, igenylo=?, igenylesideje=?, vegrehajtasideje=?, munkavegzo1=?, munkavegzo2=?, leiras=?, eszkoz=?, ugyintezo=?, modositasideje=? WHERE id=?');
        $stmt->bind_param('ssssssssssi', $_POST['hely'], $_POST['igenylo'], $_POST['igenylesideje'], $_POST['vegrehajtasideje'], $_POST['munkavegzo1'], $_POST['munkavegzo2'], $_POST['leiras'], $_POST['eszkoz'], $_POST['ugyintezo'], timeStampForSQL(), $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Munka szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }

    if(mysqli_errno($con) != 0)
    {
        echo "<h2>Munka szerkesztése sikertelen!<br></h2>";
        echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
    }
    else
    {
        header("Location: $backtosender");
    }

}