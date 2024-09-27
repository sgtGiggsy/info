<?php

if(@!$mindir)
{
    getPermissionError();
}
else
{
    $gyarto = $modell = $tipus = $szines = $scanner = $fax = $defadmin = $defpass = $maxmeret = $magyarazat = $fizikaireteg =
    $transzpszabvany = $transzpcsatlakozo = $transzpsebesseg = $lanszabvany = $lancsatlakozo = $lansebesseg = $modellid = $tipusnev = null;

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/alap/db/modelldb.php");
        
        redirectToGyujto("modelleklistaja");
    }
    
    $button = "Új modell";
    $irhat = true;
    $form = "modules/alap/forms/modellszerkesztform";
    $oldalcim = "Új modell rögzítése";

    $tipusok = new MySQLHandler("SELECT * FROM eszkoztipusok ORDER BY nev ASC");
    $tipusok = $tipusok->Result();

    if(isset($_GET['id']))
    {
        $where = null;
        $modellid = $_GET['id'];
        if($modellid)
            $where = "WHERE modell = ?";
        
        $modellszerk = new MySQLHandler("SELECT gyarto, modell, tipus FROM modellek WHERE id = ?;", $modellid);
        $modellszerk->Bind($gyarto, $modell, $tipus);
        $tipusnev = eszkozTipusValaszto($tipus);

        if(@$tipus == "12")
        {
            $nyomtato = new MySQLHandler("SELECT szines, scanner, fax, maxmeret FROM nyomtatomodellek $where;", $modellid);
            $nyomtato->Bind($szines, $scanner, $fax, $maxmeret);
        }

        if(@$tipus > 20 && @$tipus < 31)
        {
            $sql = new MySQLHandler();

            $sql->Query("SELECT * FROM fizikairetegek;", null, true);
            $fizikairetegek = $sql->AsArray();

            $sql->Query("SELECT * FROM csatlakozotipusok;", null, true);
            $csatlakozok = $sql->AsArray();

            $sql->Query("SELECT * FROM sebessegek;", null, true);
            $sebessegek = $sql->AsArray();

            $sql->Query("SELECT * FROM atviteliszabvanyok;");
            $atviteliszabvanyok = $sql->AsArray();
        }

        if(@$tipus > 20 && @$tipus < 26)
        {
            $mediakonverter = new MySQLHandler("SELECT fizikaireteg, transzpszabvany, transzpcsatlakozo, transzpsebesseg, lanszabvany, lancsatlakozo, lansebesseg FROM mediakonvertermodellek $where;", $modellid, true);
            $mediakonverter->Bind($fizikaireteg, $transzpszabvany, $transzpcsatlakozo, $transzpsebesseg, $lanszabvany, $lancsatlakozo, $lansebesseg);
        }

        if(@$tipus > 25 && @$tipus < 31)
        {
            $bovitomodul = new MySQLHandler("SELECT fizikaireteg, transzpszabvany, transzpcsatlakozo, transzpsebesseg FROM bovitomodellek $where;", $modellid);
            $bovitomodul->Bind($fizikaireteg, $transzpszabvany, $transzpcsatlakozo, $transzpsebesseg);
        }

        $button = "Szerkesztés";
        $oldalcim = "Modell szerkesztése";

    }

    include('././templates/edit.tpl.php');

}