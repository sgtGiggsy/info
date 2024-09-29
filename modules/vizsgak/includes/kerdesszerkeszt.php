<?php
if(@!$contextmenujogok['kerdesszerkeszt'])
{
	echo "<h2>Nincs jogosultságod az oldal megtekintésére!</h2>";
}
else
{
    if(count($_POST) > 0 && isset($_GET['action']))
    {
        $irhat = true;
        include("./modules/vizsgak/db/kerdesdb.php");
        $targeturl = "$RootPath/vizsga/" . $vizsgaadatok['url'] . "/kerdeslista";

        header("Location: $targeturl");
    }

    $button = "Mentés";
    $irhat = true;
    $form = "modules/vizsgak/forms/kerdesszerkesztform";
    $oldalcim = "Kérdés hozzáadása";
    $kerdesszoveg = $kep = $magyarazat = null;
    $valaszlehetosegek = array();
    $szerkparams = array();
    
    $kep = null;
    if(isset($_GET['id']))
    {
        $jogszukit = null;
        if(!$mindir)
        {
            $jogszukit = "AND vizsgak_kerdesek.vizsga IN (SELECT vizsga FROM vizsgak_adminok WHERE felhasznalo = $felhasznaloid)";
            $szerkparams[] = $felhasznaloid;
        }
        $szerkparams[] = $_GET['id'];
        $kerdesadat = new MySQLHandler("SELECT vizsgak_kerdesek.id AS kerdesid,
                    vizsgak_kerdesek.kerdes AS kerdes,
                    feltoltesek.fajl AS kep,
                    valaszszoveg,
                    vizsgak_valaszlehetosegek.id AS valaszid,
                    vizsgak_valaszlehetosegek.helyes AS helyes,
                    letrehozo,
                    letrehozasideje,
                    modosito,
                    modositasideje,
                    letrehoz.nev AS letrehozonev,
                    modosit.nev AS modositonev
            FROM vizsgak_kerdesek
                INNER JOIN vizsgak_valaszlehetosegek ON vizsgak_kerdesek.id = vizsgak_valaszlehetosegek.kerdes
                INNER JOIN felhasznalok letrehoz ON vizsgak_kerdesek.letrehozo = letrehoz.id
                LEFT JOIN felhasznalok modosit ON vizsgak_kerdesek.modosito = modosit.id
                LEFT JOIN feltoltesek ON vizsgak_kerdesek.kep = feltoltesek.id
            WHERE vizsgak_kerdesek.id = ? $jogszukit;", $szerkparams);

        if($kerdesadat->sorokszama == 0)
        {
            echo "<h2>Nincs jogosultságod a kérdés szerkesztésére!</h2>";
            $irhat = false;
        }
        else
        {
            $kerdes = $kerdesadat->Fetch();
            foreach($kerdesadat as $valaszlehetoseg)
            {
                $temp = array(
                    'valaszid' => $valaszlehetoseg['valaszid'],
                    'valaszszoveg' => $valaszlehetoseg['valaszszoveg'],
                    'helyes' => $valaszlehetoseg['helyes']
                );
                $valaszlehetosegek[] = $temp;
            }
            
            $kerdesszoveg = $kerdes['kerdes'];
            $kep = $RootPath . "/uploads/" . $kerdes['kep'];
            $oldalcim = "Kérdés: " . $id;
            $button = "Szerkesztés";
            
            ?><div style='padding-top: 0px'><small>Létrehozó: <?=$kerdes['letrehozonev']?> (<?=$kerdes['letrehozasideje']?>)</small><br></div>
            <div style='padding-top: 0px'><small>Utoljára módosította: <?=$kerdes['modositonev']?> (<?=$kerdes['modositasideje']?>)</small><br></div><?php
        }
    }

    if($irhat)
    {
        include('././templates/edit.tpl.php');
    }
}
?>
