<?php

if(isset($felhasznaloid))
{
    $con = mySQLConnect(false);

    foreach($_POST as $key => $value)
    {
        if ($value == "NULL" || $value == "")
        {
            $_POST[$key] = NULL;
        }
    }

    $szemelyesdb = mySQLConnect("SELECT * FROM szemelyesbeallitasok WHERE felhid = $felhasznaloid");

    if(mysqli_num_rows($szemelyesdb) == 0)
    {
        $stmt = $con->prepare('INSERT INTO szemelyesbeallitasok (felhid, switchstatemail, switchstateshow, szinsema) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('isss', $felhasznaloid, $_POST['switchstatemail'], $_POST['switchstateshow'], $_POST['szinsema']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Személyes beállítás hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    elseif(mysqli_num_rows($szemelyesdb) == 1)
    {
        $stmt = $con->prepare('UPDATE szemelyesbeallitasok SET switchstatemail=?, switchstateshow=?, szinsema=? WHERE felhid=?');
        $stmt->bind_param('sssi', $_POST['switchstatemail'], $_POST['switchstateshow'], $_POST['szinsema'], $felhasznaloid);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Személyes beállítás szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }

    $szemelyesbeallitasok = mySQLConnect("SELECT * FROM szemelyesbeallitasok WHERE felhid = $felhasznaloid");
    $szemelyes = mysqli_fetch_assoc($szemelyesbeallitasok);
}