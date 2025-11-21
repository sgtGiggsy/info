<?php

if(@!$mindir)
{
    getPermissionError();
}
else
{
    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/jelszokezelo/db/jelszokezeldb.php");

        if($jelkezeldb->siker)
            redirectToGyujto("jelszavak");
    }

    $uname = $passw = $leiras = null;
    $button = "Mentés";
    $irhat = true;
    $form = "modules/jelszokezelo/forms/jelszoform";
    $oldalcim = "Új jelszó rögzítése";

    if($elemid)
    {
        $jelszoszerkeszt = new MySQLHandler("SELECT * FROM jelszokezelo_jelszavak WHERE id = ?;", $elemid);
        $jelszoszerkeszt = $jelszoszerkeszt->Fetch();

        $id = $jelszoszerkeszt['id'];
        $uname = $jelszoszerkeszt['uname'];
        $leiras = $jelszoszerkeszt['leiras'];

        $oldalcim = "Jelszó szerkesztése";
    }

    include('././templates/edit.tpl.php');
}