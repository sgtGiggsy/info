<?php
if($mindolvas)
{}
elseif($csoportolvas)
{
    $where .= " AND (felvivo.szervezet = ?)";
    $paramarr[] = $szervezet;
}
$feladatterv = $untildeadline = $leiras = $rovid = $currpri = $currbuild = $ido_tervezett = $ido_hatarido = $ido_tenyleges = $szakid = null;
$csoportwhere = null;
$newelemid = 0;
$selectedfelelosok = array();

if(!$mindolvas)
{
    // A CsoportWhere űrlapja
    $csopwhereset = array(
        'tipus' => "szervezet",                 // A szűrés típusa, null = mindkettő, szervezet = szervezet, telephely = telephely
        'and' => false,                          // Kerüljön-e AND a parancs elejére
        'szervezetelo' => "felvivo",                  // A tábla neve, ahonnan az szervezet neve jön
        'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
        'szervezetnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az szervezetszűréshez
        'telephelynull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
        'szervezetmegnevezes' => "szervezet"    // Az szervezetot tartalmazó mező neve a felhasznált táblában
    );

    $csopwhere = csoportWhere_new($csoporttagsagok, $csopwhereset);
    $csoportwhere = "OR " . $csopwhere[0] . ")";
    $paramarr = array_merge($paramarr, $csopwhere[1]);
}

if(!$csoportir)
{
    $csoportwhere = ")";
}

$szakok = new MySQLHandler("SELECT id, nev FROM szakok ORDER BY nev ASC;");
$szakok = $szakok->Result();
$felhasznalok = new MySQLHandler("SELECT id, nev FROM felhasznalok WHERE szervezet = ? AND aktiv = 1 ORDER BY nev ASC;", $szervezet);
$felhasznalok = $felhasznalok->Result();

$feladatterv  = new MySQLHandler("SELECT rovid, leiras, prioritas, allapot, szulo, szakid, epulet, felvitte, modositotta,
            ido_letrehoz, ido_tervezett, ido_tenyleges, ido_hatarido,
            szakok.nev AS szaknev,
            feladatterv_feladatok.feladat_id AS feladat_id,
            felvivo.nev AS felvivo_nev,
            felvivo.szervezet AS szervezet,
            modosito.nev AS modosito_nev,
            epuletek.nev AS epulet_nev,
            epuletek.szam AS epulet_szam,
            telephelyek.telephely AS telephely,
            prioritasok.nev AS prioritasnev,
            null AS eszkoz,
            GROUP_CONCAT(DISTINCT felelos.id SEPARATOR ',;,') AS felelosids,
            GROUP_CONCAT(DISTINCT felelos.nev SEPARATOR ',;,') AS felelosnevek,
            GROUP_CONCAT(fajl SEPARATOR ',;,') AS fajlok,
            GROUP_CONCAT(feladatterv_fajlok.felhasznalo_id SEPARATOR ',;,') AS fajlfeltoltoids,
            GROUP_CONCAT(feltoltesek.timestamp SEPARATOR ',;,') AS feltoltesidok,
            GROUP_CONCAT(fajlfeltolto.nev SEPARATOR ',;,') AS fajlfeltoltonevek,
            GROUP_CONCAT(feladatterv_kommentek.szoveg SEPARATOR ',;,') AS kommentek,
            GROUP_CONCAT(feladatterv_kommentek.timestamp SEPARATOR ',;,') AS kommentidok,
            GROUP_CONCAT(kommenter.nev SEPARATOR ',;,') AS kommenterek,
            COUNT(DISTINCT feladatterv_felelosok.felelos_id) AS felelosszam,
            COUNT(DISTINCT feladatterv_fajlok.feladatfajl_id) AS fajlszam,
            COUNT(DISTINCT feladatterv_kommentek.komment_id) AS kommentszam
    FROM feladatterv_feladatok
        LEFT JOIN feladatterv_fajlok ON feladatterv_feladatok.feladat_id = feladatterv_fajlok.feladat_id
        LEFT JOIN feladatterv_felelosok ON feladatterv_feladatok.feladat_id = feladatterv_felelosok.feladat_id
        LEFT JOIN feladatterv_kommentek ON feladatterv_feladatok.feladat_id = feladatterv_kommentek.feladat_id
        LEFT JOIN felhasznalok felvivo ON feladatterv_feladatok.felvitte = felvivo.id
        LEFT JOIN felhasznalok modosito ON feladatterv_feladatok.modositotta = modosito.id
        LEFT JOIN felhasznalok felelos ON feladatterv_felelosok.felhasznalo_id = felelos.id
        LEFT JOIN felhasznalok fajlfeltolto ON feladatterv_fajlok.felhasznalo_id = fajlfeltolto.id
        LEFT JOIN felhasznalok kommenter ON feladatterv_kommentek.felhasznalo_id = kommenter.id
        LEFT JOIN epuletek ON feladatterv_feladatok.epulet = epuletek.id
        LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
        LEFT JOIN feltoltesek ON feladatterv_fajlok.feltoltes_id = feltoltesek.id
        LEFT JOIN prioritasok ON feladatterv_feladatok.prioritas = prioritasok.id
        LEFT JOIN szakok ON feladatterv_feladatok.szakid = szakok.id
    $where $csoportwhere
    GROUP BY feladatterv_feladatok.feladat_id
    ORDER BY feladatterv_feladatok.feladat_id ASC, ido_tervezett ASC;", ...$paramarr);

// Ha nincs feladatterv, akkor letiltjuk a hozzáférést
if($feladatterv->sorokszama == 0)
{
    $feladatterv = false;
}
else
{
    $feladattervszuloszerv = $feladatterv->Fetch()['szervezet'];
}