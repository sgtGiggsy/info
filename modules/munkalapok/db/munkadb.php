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
            if(isset($_POST['nyomtat']))
            {
                $last_id = mysqli_insert_id($con);
                ?><script>
                    window.open('<?=$RootPath?>/munkaprint/<?=$last_id?>')
                </script>
                <head><meta http-equiv="refresh" content="0; URL='<?=$backtosender?>'" /></head>
                <?php
            }
            else
            {
                header("Location: $backtosender");
            }
            
        }
    }

    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE munkalapok SET hely=?, igenylo=?, igenylesideje=?, vegrehajtasideje=?, munkavegzo1=?, munkavegzo2=?, leiras=?, eszkoz=?, ugyintezo=? WHERE id=?');
        $stmt->bind_param('sssssssssi', $_POST['hely'], $_POST['igenylo'], $_POST['igenylesideje'], $_POST['vegrehajtasideje'], $_POST['munkavegzo1'], $_POST['munkavegzo2'], $_POST['leiras'], $_POST['eszkoz'], $_POST['ugyintezo'], $_POST['id']);
        $stmt->execute();
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
    elseif($_GET["action"] == "delete")
    {
    }
}