<?php
include("./modules/telefonkonyv/includes/functions.php");
// Contextmenujogok
$contextmenu = array(
    'telefonkonyv' => array('gyujtooldal' => NULL, 'oldal' => 'telefonkonyv', 'gyujtooldalnev' => 'Telefonkönyv', 'oldalnev' => 'Telefonszám módosítása'),
    'valtozasok' => array('gyujtooldal' => 'valtozasok', 'oldal' => 'valtozas', 'gyujtooldalnev' => 'Változások', 'oldalnev' => 'Változtatás'),
    'szerkesztok' => array('gyujtooldal' => 'szerkesztok', 'oldal' => 'szerkeszto', 'gyujtooldalnev' => 'Szerkesztők', 'oldalnev' => 'Szerkesztő'),
    'alegysegek' => array('gyujtooldal' => 'alegysegek', 'oldal' => 'alegyseg', 'gyujtooldalnev' => 'Alegységek', 'oldalnev' => 'Alegység')
);

$contextmenujogok = array('telefonkonyv' => false, 'valtozasok' => false, 'szerkesztok' => false, 'alegysegek' => false);
$globaltelefonkonyvadmin = $szamlalo = $csaksajat = false;
$searchparams = $csoportjogok = array();
if(@$felhasznaloid)
{
    // Felhasználó jogosultságainak bekérése
    if($mindir)
    {
        $contextmenujogok = array('telefonkonyv' => true, 'valtozasok' => true, 'szerkesztok' => true, 'alegysegek' => true);
        $globaltelefonkonyvadmin = true;
    }
    else
    {
        $telefonkonyvadmin = new MySQLHandler("SELECT csoport, felhasznalo, id FROM telefonkonyvadminok WHERE felhasznalo = ? ORDER BY felhasznalo ASC, csoport ASC;", $felhasznaloid);

        if($telefonkonyvadmin->sorokszama > 0)
        {
            $contextmenujogok['valtozasok'] = true;
            $contextmenujogok['telefonkonyv'] = true;
            $adminjog = $telefonkonyvadmin->Fetch();
            if($adminjog['csoport'] == 1)
            {
                $contextmenujogok['szerkesztok'] = true;
                $contextmenujogok['alegysegek'] = true;
                $globaltelefonkonyvadmin = true;
            }
            else
            {
                $csoportjogok = $telefonkonyvadmin->AsArray(null, true);
                $jogcount = $telefonkonyvadmin->sorokszama;
            }
        }
    }
}

if(isset($_GET['id']) || isset($_GET['subpage']))
{
    $id = null;
    if(isset($_GET['subpage']))
    {
        $aloldal = $_GET['subpage'];
        if(!isset($_GET['param']))
            $id = $_GET['subpage'];
        else
            $id = $_GET['param'];
    }
    else
        $aloldal = $_GET['id'];

    $page = @fopen("./modules/telefonkonyv/includes/$aloldal.php", "r");

    if(!$page)
    {
        http_response_code(404);
        echo "<h2>A keresett oldal nem található!</h2>";
    }
    else
    {
        include("./modules/telefonkonyv/includes/$aloldal.php");
    }
}
else
{
    $csoportfilter = "alegysegfilter";
    $javascriptfiles[] = "includes/js/csoportFilter.js";

    if(!isset($_GET['nevszerinti']))
    {
        $nevszerint = false;
        $orderby = "telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok.sorrend";
        $orderbymod = "telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok_mod.sorrend, telefonkonyvfelhasznalok.nev DESC";
    }
    else
    {
        $nevszerint = true;
        $orderby = $orderbymod = "telefonkonyvfelhasznalok.nev";
    }

    $sajatcsoportmodmutat = ")";
    if(!$globaltelefonkonyvadmin && $contextmenujogok['valtozasok'])
    {
        $sajatcsoportmodmutat = "OR (telefonkonyvvaltozasok.allapot = 1 AND telefonkonyvcsoportok.id IN (";
        
        for($i = 0; $i < $jogcount; $i++)
        {
            $sajatcsoportmodmutat .= "?";
            if($i < $jogcount - 1)
                $sajatcsoportmodmutat .= ", ";
        }
        
        $sajatcsoportmodmutat .= "))";
    }

    $where = "WHERE telefonkonyvbeosztasok.allapot > 1";
    $where2 = "WHERE (telefonkonyvvaltozasok.allapot > 1 AND telefonkonyvvaltozasok.allapot < 4 $sajatcsoportmodmutat";
    if(isset($_GET['kereses']))
    {
        $keres = '%' . $_GET['kereses'] . '%';
        $szurt = " AND telefonkonyvfelhasznalok.nev LIKE ? OR telefonkonyvcsoportok.nev LIKE ? OR belsoszam LIKE ? OR belsoszam2 LIKE ? OR mobil LIKE ?";
        $searchparams = array($keres, $keres, $keres, $keres, $keres);
        $where .= $szurt;
        $where2 .= $szurt;
    }
    if(isset($_GET['csaksajat']))
    {
        $csaksajat = true;
    }
    if($nevszerint)
    {
        $where .= " AND telefonkonyvfelhasznalok.nev IS NOT NULL";
        $where2 .= " AND telefonkonyvfelhasznalok.nev IS NOT NULL";;
    }

    $alegysegek = new MySQLHandler("SELECT * FROM telefonkonyvcsoportok WHERE id > 1;");
    $alegysegek = $alegysegek->Result();

    $oldmethod = microtime(true);

    $telefonkonyvorig = new MySQLHandler("SELECT NULL AS modid,
            telefonkonyvbeosztasok.id AS telszamid,
            telefonkonyvcsoportok.nev AS csoport,
            telefonkonyvbeosztasok.nev AS beosztas,
            telefonkonyvbeosztasok.allapot AS allapot,
            nevelotagok.nev AS elotag,
            telefonkonyvfelhasznalok.nev AS nev,
            titulusok.nev AS titulus,
            rendfokozatok.nev AS rendfokozat,
            belsoszam,
            belsoszam2,
            kozcelu,
            fax,
            kozcelufax,
            mobil,
            felhasznalok.felhasznalonev AS felhasznalo,
            telefonkonyvbeosztasok.megjegyzes AS megjegyzes,
            telefonkonyvcsoportok.sorrend AS csoportsorrend,
            telefonkonyvbeosztasok.sorrend AS beosorrend,
            telefonkonyvcsoportok.id AS csopid,
            telefonkonyvbeosztasok.torolve AS torolve
        FROM telefonkonyvbeosztasok
            LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok.felhid = telefonkonyvfelhasznalok.id
            LEFT JOIN nevelotagok ON telefonkonyvfelhasznalok.elotag = nevelotagok.id
            LEFT JOIN titulusok ON telefonkonyvfelhasznalok.titulus = titulusok.id
            LEFT JOIN rendfokozatok ON telefonkonyvfelhasznalok.rendfokozat = rendfokozatok.id
            LEFT JOIN felhasznalok ON telefonkonyvfelhasznalok.felhasznalo = felhasznalok.id
            LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
            LEFT JOIN telefonkonyvvaltozasok ON telefonkonyvbeosztasok.id = telefonkonyvvaltozasok.ujbeoid
        $where
        ORDER BY $orderby;", ...$searchparams);
    $telefonkonyvorig = $telefonkonyvorig->Result();

    $telefonkonyvuj = new MySQLHandler("SELECT telefonkonyvvaltozasok.id AS modid,
            telefonkonyvvaltozasok.origbeoid AS telszamid,
            telefonkonyvcsoportok.nev AS csoport,
            telefonkonyvbeosztasok_mod.nev AS beosztas,
            telefonkonyvbeosztasok_mod.allapot AS allapot,
            nevelotagok.nev AS elotag,
            telefonkonyvfelhasznalok.nev AS nev,
            titulusok.nev AS titulus,
            rendfokozatok.nev AS rendfokozat,
            belsoszam,
            belsoszam2,
            kozcelu,
            fax,
            kozcelufax,
            mobil,
            felhasznalok.felhasznalonev AS felhasznalo,
            telefonkonyvbeosztasok_mod.megjegyzes AS megjegyzes,
            telefonkonyvcsoportok.sorrend AS csoportsorrend,
            telefonkonyvbeosztasok_mod.sorrend AS beosorrend,
            telefonkonyvcsoportok.id AS csopid,
            telefonkonyvbeosztasok_mod.torolve AS torolve
        FROM telefonkonyvbeosztasok_mod
            LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok_mod.felhid = telefonkonyvfelhasznalok.id
            LEFT JOIN nevelotagok ON telefonkonyvfelhasznalok.elotag = nevelotagok.id
            LEFT JOIN titulusok ON telefonkonyvfelhasznalok.titulus = titulusok.id
            LEFT JOIN rendfokozatok ON telefonkonyvfelhasznalok.rendfokozat = rendfokozatok.id
            LEFT JOIN felhasznalok ON telefonkonyvfelhasznalok.felhasznalo = felhasznalok.id
            LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok_mod.csoport = telefonkonyvcsoportok.id
            LEFT JOIN telefonkonyvvaltozasok ON telefonkonyvbeosztasok_mod.id = telefonkonyvvaltozasok.ujbeoid
        $where2
        ORDER BY $orderbymod;", ...$csoportjogok, ...$searchparams);
    $telefonkonyvuj = $telefonkonyvuj->Result();

    $telefonkonyv = array();

    // Először összevetjük a már meglévő, mentett telefonkönyv bejegyzéseket a beküldött, elfogadott, de nem véglegesített módosításokkal
    $uj = false;
    foreach($telefonkonyvorig as $fixbejegyzes)
    {
        foreach($telefonkonyvuj as $ujbejegyzes)
        {
            $uj = false;
            if($fixbejegyzes['telszamid'] == $ujbejegyzes['telszamid'])
            {
                $uj = true;
                //echo "ujtrue";
                // Ha a módosítás a beosztás törlése, nem adjuk hozzá a megjelenő telefonkönyvhöz
                if($ujbejegyzes['torolve'] != 1)
                {
                    $ujbejegyzes['uj'] = true;
                    $telefonkonyv[] = $ujbejegyzes;
                }
                break;
            }
        }
        // Ha nem volt a meglévő beosztással egyező módosítás, a régi bejegyzés megjelenítése
        if(!$uj && $fixbejegyzes['torolve'] != 1)
        {
            $fixbejegyzes['uj'] = false;
            $telefonkonyv[] = $fixbejegyzes;
        }
    }

    foreach($telefonkonyvuj as $ujbejegyzes)
    {
        if(!$ujbejegyzes['telszamid'])
        {
            $ujbejegyzes['uj'] = true;
            $telefonkonyv[] = $ujbejegyzes;
        }
    }

    if(!$nevszerint)
    {
        $csoportsorrend  = array_column($telefonkonyv, 'csoportsorrend');
        $beosorrend = array_column($telefonkonyv, 'beosorrend');
        array_multisort($csoportsorrend, SORT_ASC, $beosorrend, SORT_ASC, $telefonkonyv);

        $oszlopok = array(
            array('nev' => '', 'tipus' => 's', 'adatmezo' => 'csoport'),
            array('nev' => 'Beosztás', 'tipus' => 's', 'adatmezo' => 'beosztas'),
            array('nev' => 'Előtag', 'tipus' => 's', 'adatmezo' => 'elotag'),
            array('nev' => 'Név', 'tipus' => 's', 'adatmezo' => 'nev'),
            array('nev' => 'Titulus', 'tipus' => 's', 'adatmezo' => 'titulus'),
            array('nev' => 'Rendfokozat', 'tipus' => 's', 'adatmezo' => 'rendfokozat'),
            array('nev' => 'Belső szám', 'tipus' => 's', 'adatmezo' => 'belsoszam'),
            array('nev' => 'Közcélú', 'tipus' => 's', 'adatmezo' => 'kozcelu'),
            array('nev' => 'Fax', 'tipus' => 's', 'adatmezo' => 'fax'),
            array('nev' => 'Közcélú fax', 'tipus' => 's', 'adatmezo' => 'kozcelufax'),
            array('nev' => 'Szolgálati mobil', 'tipus' => 's', 'adatmezo' => 'mobil'),
            array('nev' => 'Megjegyzés', 'tipus' => 's', 'adatmezo' => 'megjegyzes')
        );
    }
    else
    {
        $oszlopok = array(
            array('nev' => 'Előtag', 'tipus' => 's', 'adatmezo' => 'elotag'),
            array('nev' => 'Név', 'tipus' => 's', 'adatmezo' => 'nev'),
            array('nev' => 'Titulus', 'tipus' => 's', 'adatmezo' => 'titulus'),
            array('nev' => 'Rendfokozat', 'tipus' => 's', 'adatmezo' => 'rendfokozat'),
            array('nev' => 'Beosztás', 'tipus' => 's', 'adatmezo' => 'beosztas'),
            array('nev' => 'Belső szám', 'tipus' => 's', 'adatmezo' => 'belsoszam'),
            array('nev' => 'Közcélú', 'tipus' => 's', 'adatmezo' => 'kozcelu'),
            array('nev' => 'Szolgálati mobil', 'tipus' => 's', 'adatmezo' => 'mobil'),
            array('nev' => 'Alegység', 'tipus' => 's', 'adatmezo' => 'alegyseg')
        );
    }

    if(isset($_GET['action']) && $_GET['action'] == "exportexcel")
    {
        include("./modules/telefonkonyv/includes/telefonkonyvexport.php");
    }
    else
    {
        include("./modules/telefonkonyv/includes/telefonkonyv.php");
    }
}