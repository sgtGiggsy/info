<?php

if(isset($irhat) && $irhat)
{
    $mysql = new MySQLHandler();
    $mysql->KeepAlive();

    purifyPost();

    if($_GET["action"] == "new")
    {
        $mysql->Prepare('INSERT INTO munkalapok (hely, igenylo, igenylesideje, vegrehajtasideje, munkavegzo1, munkavegzo2, leiras, eszkoz, ugyintezo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $mysql->Run($_POST['hely'], $_POST['igenylo'], $_POST['igenylesideje'], $_POST['vegrehajtasideje'], $_POST['munkavegzo1'], $_POST['munkavegzo2'], $_POST['leiras'], $_POST['eszkoz'], $_POST['ugyintezo']);
        $last_id = $mysql->last_insert_id;
    }

    elseif($_GET["action"] == "update")
    {
        $last_id = $_POST['id'];
        $timestamp = timeStampForSQL();
        $mysql->Prepare('UPDATE munkalapok SET hely=?, igenylo=?, igenylesideje=?, vegrehajtasideje=?, munkavegzo1=?, munkavegzo2=?, leiras=?, eszkoz=?, ugyintezo=?, modositasideje=? WHERE id=?');
        $mysql->Run($_POST['hely'], $_POST['igenylo'], $_POST['igenylesideje'], $_POST['vegrehajtasideje'], $_POST['munkavegzo1'], $_POST['munkavegzo2'], $_POST['leiras'], $_POST['eszkoz'], $_POST['ugyintezo'], $timestamp, $_POST['id']);
    }
    elseif($_GET["action"] == "delete")
    {
    }

    if(!$mysql->siker)
    {
        echo "<h2>Munka hozzáadása sikertelen!</h2>";
    }
    else
    {
        $mysql->Query("SELECT munkavegzo1.nev AS mv1nev, munkavegzo1.telefon AS mv1tel, munkavegzo1.beosztas AS mv1beo,
            munkavegzo2.nev AS mv2nev, munkavegzo2.telefon AS mv2tel, munkavegzo2.beosztas AS mv2beo,
            igenylo.nev AS ignev, igenylo.telefon AS igtel, szervezetek.nev AS igszervezet
                FROM munkalapok
                    LEFT JOIN felhasznalok munkavegzo1 ON munkavegzo1.id = munkalapok.munkavegzo1
                    LEFT JOIN felhasznalok munkavegzo2 ON munkavegzo2.id = munkalapok.munkavegzo2
                    LEFT JOIN felhasznalok igenylo ON igenylo.id = munkalapok.igenylo
                    LEFT JOIN szervezetek ON igenylo.szervezet = szervezetek.id
                WHERE munkalapok.id = ?", $last_id);
        $filltable = $mysql->Fetch();

        $mysql->Prepare('UPDATE munkalapok SET igenylonev=?, igenylotelefon=?, igenyloszervezet=?, munkavegzo1nev=?, munkavegzo1telefon=?, munkavegzo1beosztas=?, munkavegzo2nev=?, munkavegzo2telefon=?, munkavegzo2beosztas=? WHERE id=?');
        $mysql->Run($filltable['ignev'], $filltable['igtel'], $filltable['igszervezet'], $filltable['mv1nev'], $filltable['mv1tel'], $filltable['mv1beo'], $filltable['mv2nev'], $filltable['mv2tel'], $filltable['mv2beo'], $last_id);
    }

    if(isset($_GET['print']))
    {
        ?><script>
            window.open('<?=$RootPath?>/munkalapok/munkaszerkeszt/<?=$last_id?>?print');
        </script>
        <head><meta http-equiv="refresh" content="0; URL='<?=$backtosender?>'" /></head>
        <?php
    }
}