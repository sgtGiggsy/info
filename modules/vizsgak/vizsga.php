<?php

$contextmenu = array(
    'ismerteto' => array('gyujtooldal' => 'ismerteto', 'oldal' => 'ismerteto', 'gyujtooldalnev' => 'Ismertető', 'oldalnev' => 'Ismertető'),
    'vizsgazas' => array('gyujtooldal' => 'vizsgazas', 'oldal' => 'vizsgazas', 'gyujtooldalnev' => 'Vizsgázás', 'oldalnev' => 'Vizsgázás'),
    'vizsgalista' => array('gyujtooldal' => 'vizsgalista', 'oldal' => 'vizsgareszletezo', 'gyujtooldalnev' => 'Vizsgalista', 'oldalnev' => 'Vizsgarészletező'),
    'megkezdettvizsgak' => array('gyujtooldal' => 'megkezdettvizsgak', 'oldal' => '', 'gyujtooldalnev' => 'Megkezdett vizsgák', 'oldalnev' => 'Vizsgarészletező'),
    'kerdeslista' => array('gyujtooldal' => 'kerdeslista', 'oldal' => 'kerdesszerkeszt', 'gyujtooldalnev' => 'Kérdések listája', 'oldalnev' => 'Kérdés szerkesztése'),
    'adminlista' => array('gyujtooldal' => 'adminlista', 'oldal' => 'adminszerkeszt', 'gyujtooldalnev' => 'Adminok', 'oldalnev' => 'Admin szerkesztése'),
    'engedelyezettek' => array('gyujtooldal' => 'engedelyezettek', 'oldal' => 'engedelyezettszerkeszt', 'gyujtooldalnev' => 'Engedélyezettek', 'oldalnev' => 'Engedélyezettek szerkesztése'),
    'vizsgabeallitasok' => array('gyujtooldal' => 'vizsgabeallitasok', 'oldal' => 'vizsgabeallitasok', 'gyujtooldalnev' => 'Beállitások', 'oldalnev' => 'Beálltások')
);

if(!isset($_GET['subpage']) && !isset($_GET['id']) && !(isset($_GET['action']) && $_GET['action'] == 'addnew'))
{
    echo "<h2>Nincs kiválasztott vizsga!</h2>";
}
elseif(!isset($_GET['subpage']) && !isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == 'addnew')
{
    include("./modules/vizsgak/includes/vizsgabeallitasok.php");
}
else
{
    if(isset($_GET['subpage']))
    {
        $vizsgaazonosito = $_GET['subpage'];
    }
    else
    {
        $vizsgaazonosito = $_GET['id'];
    }
    $pagename = $vizsgaazonosito;
    $felhasznaloengedelyezett = true;
    $aloldal = false;
    $vizsgaqueryparams = array();

    $kivalasztottvizsga = new MySQLHandler("SELECT vizsgak_vizsgak.id AS id,
            nev,
            url,
            udvozloszoveg,
            vendegudvozlo,
            kerdesszam,
            minimumhelyes,
            vizsgaido,
            ismetelheto,
            maxismetles,
            leiras,
            lablec,
            eles,
            korlatozott,
            feltoltesek.fajl AS fejleckep
    FROM vizsgak_vizsgak
        LEFT JOIN feltoltesek ON vizsgak_vizsgak.fejleckep = feltoltesek.id
    WHERE url = ?;", $vizsgaazonosito);

    if(!$kivalasztottvizsga->siker || $kivalasztottvizsga->sorokszama == 0)
    {
        echo "<h2>Nem létezik ilyen nevű vizsga!</h2>";
    }
    else
    {
        $vizsgaadatok = $kivalasztottvizsga->Fetch();
        $vizsgaid = $vizsgaadatok['id'];
        $vizsgaeles = $vizsgaadatok['eles'];
        if($vizsgaadatok['fejleckep'])
        {
            $contextheader = $RootPath . "/uploads/" . $vizsgaadatok['fejleckep'];
        }

        if($vizsgaeles)
        {
            $vizsgaelszures = "AND vizsgak_kitoltesek.folyoszam IS NOT NULL";
        }
        else
        {
            $vizsgaelszures = "AND vizsgak_kitoltesek.folyoszam IS NULL";
        }

        if(@$felhasznaloid)
        {
            // Felhasználó jogosultságainak bekérése
            $felhasznaloengedelyezett = true;
            $contextmenujogok = array('admin' => false, 'vizsgazas' => true, 'ismerteto' => true);
            $contextmenujogok['vizsgabeallitasok'] = $contextmenujogok['kerdeslista'] = $contextmenujogok['vizsgalista'] = 
            $contextmenujogok['kerdesszerkeszt'] = $contextmenujogok['megkezdettvizsgak'] = $contextmenujogok['adminlista'] =
            $contextmenujogok['adminkijeloles'] = $contextmenujogok['ujkornyitas'] = $contextmenujogok['admin'] = false;

            if($mindir)
            {
                $contextmenujogok['vizsgabeallitasok'] = $contextmenujogok['kerdeslista'] = $contextmenujogok['vizsgalista'] = 
                $contextmenujogok['kerdesszerkeszt'] = $contextmenujogok['megkezdettvizsgak'] = $contextmenujogok['adminlista'] =
                $contextmenujogok['adminkijeloles'] = $contextmenujogok['ujkornyitas'] = $contextmenujogok['admin'] = $contextmenujogok['vizsgalapok'] = true;
                if($vizsgaadatok['korlatozott'])
                {
                    $contextmenujogok['engedelyezettek'] = true;
                }
            }
            else
            {
                $vizsgaadmin = new MySQLHandler("SELECT * FROM vizsgak_adminok WHERE felhasznalo = ? AND vizsga = ?;", $felhasznaloid, $vizsgaid);

                if($vizsgaadatok['korlatozott'] && $vizsgaadmin->sorokszama == 0)
                {
                    $felhasznaloengedelyezett = new MySQLHandler("SELECT * FROM vizsgak_engedelyezettek WHERE felhasznalo = ? AND vizsga = ?;", $felhasznaloid, $vizsgaid);
                    if($felhasznaloengedelyezett->sorokszama == 0)
                    {
                        $felhasznaloengedelyezett = false;
                    }
                }
                if($vizsgaadmin->siker && $vizsgaadmin->sorokszama > 0)
                {
                    // Ezek az alap jogok, amik minden vizsgaadminnak kiosztásra kerülnek
                    $felhasznaloengedelyezett = $contextmenujogok['vizsgalista'] = $contextmenujogok['megkezdettvizsgak'] =
                    $contextmenujogok['admin'] = $contextmenujogok['vizsgalapok'] = true;

                    $vizsgaadmin = $vizsgaadmin->Fetch();

                    if($vizsgaadmin['beallitasok'])
                    {
                        $contextmenujogok['vizsgabeallitasok'] = true;
                        if($vizsgaadatok['korlatozott'])
                        {
                            $contextmenujogok['engedelyezettek'] = true;
                        }
                    }
                    
                    if($vizsgaadmin['kerdesek'])
                    {
                        $contextmenujogok['kerdeslista'] = $contextmenujogok['kerdesszerkeszt'] = true;
                    }

                    if($vizsgaadmin['adminkijeloles'])
                    {
                        $contextmenujogok['adminlista'] = true;
                        $contextmenujogok['adminkijeloles'] = true;
                    }

                    if($vizsgaadmin['ujkornyitas'])
                    {
                        $contextmenujogok['ujkornyitas'] = true;
                    }
                }
            }
        }

        if(!$vizsgaeles && !@$contextmenujogok['vizsgalista'] || !$felhasznaloengedelyezett)
        {
            echo "<h2>Nincs jogosultságod ennek a vizsgának a megtekintéséhez!</h2>";
        }
        elseif(isset($_GET['param']))
        {
            $aloldal = $_GET['param'];
            $page = @fopen("./modules/vizsgak/includes/$aloldal.php", "r");
            if(!$page)
            {
                http_response_code(404);
                echo "<h2>A keresett oldal nem található!</h2>";
            }
            else
            {
                $korvizsgaszures = "vizsgak_vizsgakorok.vizsga = ? AND ";
                $vizsgaqueryparams[] = $vizsgaadatok['id'];
                if(isset($_GET['vizsgakor']))
                {
                    $korvizsgaszures .= "vizsgak_vizsgakorok.sorszam = ?";
                    $vizsgakorsorszam = $_GET['vizsgakor'];
                    $vizsgaqueryparams[] = $vizsgakorsorszam;
                }
                else
                {
                    $korvizsgaszures .= "vizsgak_vizsgakorok.sorszam = (SELECT MAX(sorszam) FROM vizsgak_vizsgakorok WHERE vizsga = ?)";
                    $vizsgaqueryparams[] = $vizsgaadatok['id'];
                }

                include("./modules/vizsgak/includes/$aloldal.php");
            }
        }
        else
        {
            include("./modules/vizsgak/includes/ismerteto.php");
        }
        ?><div class="vizsgalablec">
            <div><?=$vizsgaadatok['lablec']?></div>
        </div><?php
    }
}