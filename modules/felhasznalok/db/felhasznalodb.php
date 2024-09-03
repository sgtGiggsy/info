<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_GET["action"] == "new")
    {
        $elsobelepes = null;
        $stmt = $con->prepare('INSERT INTO felhasznalok (felhasznalonev, nev, email, telefon, szervezet, elsobelepes) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssss', $_POST['felhasznalonev'], $_POST['nev'], $_POST['email'], $_POST['telefon'], $_POST['szervezet'], $elsobelepes);
        $stmt->execute();

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Felhasználó hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }

    elseif($_GET["action"] == "update")
    {
        $elsobelepes = null;
        $stmt = $con->prepare('UPDATE felhasznalok SET felhasznalonev=?, nev=?, email=?, telefon=?, szervezet=? WHERE id=?');
        $stmt->bind_param('ssssss', $_POST['felhasznalonev'], $_POST['nev'], $_POST['email'], $_POST['telefon'], $_POST['szervezet'], $_POST['id']);
        $stmt->execute();

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Felhasználó szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }

    elseif($_GET["action"] == "permissions")
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
                    if("olvasas-$menuid" == $key && ($_POST["olvasas-$menuid"] != 0))
                    {
                        $stmt = $con->prepare('INSERT INTO jogosultsagok (felhasznalo, menupont, olvasas, iras) VALUES (?, ?, ?, ?)');
                        $stmt->bind_param('ssss', $felhasznalo, $menuid, $_POST["olvasas-$menuid"], $_POST["iras-$menuid"]);
                        $stmt->execute();
                        if(mysqli_errno($con) != 0)
                        {
                            echo "<h2>A jogosultsag hozzáadása sikertelen!<br></h2>";
                            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                        }
                        break;
                    }
                }
            }
            else
            {
                $jogid = mysqli_fetch_assoc($xjogosultsag)['id'];
                $stmt = $con->prepare('UPDATE jogosultsagok SET olvasas=?, iras=? WHERE id=?');
                $stmt->bind_param('ssi', $_POST["olvasas-$menuid"], $_POST["iras-$menuid"], $jogid);
                $stmt->execute();
                if(mysqli_errno($con) != 0)
                {
                    echo "<h2>A jogosultsag hozzáadása sikertelen!<br></h2>";
                    echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                }
            }
        }
        //while(isset($_POST["valasz$i"]) && $_POST["valasz$i"] != null)
        //{}
    }

    elseif($_GET['action'] == "sync")
    {
        $ldapusername = $_POST['felhasznalonev'] . "@" . $LDAP_DOMAIN;
        $plainpassword = $_POST['jelszo'];

        $con = mySQLConnect(false);

        foreach($LDAP_SERVERS as $x)
        {
            if(checkLDAPConnection($x))
            {
                $ldapconnection = ldap_connect($x, $LDAP_PORT); // LDAP kapcsolat inicializálása
                
                ldap_set_option($ldapconnection, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($ldapconnection, LDAP_OPT_REFERRALS, 0);
                $ldapbind = ldap_bind($ldapconnection, $ldapusername, $plainpassword); // LDAP bejelentkezés, mivel nem errort, csak warningot dobhat hiba esetén, el kell nyomni a hibaüzenetet
                if($ldapbind)
                {
                    $szervezetnevarray = array(); // Gyorsítótárazzuk a szervezetneveket, hogy ne kelljen annyi SQL lekérdezést végrehajtani
                    $felhasznalok = mySQLConnect("SELECT * FROM felhasznalok;");
                    foreach($felhasznalok as $felhasznalo)
                    {
                        $samaccountname = $felhasznalo['felhasznalonev'];
                        $filter = "(&(objectClass=user)(sAMAccountName=$samaccountname))";
                        $ldapsearch = ldap_search($ldapconnection, $LDAP_DIR, $filter); // A felhasználó adatainak lekérése a DC-től
                        if($ldapsearch)
                        {
                            $ldapresults = ldap_get_entries($ldapconnection, $ldapsearch);
                            // Ha nincs email, vagy megjelenő név valakinél megadva, warningot dobna a lekérés, így el kell nyomnunk az esetleges hibaüzenetet
                            if(@$ldapresults[0]['displayname'][0])
                            {
                                if(isset($ldapresults[0]['company'][0]))
                                {
                                    if(array_key_exists($ldapresults[0]['company'][0], $szervezetnevarray))
                                    {
                                        $szervezet = $szervezetnevarray[$ldapresults[0]['company'][0]];
                                    }
                                    else
                                    {
                                        $szervezet = szervezetValaszto($ldapresults[0]['company'][0]);
                                        $szervezetnevarray[$ldapresults[0]['company'][0]] = $szervezet;
                                    }
                                }
                                else
                                {
                                    $szervezet = null;
                                }
                                @$email = $ldapresults[0]['mail'][0];
                                @$nev = $ldapresults[0]['displayname'][0];
                                @$osztaly = $ldapresults[0]['department'][0];
                                @$telefon = $ldapresults[0]['telephonenumber'][0];
                                @$beosztas = $ldapresults[0]['title'][0];
                                @$thumb = $ldapresults[0]['thumbnailphoto'][0];
        
                                if ($stmt = $con->prepare('UPDATE felhasznalok SET nev=?, email=?, osztaly=?, szervezet=?, telefon=?, beosztas=?, profilkep=? WHERE felhasznalonev=?'))
                                {
                                    $stmt->bind_param('ssssssss', $nev, $email, $osztaly, $szervezet, $telefon, $beosztas, $thumb, $samaccountname);
                                    $stmt->execute();
                                }
                            }
                        }
                    }
                }
                else
                {
                    echo "A megadott felhasználónév, vagy jelszó nem megfelelő!";
                }
                break;
            }
            else
            {
                echo "$x AD kiszolgáló nem elérhető!<br>";
            }
        }
    }

    elseif($_GET["action"] == "delete")
    {
    }
}