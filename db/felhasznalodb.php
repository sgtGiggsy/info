<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    foreach($_POST as $key => $value)
    {
        //echo "$key: $value <br>";
        if ($value == "NULL" || $value == "")
        {
            $_POST[$key] = NULL;
        }
    }

    if($_GET["action"] == "new")
    {
    }
    elseif($_GET["action"] == "update")
    {
        $felhasznalo = $_POST['id'];
        foreach($menu as $x)
        {
            $menuid = $x['id'];
            // echo "lekér $menuid <br>";
            $xjogosultsag = mySQLConnect("SELECT * FROM jogosultsagok WHERE menupont = $menuid AND felhasznalo = $felhasznalo");

            //print_r($xjogosultsag);
            if(mysqli_num_rows($xjogosultsag) == 0)
            {
                //echo "nincs $menuid<br>";
                foreach($_POST as $key => $value)
                {
                    if("sajatolvas-$menuid" == $key)
                    {
                        $stmt = $con->prepare('INSERT INTO jogosultsagok (felhasznalo, menupont, sajatolvas, csoportolvas, mindolvas, sajatir, csoportir, mindir) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                        $stmt->bind_param('ssssssss', $felhasznalo, $menuid, $_POST["sajatolvas-$menuid"], $_POST["csoportolvas-$menuid"], $_POST["mindolvas-$menuid"], $_POST["sajatir-$menuid"], $_POST["csoportir-$menuid"], $_POST["mindir-$menuid"]);
                        $stmt->execute();
                        if(mysqli_errno($con) != 0)
                        {
                            echo "<h2>A jogosultsag hozzáadása sikertelen!<br></h2>";
                            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                        }
                        else
                        {
                            header("Location: $backtosender");
                        }
                        break;
                    }
                }
            }
            else
            {
                // megoldani, hogy a sajatolvas jog elvétele is működjön
                if(isset($_POST["sajatolvas-$menuid"]))
                {
                    $jogid = mysqli_fetch_assoc($xjogosultsag)['id'];
                    $stmt = $con->prepare('UPDATE jogosultsagok SET sajatolvas=?, csoportolvas=?, mindolvas=?, sajatir=?, csoportir=?, mindir=? WHERE id=?');
                    $stmt->bind_param('ssssssi', $_POST["sajatolvas-$menuid"], $_POST["csoportolvas-$menuid"], $_POST["mindolvas-$menuid"], $_POST["sajatir-$menuid"], $_POST["csoportir-$menuid"], $_POST["mindir-$menuid"], $jogid);
                    $stmt->execute();
                    if(mysqli_errno($con) != 0)
                    {
                        echo "<h2>A jogosultsag hozzáadása sikertelen!<br></h2>";
                        echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                    }
                    else
                    {
                        header("Location: $backtosender");
                    }
                }
            }
        }
        //while(isset($_POST["valasz$i"]) && $_POST["valasz$i"] != null)
        //{}
    }
    elseif($_GET["action"] == "delete")
    {
    }
}