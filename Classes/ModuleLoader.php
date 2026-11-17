<?php

namespace Infoinventory\Classes;

class ModuleLoader
{
    /**
     * @var array $modulok A telepített modulok listája, a manifest fájlok alapján
     */
    public static array $modulok = [];
    /**
     * @var array $oldalak A telepített modulok felhasználó részére engedélyezett oldalainak listája
     */
    public readonly array $oldalak;

    /**
     * @var array $felhasznalomodulok A felhasználó számára engedélyezett modulok listája a Felhasznalo objektum részére
     */
    public array $felhasznalomodulok = [];

    public static array $jogosultsagnevek = [
        1 => 'saját elemek',
        2 => 'csoport elemei',
        3 => 'minden elem'
    ];

    public function __construct(?int $felhasznalo_id = null) {
        if(count(self::$modulok) === 0) {
            self::$modulok = $this->GetAllModules();
        }

        if($felhasznalo_id)
            $usermoduljai = self::DB_GetAuthorizedModules($felhasznalo_id);
        else
            $usermoduljai = self::DB_GetUnauthorizedModules();

        $this->GenerateOldalak(self::$modulok, $usermoduljai);
    }

    private function GetAllModules() : array {
        $oldalak = [];
        foreach (glob(ROOT_DIR . '/Modules/*/manifest*.php') ?: [] as $manifest) {
            $module = require $manifest;
            if (!is_array($module) || !isset($module['handler'], $module['pages']) || !is_array($module['pages']) || !isset($module['slug']) || !isset($module['nev'])) {
                throw new \UnexpectedValueException("Hibás modul manifest: {$manifest}");
            }

            foreach ($module['pages'] as $route => $page) {
                if ($route === '' || isset($oldalak[$route])) {
                    throw new \UnexpectedValueException("Üres vagy duplikált útvonal ({$route}) a manifestben: {$manifest}");
                }
                $oldalak[$route]['menupont'] = $page['menupont'] ?? '';
                $oldalak[$route]['lathatosag'] = $page['lathatosag'] ?? 'mindenki';
                $oldalak[$route]['menuben'] = $page['menuben'] ?? false;
                $oldalak[$route]['sorrend'] = $page['sorrend'] ?? null;
                $oldalak[$route]['szulo'] = $page['szulo'] ?? null;
                $oldalak[$route]['ikon'] = $page['ikon'] ?? null;
                $oldalak[$route]['handler'] = $module['handler'];
                $oldalak[$route]['kezelomodul'] = $module['slug'];
                $oldalak[$route]['kezelonev'] = $module['nev'];
            }
        }

        return $oldalak;
    }

    private function GenerateOldalak(array $mindenmodul, array $felhasznalomodulfromdb) : void {
        $oldalak = [];
        foreach($mindenmodul as $slug => $modul) {
            $kezelomodul = $modul['kezelomodul'];
            if(array_key_exists($kezelomodul, $felhasznalomodulfromdb)) {
                $oldalak[$slug] = $modul;
                $this->felhasznalomodulok[$slug]  = $felhasznalomodulfromdb[$kezelomodul];
                $this->felhasznalomodulok[$slug]['modulolvasas']  = $felhasznalomodulfromdb[$kezelomodul]['modulolvasas'] ?? self::Szintek($modul['lathatosag']);
                $this->felhasznalomodulok[$slug]['moduliras']  = $felhasznalomodulfromdb[$kezelomodul]['moduliras'] ?? self::Szintek($modul['lathatosag']);
                $oldalak[$slug]['olvasas'] = $this->felhasznalomodulok[$slug]['modulolvasas'];
                $oldalak[$slug]['iras'] = $this->felhasznalomodulok[$slug]['moduliras'];
            }
        }
        $this->oldalak = $oldalak;
    }

    private static function Szintek(string $lathatosag) : int {
        // A függvény meghívásakor még jó eséllyel nem áll rendelkezésre a FELHASZNALO objektum, ezért a jogosultságot a session alapján ellenőrizzük.
        if($_SESSION['uid']) {
            if($lathatosag === 'tagok' || $lathatosag === 'mindenki')
                return 1;
            else
                return 0;
        }
        else {
            if($lathatosag === 'mindenki' || $lathatosag === 'vendeg')
                return 1;
            else
                return 0;
        }
    }

    public function Menupontok(): array
    {
        $rendezett = array_filter($this->oldalak, fn($item) => $item['menuben'] === true);
        uasort($rendezett, fn($a, $b) => $a['sorrend'] <=> $b['sorrend']);
        return $rendezett;
    }

    private static function DB_GetUnauthorizedModules() : array {
        $telepitve = new MySQLHandler("SELECT slug, modul_id, sorrend, 0 AS modulolvasas, 0 AS moduliras
                FROM core_modulok
                WHERE (lathatosag = 0 OR lathatosag = 1)
                    AND telepitve = 1;");
        return $telepitve->AsArray("slug");
    }

    private static function DB_GetAuthorizedModules(int $felhasznalo_id) : array {
        $telepitve = new MySQLHandler("SELECT slug, core_modulok.modul_id AS modul_id,
                    sorrend, lathatosag, iras AS moduliras, olvasas AS modulolvasas
                FROM core_modulok
                    LEFT JOIN core_modulok_jogosultsagok ON core_modulok.modul_id = core_modulok_jogosultsagok.modul_id
                        AND core_modulok_jogosultsagok.felhasznalo_id = ?
                WHERE telepitve = 1
                    AND (core_modulok_jogosultsagok.felhasznalo_id IS NOT NULL
                        OR core_modulok.lathatosag IN (1, 2)
                    );", $felhasznalo_id);
        return $telepitve->AsArray("slug");
    }

    public static function GetModuleSlug(string $page) : string {
        if(!array_key_exists($page, self::$modulok))
            return '';
        else
            return self::$modulok[$page]['kezelomodul'];
    }

    private static function ModulJogForDisplay(array $jogosultsagok) : array {
        $display = [];
        foreach($jogosultsagok as $slug => $modul) {
            if(array_key_exists($slug, self::$modulok))
                $display[$slug] = [
                    'nev' => self::$modulok[$slug]['kezelonev'],
                    'modulolvasas' => ($modul['modulolvasas']) ? self::$jogosultsagnevek[$modul['modulolvasas']] : 'saját elemek',
                    'moduliras' => ($modul['moduliras']) ? self::$jogosultsagnevek[$modul['moduliras']] : 'saját elemek'
                ];
        }
        return $display;
    }

    public static function GetModuleJogosultsagok(int $felhasznalo_id) : array {
        $jogosultsagok = self::DB_GetAuthorizedModules($felhasznalo_id);
        return self::ModulJogForDisplay($jogosultsagok);
    }
}
