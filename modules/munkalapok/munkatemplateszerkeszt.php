
<?php

if(!$csoportir)
{
    getPermissionError();
}
else
{
    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/munkalapok/db/templatedb.php");

        $targeturl = "$RootPath/munkalaptemplateek";

        header("Location: $targeturl");
    }

    $irhat = true;
    $magyarazat = null;
    $form = "modules/munkalapok/forms/templateform";

    if(isset($_GET['id']))
    {
        $template = mySQLConnect("SELECT id, szoveg FROM munkalaptemplateek WHERE id = $id;");
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