<?php
if(!$irhat)
{
    getPermissionError();
}
else
{
    $con = mySQLConnect(false);

    if($_GET["action"] == "addnew")
    {
        $lastid = mySQLConnect("SELECT MAX(id) AS utolso FROM vizsgak_vizsgalapok;");
        $lastid = mysqli_fetch_assoc($lastid)['utolso'];

        if(!$lastid)
        {
            $lastid = 0;
        }

        $lastid++;

        $azonosito = date("Ymd") . $vizsgaid . $felhasznaloid . $lastid;
        
        $stmt = $con->prepare('INSERT INTO vizsgak_vizsgalapok (vizsgaid, letrehozo, azonosito, megoldokulcs) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $vizsgaid, $felhasznaloid, $azonosito, $megoldokulcs);
        $stmt->execute();

        $vizsgalapid = mysqli_insert_id($con);

        $vizsgalapkerdesinsertstring = "INSERT INTO `vizsgak_vizsgalapkerdesek` (`vizsgalapid`, `kerdesid`) VALUES ";
        foreach($vizsgalapkerdesei as $kerdes)
        {
            $vizsgalapkerdesinsertstring .= "($vizsgalapid, $kerdes), ";
        }

        $vizsgalapkerdesinsertstring = rtrim($vizsgalapkerdesinsertstring, ", ");
        $vizsgalapkerdesinsertstring .= ";";

        mySQLConnect($vizsgalapkerdesinsertstring);
    }
}