
<?php

if(!$contextmenujogok['templateek'])
{
    getPermissionError();
}
else
{
    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/munkalapok/db/templatedb.php");

        $targeturl = "$RootPath/munkalapok/templateek";

        header("Location: $targeturl");
        die;
    }

    $irhat = true;
    $magyarazat = $szoveg = null;
    $form = "modules/munkalapok/forms/templateform";

    if($elemid)
    {
        $template = new MySQLHandler("SELECT id, szoveg FROM munkalaptemplateek WHERE id = ?", $elemid);
        $szoveg = $template->Fetch()['szoveg'];
        $oldalcim = "Munkalap template szerkesztése";
        $button = "Template szerkesztése";
    }
    elseif(isset($_GET['action']) && $_GET['action'] == "addnew")
    {
        $oldalcim = "Template létrehozása a munkalapokhoz";
        $button = "Template létrehozása";
    }
    else
    {
        $irhat = false;
    }

    if(!$irhat)
    {
        getPermissionError();
    }
    else
    {
        include('././templates/edit.tpl.php');
    }
}