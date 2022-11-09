<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    if($_GET["action"] == "szamtarsitas") // Verziókövetés kész
    {
        $postid = 1;
        $ujsor = true;
        $nullval = null;

        do
        {
            if(isset($_POST['portid-'.$postid]))
            {
                $szamid = 1;
                $ujszam = true;
                do
                {
                    if(isset($_POST['telefonszam-' . $postid . "-" . $szamid]))
                    {
                        if($_POST['telefonszam-' . $postid . "-" . $szamid] == null && isset($_POST['nullid-' . $postid . "-" . $szamid]))
                        {
                            $stmt = $con->prepare('UPDATE telefonszamok SET port=? WHERE id=?');
                            $stmt->bind_param('ss', $nullval, $_POST['nullid-' . $postid . "-" . $szamid]);
                            $stmt->execute();
                        }
                        else
                        {
                            $stmt = $con->prepare('UPDATE telefonszamok SET port=? WHERE id=?');
                            $stmt->bind_param('si', $_POST['portid-'.$postid], $_POST['telefonszam-' . $postid . "-" . $szamid]);
                            $stmt->execute();
                        }
                        if(mysqli_errno($con) != 0)
                        {
                            echo "<h2>A telefonszám szerkesztése sikertelen!<br></h2>";
                            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                        }
                    }
                    else
                    {
                        $ujszam = false;
                    }
                    $szamid++;
                } while ($ujszam);
            }
            else
            {
                $ujsor = false;
            }
            $postid++;
        } while ($ujsor);

        $targeturl = $RootPath . "/" . $_GET['kuldooldal'] . "/" . $_GET['kuldooldalid'] . "?action=szamtarsitas";
        header("Location: $targeturl");
    }
}