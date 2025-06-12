<?php

if(@!$mindir)
{
    getPermissionError();
}
else
{
    $gyarto = $modell = $tipus = $szines = $scanner = $fax = $defadmin = $defpass = $maxmeret = $magyarazat = $fizikaireteg =
    $transzpszabvany = $transzpcsatlakozo = $transzpsebesseg = $lanszabvany = $lancsatlakozo = $lansebesseg = $tipusnev = null;
    $fizikairetegek = $csatlakozok = $sebessegek = $atviteliszabvanyok = array();

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/eszkozok/db/modelldb.php");
        
        redirectToGyujto("modelleklistaja");
    }
    
    $button = "Új modell";
    $irhat = true;
    $form = "modules/eszkozok/forms/modellszerkesztform";
    $oldalcim = "Új modell rögzítése";

    $tipusok = new MySQLHandler("SELECT * FROM eszkoztipusok ORDER BY nev ASC");
    $tipusok = $tipusok->Result();

    if($elemid)
    {
        $modellszerk = new MySQLHandler("SELECT gyarto, modell, tipus FROM modellek WHERE id = ?;", $elemid);
        $modellszerk->Bind($gyarto, $modell, $tipus);
        $tipusnev = eszkozTipusValaszto($tipus);

        if(@$tipus == "12")
        {
            $nyomtato = new MySQLHandler("SELECT szines, scanner, fax, maxmeret, defadmin, defpass FROM nyomtatomodellek WHERE modell = ?;", $elemid);
            $nyomtato->Bind($szines, $scanner, $fax, $maxmeret, $defadmin, $defpass);
        }

        if(@$tipus > 20 && @$tipus < 31)
        {
            $sql = new MySQLHandler();

            $sql->Query("SELECT * FROM fizikairetegek;");
            $fizikairetegek = $sql->AsArray();

            $sql->Query("SELECT * FROM csatlakozotipusok;");
            $csatlakozok = $sql->AsArray();

            $sql->Query("SELECT * FROM sebessegek;");
            $sebessegek = $sql->AsArray();

            $sql->Query("SELECT * FROM atviteliszabvanyok;");
            $atviteliszabvanyok = $sql->AsArray();

            $sql->Close();
        }

        if(@$tipus > 20 && @$tipus < 26)
        {
            $mediakonverter = new MySQLHandler("SELECT fizikaireteg, transzpszabvany, transzpcsatlakozo, transzpsebesseg, lanszabvany, lancsatlakozo, lansebesseg FROM mediakonvertermodellek WHERE modell = ?;", $elemid);
            $mediakonverter->Bind($fizikaireteg, $transzpszabvany, $transzpcsatlakozo, $transzpsebesseg, $lanszabvany, $lancsatlakozo, $lansebesseg);
        }

        if(@$tipus > 25 && @$tipus < 31)
        {
            $bovitomodul = new MySQLHandler("SELECT fizikaireteg, transzpszabvany, transzpcsatlakozo, transzpsebesseg FROM bovitomodellek WHERE modell = ?;", $elemid);
            $bovitomodul->Bind($fizikaireteg, $transzpszabvany, $transzpcsatlakozo, $transzpsebesseg);
        }

        $button = "Szerkesztés";
        $oldalcim = "Modell szerkesztése";

    }

    include('././templates/edit.tpl.php');

}