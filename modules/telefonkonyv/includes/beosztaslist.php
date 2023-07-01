<?php

if(!isset($novaltozatlan))
{
    include("../../../includes/config.inc.php");
    include("../../../includes/functions.php");
}

$where = $origvolt = null;

if(isset($_GET['eredeti']))
{
    $origsorrend = $_GET["eredeti"];
}

if(!isset($sorrend))
{
    $sorrend = null;
}

if(isset($_GET['csoport']))
{
    $csoport = $_GET["csoport"];
    $where = "WHERE telefonkonyvcsoportok.id = $csoport";
}
elseif(isset($csoport))
{
    $where = "WHERE telefonkonyvcsoportok.id = $csoport";
}


$telkonyvbeosztasok = mySQLConnect("SELECT telefonkonyvbeosztasok.nev AS beosztas,
            telefonkonyvfelhasznalok.nev AS dolgozo,
            telefonkonyvbeosztasok.sorrend AS sorrend,
            telefonkonyvcsoportok.sorrend AS csoportsorrend
        FROM telefonkonyvbeosztasok
            LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok.felhid = telefonkonyvfelhasznalok.id
            LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
        $where
    UNION
        SELECT telefonkonyvvaltozasok.beosztasnev AS beosztas,
            IF(telefonkonyvfelhasznalok.nev, telefonkonyvfelhasznalok.nev, telefonkonyvvaltozasok.nev) AS dolgozo,
            telefonkonyvvaltozasok.sorrend AS sorrend,
            telefonkonyvcsoportok.sorrend AS csoportsorrend
        FROM telefonkonyvvaltozasok
            LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvvaltozasok.felhid = telefonkonyvfelhasznalok.id
            LEFT JOIN telefonkonyvcsoportok ON telefonkonyvvaltozasok.csoport = telefonkonyvcsoportok.id
        $where");

$telkonyvbeosztasok = mysqliToArray($telkonyvbeosztasok);

$csoportsorrend  = array_column($telkonyvbeosztasok, 'csoportsorrend');
$sorrend = array_column($telkonyvbeosztasok, 'sorrend');
array_multisort($csoportsorrend, SORT_ASC, $sorrend, SORT_ASC, $telkonyvbeosztasok);

    $sorrendszamlalo = 0;
    
    foreach($telkonyvbeosztasok as $x)
    {
        $selected = null;
        if($sorrendszamlalo == 0)
        {
            $minushalf = $x['sorrend'] - 0.5;
            if($origsorrend != $sorrend && $minushalf == $sorrend)
            {
                $selected = "selected";
                $origvolt = true;
            }
            ?><option value="<?=$minushalf?>" <?=$selected?>><?=$x['beosztas']?> - <?=($x['dolgozo']) ? $x['dolgozo'] : "BETÖLTETLEN" ?> =>> Felett</option><?php
            $selected = null;
        }
        if($origsorrend == $x['sorrend'])
        {
            ?><option value="<?=$origsorrend?>" selected><?=$x['beosztas']?> - <?=($x['dolgozo']) ? $x['dolgozo'] : "BETÖLTETLEN" ?> ==>> !! JELENLEGI HELY !!</option><?php
            $origvolt = true;
            $selected = null;
        }
        $plushalf = $x['sorrend'] + 0.5;
        if($origsorrend != $sorrend && $plushalf == $sorrend)
        {
            $selected = "selected";
            $origvolt = true;
        }
        ?><option value="<?=$plushalf?>" <?=$selected?>><?=$x['beosztas']?> - <?=($x['dolgozo']) ? $x['dolgozo'] : "BETÖLTETLEN" ?> =>> Alatt</option><?php
        $sorrendszamlalo++;
    }

    if(count($telkonyvbeosztasok) == 0 || !$origvolt)
    {
        ?><option value="999999" selected>!!! UTOLSÓ ELEM !!!</option><?php
    }
?>