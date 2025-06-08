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
        $menu = mySQLConnect("SELECT * FROM menupontok ORDER BY menupont ASC");
        foreach($menu as $x)
        {
            $menuid = $x['id'];
            //echo "lekér $menuid <br>";
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
                //TODO Újraírás során UPDATE helyett DELETE FROM, ha az írás és olvasás is NULL
                $olvasas = $_POST["olvasas-$menuid"];
                $iras = $_POST["iras-$menuid"];
                $iras = ($iras == 0) ? null : $iras;
                $olvasas = ($olvasas == 0) ? null : $olvasas;
                $jogid = mysqli_fetch_assoc($xjogosultsag)['id'];
                $stmt = $con->prepare('UPDATE jogosultsagok SET olvasas=?, iras=? WHERE id=?');
                $stmt->bind_param('ssi', $olvasas, $iras, $jogid);
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
                    $stmt = $con->prepare('UPDATE felhasznalok SET nev=?, email=?, osztaly=?, szervezet=?, telefon=?, beosztas=?, profilkep=?, descript=? WHERE felhasznalonev=?');
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
                                @$descript = $ldapresults[0]['description'][0];
        
                                $stmt->bind_param('sssssssss', $nev, $email, $osztaly, $szervezet, $telefon, $beosztas, $thumb, $descript, $samaccountname);
                                $stmt->execute();
                            }
                            else
                            {
                                mySQLConnect("UPDATE felhasznalok SET aktiv = 0 WHERE felhasznalonev = '$samaccountname'");
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

    elseif($_GET["action"] == "syncou")
    {
        $ldapusername = $_POST['felhasznalonev'] . "@" . $LDAP_DOMAIN;
        $plainpassword = $_POST['jelszo'];

        $con = mySQLConnect(false);
        $ldapfelhasznalok = array();
        $elsobelepes = null;

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
                    $szervezetnevarray = array();
                    $userek = ldap_search($ldapconnection, ConvertToDistinguishedName($_POST['ou']), "(&(objectClass=user)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))", array('samAccountName', 'mail', 'displayName', 'telephoneNumber', 'company', 'thumbnailphoto', 'department', 'title'));
                    $info = ldap_get_entries($ldapconnection, $userek);
                    foreach($info as $user)
                    {
                        if(@$user['samaccountname'][0])
                        {
                            $samaccountname = $user['samaccountname'][0];
                            @$mail = $user['mail'][0];
                            @$displayName = $user['displayname'][0];
                            @$telephoneNumber = $user['telephonenumber'][0];
                            @$company = $user['company'][0];
                            @$thumb = $user['thumbnailphoto'][0];
                            @$department = $user['department'][0];
                            @$title = $user['title'][0];
                            $ldapfelhasznalok[$samaccountname] = array(
                                'samaccountname' => $samaccountname,
                                'mail' => $mail,
                                'displayName' => $displayName,
                                'telephoneNumber' => $telephoneNumber,
                                'company' => $company,
                                'thumb' => $thumb,
                                'department' => $department,
                                'title' => $title
                            );
                        }
                        else
                        {
                            var_dump($user);
                            echo "<br>";
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

        $felhasznalolista = mySQLConnect("SELECT felhasznalonev FROM felhasznalok;");
        $felhasznalolista = mysqliToArray($felhasznalolista, true);
        $stmtupdate = $con->prepare('UPDATE felhasznalok SET nev=?, email=?, osztaly=?, szervezet=?, telefon=?, beosztas=?, profilkep=? WHERE felhasznalonev=?');
        $stmtinsert = $con->prepare('INSERT INTO felhasznalok (felhasznalonev, nev, email, elsobelepes, osztaly, szervezet, telefon, beosztas, profilkep) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');

        foreach($ldapfelhasznalok as $ldapfelh)
        {
            if(isset($ldapfelh['company']) && $ldapfelh['company'] != null)
            {
                if(array_key_exists($ldapfelh['company'], $szervezetnevarray))
                {
                    $szervezet = $szervezetnevarray[$ldapfelh['company']];
                }
                else
                {
                    $szervezet = szervezetValaszto($ldapfelh['company']);
                    $szervezetnevarray[$ldapfelh['company']] = $szervezet;
                }
            }
            else
            {
                $szervezet = null;
            }

            if(!in_array($ldapfelh['samaccountname'], $felhasznalolista))
            {
                $stmtinsert->bind_param('sssssssss', $ldapfelh['samaccountname'], $ldapfelh['displayName'], $ldapfelh['mail'], $elsobelepes, $ldapfelh['department'], $szervezet, $ldapfelh['telephoneNumber'], $ldapfelh['title'], $ldapfelh['thumb']);
                $stmtinsert->execute();
            }
            else
            {
                $stmtupdate->bind_param('ssssssss', $ldapfelh['displayName'], $ldapfelh['mail'], $ldapfelh['department'], $szervezet, $ldapfelh['telephoneNumber'], $ldapfelh['title'], $ldapfelh['thumb'], $samaccountname);
                $stmtupdate->execute();
            }
        }
    }

    elseif($_GET["action"] == "delete")
    {
    }
}