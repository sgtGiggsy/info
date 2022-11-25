<?php

if(@!$mindir)
{
    getPermissionError();
}
else
{
    $gyarto = $modell = $tipus = $szines = $scanner = $fax = $defadmin = $defpass = $maxmeret = $magyarazat = null;
    if(isset($_GET['tipus']))
    {
        $tipusnev = $_GET['tipus'];
        if($tipusnev == "nyomtato")
        {
            $tipus = "12";
        }
    }

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

    $tipusok = mySQLConnect("SELECT * FROM eszkoztipusok ORDER BY nev ASC");

    if(isset($_GET['id']))
    {
        $modellid = $_GET['id'];
        $modellszerk = mySQLConnect("SELECT * FROM modellek WHERE id = $modellid;");
        $modellszerk = mysqli_fetch_assoc($modellszerk);

        $gyarto = $modellszerk['gyarto'];
        $modell = $modellszerk['modell'];
        $tipus = $modellszerk['tipus'];

        if(@$tipusnev == "nyomtato" || @$tipus == "12")
        {
            $tipusnev = "nyomtato";
            $tipus = "12";
            $nyomtato = mySQLConnect("SELECT * FROM nyomtatomodellek WHERE modell = $modellid;");
            $nyomtato = mysqli_fetch_assoc($nyomtato);

            $szines = @$nyomtato['szines'];
            $scanner = @$nyomtato['scanner'];
            $fax = @$nyomtato['fax'];
            $maxmeret = @$nyomtato['maxmeret'];
        }

        if(@$tipusnev == "mediakonverter" || @$tipus > 20 && @$tipus < 26)
        {
            $tipusnev = "mediakonverter";
            $fizikairetegek = mySQLConnect("SELECT * FROM fizikairetegek;");
            $csatlakozok = mySQLConnect("SELECT * FROM csatlakozotipusok;");
            $sebessegek = mySQLConnect("SELECT * FROM sebessegek;");
            $atviteliszabvanyok = mySQLConnect("SELECT * FROM atviteliszabvanyok;");

            $mediakonverter = mySQLConnect("SELECT * FROM mediakonvertermodellek WHERE modell = $modellid;");
            $mediakonverter = mysqli_fetch_assoc($mediakonverter);
            $fizikaireteg = @$mediakonverter['fizikaireteg'];
            $transzpszabvany = @$mediakonverter['transzpszabvany'];
            $transzpcsatlakozo = @$mediakonverter['transzpcsatlakozo'];
            $transzpsebesseg = @$mediakonverter['transzpsebesseg'];
            $lanszabvany = @$mediakonverter['lanszabvany'];
            $lancsatlakozo = @$mediakonverter['lancsatlakozo'];
            $lansebesseg = @$mediakonverter['lansebesseg'];
        }

        if(@$tipusnev == "bovitomodul" || @$tipus > 25 && @$tipus < 31)
        {
            $tipusnev = "bovitomodul";
            $fizikairetegek = mySQLConnect("SELECT * FROM fizikairetegek;");
            $csatlakozok = mySQLConnect("SELECT * FROM csatlakozotipusok;");
            $sebessegek = mySQLConnect("SELECT * FROM sebessegek;");
            $atviteliszabvanyok = mySQLConnect("SELECT * FROM atviteliszabvanyok;");

            $mediakonverter = mySQLConnect("SELECT * FROM bovitomodellek WHERE modell = $modellid;");
            $mediakonverter = mysqli_fetch_assoc($mediakonverter);
            $fizikaireteg = @$mediakonverter['fizikaireteg'];
            $transzpszabvany = @$mediakonverter['transzpszabvany'];
            $transzpcsatlakozo = @$mediakonverter['transzpcsatlakozo'];
            $transzpsebesseg = @$mediakonverter['transzpsebesseg'];
        }

        if (!isset($tipusnev))
        {
            $tipusnev = eszkozTipusValaszto($tipus);
        }

        $button = "Szerkesztés";
        $oldalcim = "Modell szerkesztése";

    }

    include('././templates/edit.tpl.php');

}