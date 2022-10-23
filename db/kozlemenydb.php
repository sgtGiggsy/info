<?php
if (!$irhat)
{
	echo "Nincs jogosultságod az oldal megtekintésére!";
}
else
{
    $con = mySQLConnect(false);
    foreach($_POST as $key => $value)
    {
        if ($value == "NULL")
        {
            $_POST[$key] = NULL;
        }
    }

    if(!isset($_POST['link']) || !$_POST['link'])
    {
        $link = nevToLink($_POST['cim']);
    }
    else
    {
        $link = $_POST['link'];
    }

    $cimke = null;
    foreach($_POST['cimkek'] as $x)
    {
        $cimke .= $x . ",";
    }
    $cimke = substr_replace($cimke ,"",-1);
    
    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO kozlemenyek (szerzo, cim, bevezetes, szovegtorzs, cimke, publikalt, link) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssss', $felhasznaloid, $_POST['cim'], $_POST['bevezetes'], $_POST['szovegtorzs'], $cimke, $_POST['publikalt'], $link);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Új közlemény hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        $stmt->close();
        $con->close();
    }
    elseif($_GET["action"] == "update")
    {
        $timestamp = time();
        $stmt = $con->prepare('UPDATE kozlemenyek SET cim=?, bevezetes=?, szovegtorzs=?, cimke=?, publikalt=?, link=? WHERE id=?');
        $stmt->bind_param('ssssssi', $_POST['cim'], $_POST['bevezetes'], $_POST['szovegtorzs'], $cimke, $_POST['publikalt'], $link, $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A hír szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        $stmt->close();
        $con->close();
    }
}
?>