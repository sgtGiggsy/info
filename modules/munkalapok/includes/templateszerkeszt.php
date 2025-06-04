
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

    if(isset($_GET['param']))
    {
        $id = $_GET['param'];
        $template = mySQLConnect("SELECT id, szoveg FROM munkalaptemplateek WHERE id = $id;");
        $szoveg = mysqli_fetch_assoc($template)['szoveg'];
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