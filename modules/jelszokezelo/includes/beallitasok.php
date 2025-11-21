<?php

if(@!$mindir)
{
    getPermissionError();
}
else
{
    if(count($_POST) > 0)
    {
        if($_POST['newpass'] != $_POST['newpassrepeat'])
        {
            ?><script type='text/javascript'>alert('A beírt jelszavak nem egyeznek!')</script><?php
        }
        else
        {
            $mpassorig = new MySQLHandler('SELECT masterpass FROM jelszokezelo_beallitasok WHERE id = 1');
            $mpassorig->Bind($oldpass);

            if(!password_verify($_POST['oldpass'], $oldpass))
            {
                ?><script type='text/javascript'>alert('Hibás a régi jelszó!')</script><?php
            }
            else
            {
                $irhat = true;
                include("./modules/jelszokezelo/db/jelszokezeldb.php");
            }
        }
    }

    $nev = $magyarazat = null;
    $button = "Mentés";
    $irhat = true;
    $form = "modules/jelszokezelo/forms/beallitasform";
    $oldalcim = "Mester jelszó beállítása";

    include('././templates/edit.tpl.php');
}