<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

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
                    if("csoportolvas-$menuid" == $key)
                    {
                        $stmt = $con->prepare('INSERT INTO jogosultsagok (felhasznalo, menupont, csoportolvas, mindolvas, sajatir, mindir) VALUES (?, ?, ?, ?, ?, ?)');
                        $stmt->bind_param('ssssss', $felhasznalo, $menuid, $_POST["csoportolvas-$menuid"], $_POST["mindolvas-$menuid"], $_POST["sajatir-$menuid"], $_POST["mindir-$menuid"]);
                        $stmt->execute();
                        if(mysqli_errno($con) != 0)
                        {
                            echo "<h2>A válasz hozzáadása sikertelen!<br></h2>";
                            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                        }
                        break;
                    }
                }
            }
            else
            {
                if(isset($_POST["csoportolvas-$menuid"]))
                {
                    $jogid = mysqli_fetch_assoc($xjogosultsag)['id'];
                    $stmt = $con->prepare('UPDATE jogosultsagok SET csoportolvas=?, mindolvas=?, sajatir=?, mindir=? WHERE id=?');
                    $stmt->bind_param('ssssi', $_POST["csoportolvas-$menuid"], $_POST["mindolvas-$menuid"], $_POST["sajatir-$menuid"], $_POST["mindir-$menuid"], $jogid);
                    $stmt->execute();
                    if(mysqli_errno($con) != 0)
                    {
                        echo "<h2>A válasz hozzáadása sikertelen!<br></h2>";
                        echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
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