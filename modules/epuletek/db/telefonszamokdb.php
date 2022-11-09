<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    if($_GET["action"] == "szamtarsitas") // Verziókövetés kész
    {
        $postid = 1;
        $ujsor = true;
        $nullval = null;

        // Addig ismételjük a ciklust, amíg létezik az adott névvel sor
        do
        {
            // Ha ilyen névvel nem érkezett POST érték, akkor a lista végére értünk
            // az $ujsor változó false-ra álítódik. Ha van érték, megkezdjük vele a műveleteket.
            if(isset($_POST['portid-'.$postid]))
            {
                $szamid = 1;
                $ujszam = true;
                do
                {
                    // Ha nem érkezett ilyen POST névvel érték, kilépés a ciklusból.
                    // Ha igen, adatbázis műveletek megkezdése
                    if(isset($_POST['telefonszam-' . $postid . "-" . $szamid]))
                    {
                        // Ha null érték érkezett a formból, de létezett az adott azonosítóhoz rejtett
                        // nullázáshoz létrehozott input, úgy az adott szám porttársításának törlése
                        if($_POST['telefonszam-' . $postid . "-" . $szamid] == null && isset($_POST['nullid-' . $postid . "-" . $szamid]))
                        {
                            $stmt = $con->prepare('UPDATE telefonszamok SET port=? WHERE id=?');
                            $stmt->bind_param('ss', $nullval, $_POST['nullid-' . $postid . "-" . $szamid]);
                            $stmt->execute();
                        }

                        // Ha új számkirendezés történt egy portra, vagy a számkirendezés módosításra került
                        // úgy újabb adatbázisművelet
                        else
                        {
                            // Ha a port már egy számhoz volt rendelve, de a hozzárendelés megváltoztatásra kerül,
                            // úgy először töröljük a régi hozzárendelést az előző portról
                            if(isset($_POST['nullid-' . $postid . "-" . $szamid]) && $_POST['nullid-' . $postid . "-" . $szamid] != $_POST['telefonszam-' . $postid . "-" . $szamid])
                            {
                                $stmt = $con->prepare('UPDATE telefonszamok SET port=? WHERE id=?');
                                $stmt->bind_param('ss', $nullval, $_POST['nullid-' . $postid . "-" . $szamid]);
                                $stmt->execute();
                            }
                            
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