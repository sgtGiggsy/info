<?php

if(isset($irhat) && $irhat)
{
    $jelkezeldb = new mySQLHandler();

    if($_GET["action"] == "addnew" || $_GET["action"] == "update")
    {
        $encoded = encPass($_POST['pass']);

        // Nem hibából raktam ide. A purifyPost eltávolít bizonyos speciális karaktereket a POST tartalmakból, viszont ezek a karakterek a jelszavakban engedélyezettek,
        // így a purifyPost lehetséges, hogy hibásan tárolná őket
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
        if(!is_null($_POST['newpass']))
        {
            $plainpassword = $_POST['newpass'];
            $hashedpassword = password_hash($plainpassword, PASSWORD_DEFAULT);
            $jelkezeldb->Query('UPDATE jelszokezelo_beallitasok SET masterpass = ? WHERE id = 1', $hashedpassword);
        }
    }

    $jelkezeldb->Close();
}