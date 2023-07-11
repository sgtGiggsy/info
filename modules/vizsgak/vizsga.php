<?php

if(!$felhasznaloid)
{
    echo "<h2>Az oldal kizárólag bejelentkezett felhasználók számára érhető el!</h2>";
}
elseif(!isset($_GET['subpage']) && !isset($_GET['id']))
{
    echo "<h2>Nincs kiválasztott vizsga!</h2>";
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

    $kivalasztottvizsga = mySQLConnect("SELECT * FROM vizsgak_vizsgak WHERE url = '$vizsgaazonosito';");

    if(!$kivalasztottvizsga || mysqli_num_rows($kivalasztottvizsga) == 0)
    {
        echo "<h2>Nem létezik ilyen nevű vizsga!</h2>";
    }
    else
    {
        $vizsgaadatok = mysqli_fetch_assoc($kivalasztottvizsga);
        $vizsgaid = $vizsgaadatok['id'] ;
        
        // Felhasználó jogosultságainak bekérése
        $contextmenujogok = array('admin' => false, 'vizsgazas' => true, 'ismerteto' => true);
        if($mindir)
        {
            $contextmenujogok['vizsgabeallitasok'] = $contextmenujogok['kerdeslista'] = $contextmenujogok['vizsgalista'] = 
            $contextmenujogok['kerdesszerkeszt'] = $contextmenujogok['megkezdettvizsgak'] = $contextmenujogok['adminlista'] =
            $contextmenujogok['ujkornyitas'] = $contextmenujogok['admin'] = true;
        }
        else
        {
            $vizsgaadmin = mySQLConnect("SELECT * FROM vizsgak_adminok WHERE felhasznalo = $felhasznaloid AND vizsga = $vizsgaid;");
            if(mysqli_num_rows($vizsgaadmin) > 0)
            {
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
                }

                if($vizsgaadmin['ujkornyitas'])
                {
                    $contextmenujogok['ujkornyitas'] = true;
                }
                
                $contextmenujogok['vizsgalista'] = $contextmenujogok['megkezdettvizsgak'] = $contextmenujogok['admin'] = true;
            }
        }
   
        if(isset($_GET['param']))
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
                
                $vizsgasession = false;
                if(isset($_SESSION[getenv('SESSION_NAME').$vizsgaazonosito.'vizsga']))
                {
                    $vizsgasession = $_SESSION[getenv('SESSION_NAME').$vizsgaazonosito.'vizsga'];
                }

                $korvizsgaszures = "vizsgak_vizsgakorok.vizsga = '" . $vizsgaadatok['id'] . "' AND ";
                if(isset($_GET['vizsgakor']))
                {
                    $korvizsgaszures .= "vizsgak_vizsgakorok.sorszam = '" . $_GET['vizsgakor'] . "'";
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























/*        switch($_GET['param'])
        {
            case 'ismerteto' : include("./modules/vizsgak/includes/ismerteto.php"); break;
            case 'vizsgazas' : include("./modules/vizsgak/includes/vizsgazas.php"); break;
            case 'megkezdettvizsgak' : include("./modules/vizsgak/includes/ismerteto.php"); break;
            case 'vizsgalista' : include("./modules/vizsgak/includes/ismerteto.php"); break;
            case 'kerdeslista' : include("./modules/vizsgak/includes/ismerteto.php"); break;
            case 'vizsgareszletezo' : include("./modules/vizsgak/includes/ismerteto.php"); break;
            case 'vizsgaszerkesztese' : include("./modules/vizsgak/includes/ismerteto.php"); break;
            case 'kerdesszerkesztese' : include("./modules/vizsgak/includes/ismerteto.php"); break;
        }
*/
    }
}