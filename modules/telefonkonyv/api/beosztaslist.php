<?php

if(!headers_sent())
    header('Content-Type: Content-Type: text/html; charset=utf-8');

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
elseif(isset($csoportid))
{
    $where = "WHERE telefonkonyvcsoportok.id = $csoportid";
}


$telkonyvbeosztasok = new MySQLHandler("SELECT telefonkonyvbeosztasok.nev AS beosztas,
            telefonkonyvfelhasznalok.nev AS dolgozo,
            telefonkonyvbeosztasok.sorrend AS sorrend,
            telefonkonyvcsoportok.sorrend AS csoportsorrend
        FROM telefonkonyvbeosztasok
            LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok.felhid = telefonkonyvfelhasznalok.id
            LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
        $where");
$telkonyvbeosztasok = $telkonyvbeosztasok->AsArray();

$csoportsorrend  = array_column($telkonyvbeosztasok, 'csoportsorrend');
$sorrendszerint = array_column($telkonyvbeosztasok, 'sorrend');
array_multisort($csoportsorrend, SORT_ASC, $sorrendszerint, SORT_ASC, $telkonyvbeosztasok);

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