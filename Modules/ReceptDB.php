<?php

namespace Infoinventory\Modules;

use Infoinventory\Classes\Helpers;
use Infoinventory\Classes\MySQLHandler;
use Infoinventory\Classes\Settings;
use Infoinventory\Classes\Personalvars;

class ReceptDB
{
    private static string $alap_lista_query = "SELECT recept_nev, lathatosag, receptek.recept_id AS recept_id,
                    slug, cukor, gluten, laktoz, fajl AS kepurl,
                    AVG(recept_ertekelesek.ertekeles) AS ertekeles,
                    COUNT(recept_ertekelesek.ertekeles) AS ertekelesek_szama,
                    SUM(IF(szakacskonyvek.felhasznalo_id = ?, 1, 0)) AS mentve
                FROM receptek
                    LEFT JOIN recept_ertekelesek ON recept_ertekelesek.recept_id = receptek.recept_id
                    LEFT JOIN recept_kepek ON recept_kepek.recept_id = receptek.recept_id
                    LEFT JOIN feltoltesek ON feltoltesek.feltoltes_id = recept_kepek.feltoltes_id
                    LEFT JOIN szakacskonyv_receptek ON szakacskonyv_receptek.recept_id = receptek.recept_id
                    LEFT JOIN szakacskonyvek ON szakacskonyv_receptek.szakacskonyv_id = szakacskonyvek.szakacskonyv_id";
    private static string $alap_lista_query_where = " WHERE (lathatosag = 1 OR (lathatosag = 0 AND receptek.felhasznalo_id = ?))
                    AND (recept_kepek.elsodleges IS NULL OR recept_kepek.elsodleges = 1)
                    AND receptek.letezik = 1";

    private static string $szakacskonyv_lista_query_where = " WHERE (lathatosag = 1 OR lathatosag = 0 AND receptek.felhasznalo_id = ?)
                    AND (recept_kepek.elsodleges IS NULL OR recept_kepek.elsodleges = 1)
                    AND (szakacskonyvek.felhasznalo_id = ? OR receptek.felhasznalo_id = ?)
                    AND receptek.letezik = 1";

    private static string $alap_lista_query_order = " ORDER BY ertekeles DESC";
    public static function UjRecept(string $recept_nev, string $recept_szoveg, int $lathatosag, $slug, ?string $adagmeret, array $alapanyagok, ?int $elokeszuletek, ?int $sutesido, string $tapanyagtabla, int $uid) : int|bool {
        $recept_id = null;
        $sikeresdb = true;
        $allergenek = $alapanyagok['allergenek'];
        $receptgyarto = new MySQLHandler();
        $receptgyarto->StartTransaction();
        $receptgyarto->Prepare('INSERT INTO receptek (recept_nev, recept_szoveg, slug, felhasznalo_id, lathatosag, adagmeret, cukor, laktoz, gluten, elokeszuletek, sutesido, tapanyagtablazat, modosito_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
        $receptgyarto->Run($recept_nev, $recept_szoveg, $slug, $uid, $lathatosag, $adagmeret, $allergenek['cukor'], $allergenek['laktoz'], $allergenek['gluten'], $elokeszuletek, $sutesido, $tapanyagtabla, $uid);
        if(!$receptgyarto->siker) {
            $sikeresdb = false;
        }
        else {
            $recept_id = $receptgyarto->last_insert_id;
            $alapanyagok = $alapanyagok['alapanyagok'];
            foreach ($alapanyagok as $alapanyag) {
                if(!$alapanyag['alapanyag_id'])
                    break;

                $receptgyarto->Prepare('INSERT INTO recept_alapanyagok (recept_id, alapanyag_id, mertekegyseg, mennyiseg) VALUES (?, ?, ?, ?);');
                $receptgyarto->Run($recept_id, $alapanyag['alapanyag_id'], $alapanyag['mertekegyseg'], $alapanyag['mennyiseg']);
                if(!$receptgyarto->siker) {
                    $sikeresdb = false;
                    break;
                }
            }
        }
        if(!$sikeresdb) {
            $receptgyarto->Rollback();
            return false;
        }
        else {
            $receptgyarto->Commit();
            return $recept_id;
        }
    }

    public static function ReceptSzerkeszt(string $recept_nev, string $recept_szoveg, int $lathatosag, ?string $adagmeret, array $alapanyagok, ?int $elokeszuletek, ?int $sutesido, string $tapanyagtabla, int $uid, int $recept_id) : int|bool {
        $sikeresdb = true;
        $modositasideje = Helpers::TimeStampForSQL();
        $allergenek = $alapanyagok['allergenek'];
        $receptgyarto = new MySQLHandler();
        $receptgyarto->StartTransaction();
        $receptgyarto->Prepare('UPDATE receptek SET recept_nev = ?, recept_szoveg = ?, modosito_id = ?, modositas_ideje = ?, lathatosag = ?, adagmeret = ?, cukor = ?, laktoz = ?, gluten = ?, elokeszuletek = ?, sutesido = ?, tapanyagtablazat = ? WHERE recept_id = ?;');
        $receptgyarto->Run($recept_nev, $recept_szoveg, $uid, $modositasideje, $lathatosag, $adagmeret, $allergenek['cukor'], $allergenek['laktoz'], $allergenek['gluten'], $elokeszuletek, $sutesido, $tapanyagtabla, $recept_id);
        if(!$receptgyarto->siker) {
            $sikeresdb = false;
        }
        else {
            $receptgyarto->Query("DELETE FROM recept_alapanyagok WHERE recept_id = ?;", $recept_id);
            if(!$receptgyarto->siker) {
                $sikeresdb = false;
            }
            else {
                foreach ($alapanyagok['alapanyagok'] as $alapanyag) {
                    if(!$alapanyag['alapanyag_id'])
                        break;

                    $receptgyarto->Prepare('INSERT INTO recept_alapanyagok (recept_id, alapanyag_id, mertekegyseg, mennyiseg) VALUES (?, ?, ?, ?);');
                    $receptgyarto->Run($recept_id, $alapanyag['alapanyag_id'], $alapanyag['mertekegyseg'], $alapanyag['mennyiseg']);
                    if(!$receptgyarto->siker) {
                        $sikeresdb = false;
                        break;
                    }
                }
            }
        }
        if(!$sikeresdb) {
            $receptgyarto->Rollback();
            return false;
        }
        else {
            $receptgyarto->Commit();
            return $recept_id;
        }
    }

    public static function ReceptTorol(int $recept_id) : bool {
        $toroldb = new MySQLHandler("UPDATE receptek SET letezik = 0 WHERE recept_id = ?;", $recept_id);
        return $toroldb->siker;
    }

    public static function ReceptKepek(int $recept_id, array $kepidk) : bool {
        $elsodleges = 1;
        $kepek = new MySQLHandler();
        $kepek->StartTransaction();
        $kepek->Prepare("INSERT INTO recept_kepek (recept_id, feltoltes_id, elsodleges) VALUES (?, ?, ?);");

        foreach($kepidk as $kepid) {
            $kepek->Run($recept_id, $kepid, $elsodleges);
            if($elsodleges == 1)
                $elsodleges = 0;
        }

        if(!$kepek->siker) {
            $kepek->Rollback();
            return false;
        }
        else {
            $kepek->Commit();
            return true;
        }
    }

    public static function ElsodlegesKep(int $recept_id, int $kep_id) : bool {
        $db = new MySQLHandler();
        $db->StartTransaction();
        $db->Query("UPDATE recept_kepek SET elsodleges = 0 WHERE recept_id = ?;", $recept_id);
        if(!$db->siker)
            $db->Rollback();
        else {
            $db->Query("UPDATE recept_kepek SET elsodleges = 1 WHERE receptkep_id = ?;", $kep_id);
            if(!$db->siker)
                $db->Rollback();
            else
                $db->Commit();
        }
        return $db->siker;
    }

    public static function KepAllapot(int $recept_id, ?array $kepidk = null) : bool {
        $megtart = "";
        if(!$kepidk || count($kepidk) == 0) {
            $kepallapot = new MySQLHandler("UPDATE recept_kepek SET latszik = 0 WHERE recept_id = ?;", $recept_id);
            return $kepallapot->siker;
        }

        foreach ($kepidk as $kepid) {
            $megtart .= "?, ";
        }
        $megtart = rtrim($megtart, ", ");
        $kepallapot = new MySQLHandler("UPDATE recept_kepek SET latszik = 0 WHERE recept_id = ? AND receptkep_id NOT IN ($megtart);", $recept_id, ...$kepidk);
        return $kepallapot->siker;
    }

    public static function ReceptCimkek(int $recept_id, array $cimkek) : bool {
        $cimke = new MySQLHandler();
        $cimke->StartTransaction();
        $cimke->Prepare("DELETE FROM cimke_recept WHERE recept_id = ?;");
        $cimke->Run($recept_id);
        if(!$cimke->siker) {
            $cimke->Rollback();
            return false;
        }
        $cimke->Prepare("INSERT INTO cimke_recept (recept_id, receptcimke_id) VALUES (?, ?);");
        foreach($cimkek as $cimke_id) {
            $cimke->Run($recept_id, $cimke_id);
            if(!$cimke->siker) {
                break;
            }
        }
        if(!$cimke->siker) {
            $cimke->Rollback();
            return false;
        }
        else {
            $cimke->Commit();
            return true;
        }
    }

    public static function BookmarkRecept(int $recept_id, int $felhasznalo_id) : ?bool {
        $szakacskonyv_id = ReceptDB::GetSzakacskonyvId($felhasznalo_id);
        $bookmark = new MySQLHandler();
        $bookmark->Query('DELETE FROM szakacskonyv_receptek WHERE recept_id = ? AND szakacskonyv_id = ?;', $recept_id, $szakacskonyv_id);
        if($bookmark->affectedrows == 0) {
            $bookmark->Prepare("INSERT INTO szakacskonyv_receptek (recept_id, szakacskonyv_id)
                VALUES (?, ?);");
            $bookmark->Run($recept_id, $szakacskonyv_id);
            $ret = $bookmark->siker;
        } else {
            $ret = null;
        }

        return $ret;
    }

    public static function GetSzakacskonyvId(int $felhasznalo_id) : ?int {
        $bookmark = new MySQLHandler("SELECT szakacskonyv_id FROM szakacskonyvek WHERE felhasznalo_id = ?;", $felhasznalo_id);
        if($bookmark->sorokszama == 0)
            return null;
        else
            return $bookmark->Fetch()['szakacskonyv_id'];
    }

    public static function UjSzakacskonyv(int $felhasznalo_id) : bool {
        $szakacskonyv = new MySQLHandler();
        $szakacskonyv->Prepare("INSERT INTO szakacskonyvek (felhasznalo_id) VALUES (?);");
        $szakacskonyv->Run($felhasznalo_id);
        return $szakacskonyv->siker;
    }

    public static function GetRecept(string $slug) : ?array {
        $recept = new MySQLHandler("SELECT recept_nev, recept_szoveg, receptek.letrehozas_ideje, slug, elokeszuletek, sutesido, lathatosag, adagmeret, tapanyagtablazat,
                    receptek.cukor AS cukor, receptek.gluten AS gluten, receptek.laktoz AS laktoz, receptek.recept_id, receptek.felhasznalo_id, receptek.recept_id AS recept_id,
                    AVG(recept_ertekelesek.ertekeles) AS ertekeles,
                    COUNT(recept_ertekelesek.ertekeles) AS ertekelesek_szama,
                    SUM(IF(szakacskonyvek.felhasznalo_id = ?, 1, 0)) AS mentve
                FROM receptek
                    LEFT JOIN recept_ertekelesek ON recept_ertekelesek.recept_id = receptek.recept_id
                    LEFT JOIN szakacskonyv_receptek ON szakacskonyv_receptek.recept_id = receptek.recept_id
                    LEFT JOIN szakacskonyvek ON szakacskonyv_receptek.szakacskonyv_id = szakacskonyvek.szakacskonyv_id
                WHERE receptek.slug = ? AND receptek.letezik = 1
                GROUP BY receptek.recept_id;", Settings::$uid, $slug);
        if($recept->sorokszama == 0)
            return null;

        $recept = $recept->EscapedSingleElem('recept_szoveg', 'tapanyagtablazat');
        $alapanyagok = new MySQLHandler("SELECT alapanyag_nev, recept_alapanyagok.mertekegyseg AS mertekegyseg, mennyiseg, kaloria, alapanyagok.mertekegyseg AS alapanyagegyseg, feherje, szenhidrat, zsir, alapanyagok.alapanyag_id AS alapanyag_id
                FROM recept_alapanyagok
                    INNER JOIN alapanyagok ON alapanyagok.alapanyag_id = recept_alapanyagok.alapanyag_id
                WHERE recept_alapanyagok.recept_id = ?;", $recept['recept_id']);

        $kepek = new MySQLHandler("SELECT fajl, receptkep_id, elsodleges FROM recept_kepek
                    INNER JOIN feltoltesek ON feltoltesek.feltoltes_id = recept_kepek.feltoltes_id  
                WHERE recept_id = ? AND latszik = 1
                ORDER BY elsodleges DESC;", $recept['recept_id']);

        $cimkek = new MySQLHandler("SELECT receptcimke_id FROM cimke_recept WHERE recept_id = ?;", $recept['recept_id']);

        return ['recept' => $recept, 'alapanyagok' => $alapanyagok->EscapedArray(), 'kepek' => $kepek->EscapedArray(), 'cimkek' => $cimkek->AsArray(null, true)];
    }

    public static function GetReceptek(int $startindex = 0, int $dbszam = 20, ?array $cimkek = null) : array {
        $receptek = new MySQLHandler();
        if($cimkek == null) {
            $receptek->Prepare(self::$alap_lista_query . self::$alap_lista_query_where . ' GROUP BY receptek.recept_id' . self::$alap_lista_query_order . ' LIMIT ?, ?;');
            $receptek->Run(Personalvars::$uid, Personalvars::$uid, $startindex, $dbszam);
        }
        else {
            $cimke_ids = [];
            foreach($cimkek as $cimke)
                $cimke_ids[] = '?';
            $qparams = [ Personalvars::$uid, Personalvars::$uid, ...$cimkek, count($cimkek), $startindex, $dbszam ];
            $receptek->Prepare(self::$alap_lista_query .
                ' INNER JOIN cimke_recept ON cimke_recept.recept_id = receptek.recept_id' .
                self::$alap_lista_query_where . ' AND cimke_recept.receptcimke_id IN (' . implode(',', $cimke_ids) . ')
                GROUP BY receptek.recept_id
                HAVING COUNT(DISTINCT cimke_recept.receptcimke_id) = ?' . self::$alap_lista_query_order . ' LIMIT ?, ?;');
            $receptek->Run(...$qparams);
        }

        return $receptek->EscapedArray();
    }

    public static function GetReceptekFuzzy(array $searcharr, string $column_name) : array {
        $receptek = new MySQLHandler();
        // Tudom, hogy ezzel csökkentem a prepared statement biztonságát, de egyrészt a Settings:$uid értéke
        // nem user-től érkezik, másrészt a GetFuzzyList függvényt komolyabban módosítani kellene,
        // hogy kezelni tudjon egynél több változót, és erre most nincs időm.
        $uid = Settings::$uid;
        if(!$uid)
            $uid = 0;
        $where = str_replace('?', $uid, self::$alap_lista_query_where);
        $alapquery = str_replace('?', $uid, self::$alap_lista_query);
        $receptek->Prepare($alapquery . $where . ' AND recept_nev LIKE ? GROUP BY receptek.recept_id' . self::$alap_lista_query_order);
        return $receptek->GetFuzzyList($searcharr, $column_name);
    }

    public static function GetSzakacskonyv(string $uid) : array {
        $receptek = new MySQLHandler(self::$alap_lista_query .
            self::$szakacskonyv_lista_query_where .
            ' GROUP BY receptek.recept_id' .
            self::$alap_lista_query_order, Settings::$uid, Settings::$uid, Settings::$uid, Settings::$uid);
        return $receptek->EscapedArray();
    }

    public static function GetReceptIrasjog(?int $felhasznalo_id, int $elem_id) : bool {
        if($felhasznalo_id == null)
            return false;
        $irasjog = new MySQLHandler("SELECT slug FROM receptek WHERE (recept_id = ? OR slug = ?) AND felhasznalo_id = ?;", $elem_id, $elem_id, $felhasznalo_id);
        if($irasjog->sorokszama == 0)
            return false;
        else
            return true;
    }

    public static function Ertekeles(int $uid, int $recept_id, int $ertekelesertek) : bool {
        $ertekeles = new MySQLHandler();
        $ertekeles->Prepare("INSERT INTO recept_ertekelesek (ertekeles, felhasznalo_id, recept_id)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE ertekeles = VALUES(ertekeles);");
        $ertekeles->Run($ertekelesertek, $uid, $recept_id);

        return $ertekeles->siker;
    }
}