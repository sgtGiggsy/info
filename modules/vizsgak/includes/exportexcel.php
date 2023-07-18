<?php
include_once('./classes/xlsxwriter.class.php');

$exporthoz = array(array('Sorszám', 'Vizsgázó', 'Felhasználónév', 'Megválaszolt kérdések', 'Helyes válaszok', 'Helyes százalék', 'Kitöltés ideje', 'Részleg'));

foreach($kitoltesek as $x) 
{
    $export = true;
    if($x['helyes'] == 0)
    {
        $szazalek = 0;
    }
    else
    {
        $szazalek = round($x['helyes']/$x['ossz']*100, 2);
    }
    foreach($exporthoz as $key => $value) // A szétválasztásra az unset miatt van szükség
    {
        if($key > 0) // A 0. sor a fejléc, így azt kihagyjuk az ellenőrzésből
        {
            if($exporthoz[$key]['felhasznalonev'] == $x['felhasznalonev'])
            {
                // Csak a legjobb eredményt exportáljuk, így ha a felhasználó korábbi eredménye jobb
                // akkor az újabbat nem adjuk hozzá a tömbhöz. Ha rosszabb, akkor az újat hozzáadjuk
                // és a már meglévőt eltávolítjuk
                if($exporthoz[$key]['szazalek'] > $szazalek)
                {
                    $export = false;
                }
                else
                {
                    unset($exporthoz[$key]);
                }
                break;
            }
        }
    }
    if($export)
    {
        $exporthoz[] = array(
                'sorszam' => $x['sorszam'],
                'nev' => $x['nev'],
                'felhasznalonev' => $x['felhasznalonev'],
                'ossz' => $x['ossz'],
                'helyes' => $x['helyes'],
                'szazalek' => $szazalek,
                'kitoltesideje' => $x['kitoltesideje'],
                'osztaly' => $x['osztaly']
            );
    }
}

$file = "./createdfiles/" . $vizsgaadatok['url'] . "-vizsgalista.xlsx";
$writer = new XLSXWriter();
$writer->writeSheet($exporthoz);
$writer->writeToFile($file);

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/xlsx');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    readfile($file);
    exit;
}