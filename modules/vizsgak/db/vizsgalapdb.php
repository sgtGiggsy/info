<?php
if(!$irhat)
{
    getPermissionError();
}
else
{

    if($_GET["action"] == "addnew")
    {
        $last = new MySQLHandler("SELECT MAX(id) AS utolso FROM vizsgak_vizsgalapok");
        $last->Bind($lastid);

        if(!$lastid)
        {
            $lastid = 0;
        }

        $lastid++;

        $azonosito = date("Ymd") . $vizsgaid . $felhasznaloid . $lastid;
        
        $vizgalap = new MySQLHandler("INSERT INTO vizsgak_vizsgalapok (vizsgaid, letrehozo, azonosito, megoldokulcs) VALUES (?, ?, ?, ?)",
            $vizsgaid, $felhasznaloid, $azonosito, $megoldokulcs);

        $vizsgalapid = $vizgalap->last_insert_id;

        $vizsgalapDB = new MySQLHandler();
        $vizsgalapDB->Prepare("INSERT INTO vizsgak_vizsgalapkerdesek (vizsgalapid, kerdesid) VALUES (?, ?)");
        foreach($vizsgalapkerdesei as $kerdes)
        {
            $vizsgalapDB->Run($vizsgalapid, $kerdes);
        }
        $vizsgalapDB->Close();
    }

    elseif($_GET["action"] == "delete")
    {
        $disable = 0;
        $del = new MySQLHandler("UPDATE vizsgak_vizsgalapok SET aktiv = ? WHERE id = ?", $disable, $_GET['lapid']);
    }
}