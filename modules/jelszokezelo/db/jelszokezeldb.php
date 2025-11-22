<?php

if(isset($irhat) && $irhat && isset($_SESSION['unlockedmaster']) && $_SESSION['unlockedmaster'])
{
    $jelkezeldb = new mySQLHandler();

    if($_GET["action"] == "addnew" || $_GET["action"] == "update")
    {
        $encoded = encPass($_POST['pass']);

        // Nem hibából raktam ennyire hátra. A purifyPost eltávolít bizonyos speciális karaktereket a POST tartalmakból,
        // viszont ezek a karakterek a jelszavakban engedélyezettek, így a purifyPost miatt hibásan kerülnének mentésre.
        // Az adatbázisba úgyis a kódolt verzió megy, így prepared statement nélkül sem lehetne SQL injection-re használni.
        purifyPost();

        if($_GET["action"] == "addnew")
        {
            $jelkezeldb->Query('INSERT INTO jelszokezelo_jelszavak (uname, pass, leiras) VALUES (?, ?, ?)', $_POST['uname'], $encoded, $_POST['leiras']);
        }
        elseif($_GET["action"] == "update")
        {
            $jelkezeldb->Query('UPDATE jelszokezelo_jelszavak SET uname=?, pass=?, leiras=? WHERE id=?', $_POST['uname'], $encoded, $_POST['leiras'], $_POST['id']);
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
    elseif($_GET["action"] == "masterpass" && $mindir)
    {
        //TODO: Megcsinálni, hogy mesterjelszó váltásnál a rendszer felajánlja az összes meglévő jelszó újrakódolását
        
        if(!is_null($_POST['newpass']))
        {
            $plainpassword = $_POST['newpass'];
            $hashedpassword = password_hash($plainpassword, PASSWORD_DEFAULT);
            $jelkezeldb->Query('UPDATE jelszokezelo_beallitasok SET masterpass = ? WHERE id = 1', $hashedpassword);
        }
    }

    $jelkezeldb->Close();
}
else
{
    $popup['type'] = "error";
    $popup['message'] = "A jelszókezelő zárolt állapotban van! Ilyenkor nem lehet módosításokat végezni!";
}