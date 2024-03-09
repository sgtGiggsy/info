<?php

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
    $topmenuszoveges = true;
    $aloldal = false;

    $kivalasztottvizsga = mySQLConnect("SELECT vizsgak_vizsgak.id AS id,
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
            feltoltesek.fajl AS fejleckep
    FROM vizsgak_vizsgak
        LEFT JOIN feltoltesek ON vizsgak_vizsgak.fejleckep = feltoltesek.id
    WHERE url = '$vizsgaazonosito';");

    if(!$kivalasztottvizsga || mysqli_num_rows($kivalasztottvizsga) == 0)
    {
        echo "<h2>Nem létezik ilyen nevű vizsga!</h2>";
    }
    else
    {
        $vizsgaadatok = mysqli_fetch_assoc($kivalasztottvizsga);
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
            $contextmenujogok = array('admin' => false, 'vizsgazas' => true, 'ismerteto' => true);
            $contextmenujogok['vizsgabeallitasok'] = $contextmenujogok['kerdeslista'] = $contextmenujogok['vizsgalista'] = 
            $contextmenujogok['kerdesszerkeszt'] = $contextmenujogok['megkezdettvizsgak'] = $contextmenujogok['adminlista'] =
            $contextmenujogok['adminkijeloles'] = $contextmenujogok['ujkornyitas'] = $contextmenujogok['admin'] = false;

            if($mindir)
            {
                $contextmenujogok['vizsgabeallitasok'] = $contextmenujogok['kerdeslista'] = $contextmenujogok['vizsgalista'] = 
                $contextmenujogok['kerdesszerkeszt'] = $contextmenujogok['megkezdettvizsgak'] = $contextmenujogok['adminlista'] =
                $contextmenujogok['adminkijeloles'] = $contextmenujogok['ujkornyitas'] = $contextmenujogok['admin'] = $contextmenujogok['vizsgalapok'] = true;
            }
            else
            {
                $vizsgaadmin = mySQLConnect("SELECT * FROM vizsgak_adminok WHERE felhasznalo = $felhasznaloid AND vizsga = $vizsgaid;");
                if(mysqli_num_rows($vizsgaadmin) > 0)
                {
                    // Ezek az alap jogok, amik minden vizsgaadminnak kiosztásra kerülnek
                    $contextmenujogok['vizsgalista'] = $contextmenujogok['megkezdettvizsgak'] = $contextmenujogok['admin'] = $contextmenujogok['vizsgalapok'] = true;

                    $vizsgaadmin = mysqli_fetch_assoc($vizsgaadmin);

                    if($vizsgaadmin['beallitasok'])
                    {
                        $contextmenujogok['vizsgabeallitasok'] = true;
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

        if(!$vizsgaeles && !@$contextmenujogok['vizsgalista'])
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
                $korvizsgaszures = "vizsgak_vizsgakorok.vizsga = '" . $vizsgaadatok['id'] . "' AND ";
                if(isset($_GET['vizsgakor']))
                {
                    $korvizsgaszures .= "vizsgak_vizsgakorok.sorszam = '" . $_GET['vizsgakor'] . "'";
                    $vizsgakorsorszam = $_GET['vizsgakor'];
                }
                else
                {
                    $korvizsgaszures .= "vizsgak_vizsgakorok.sorszam = (SELECT MAX(sorszam) FROM vizsgak_vizsgakorok WHERE vizsga = '" . $vizsgaadatok['id'] . "')";
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