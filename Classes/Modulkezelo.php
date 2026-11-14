<?php

namespace Infoinventory\Classes;

class Modulkezelo
{
    public static array $oldalak = [];
    private static array $telepitettmodulok = [];
    private static bool $inicializalva = false;

    public static array $jogszintek = [
        'vendeg' => 0,
        'mindenki' => 1,
        'tagok' => 2,
        'engedelyezettek' => 3
    ];

    public static array $jogosultsagnevek = [
        1 => 'saját elemek',
        2 => 'csoport elemei',
        3 => 'minden elem'
    ];

    public static function Inicializal(?int $felhasznalo_id = null): void
    {
        if(isset($felhasznalo_id)) {
            self::$oldalak = [];
            self::$telepitettmodulok = [];
            self::$inicializalva = false;
        }

        if (self::$inicializalva) {
            return;
        }
        self::$inicializalva = true;

        if($felhasznalo_id === null)
            $felhasznalo_id = FELHASZNALO->uid;

        if(!$felhasznalo_id)
            self::$telepitettmodulok = self::GetUnauthorizedModules();
        else
            self::$telepitettmodulok = self::GetAuthorizedModules($felhasznalo_id);

        foreach (glob(ROOT_DIR . '/Modules/*/manifest*.php') ?: [] as $manifest) {
            $module = require $manifest;
            if (!is_array($module) || !isset($module['handler'], $module['pages']) || !is_array($module['pages'])) {
                throw new \UnexpectedValueException("Hibás modul manifest: {$manifest}");
            }
            $modulnev = $module['nev'] ?? '';

            foreach ($module['pages'] as $route => $page) {
                if ($route === '' || isset(self::$oldalak[$route])) {
                    throw new \UnexpectedValueException("Üres vagy duplikált útvonal ({$route}) a manifestben: {$manifest}");
                }

                if(array_key_exists($module['slug'], self::$telepitettmodulok)) {
                    self::$oldalak[$route] = array_replace([
                        'menupont' => '', 'lathatosag' => 'mindenki', 'menuben' => false,
                        'sorrend' => null, 'szulo' => null, 'ikon' => null,
                    ], $page, ['handler' => $module['handler'], 'olvasas' => self::$telepitettmodulok[$module['slug']]['olvasas'], 'iras' => self::$telepitettmodulok[$module['slug']]['iras'], 'kezelomodul' => $module['slug']]);
                    self::$telepitettmodulok[$module['slug']]['modulnev'] = $modulnev;

                    if(self::$telepitettmodulok[$module['slug']]['olvasas'] === null
                        && (self::$oldalak[$route]['lathatosag'] === 'tagok' || self::$oldalak[$route]['lathatosag'] === 'mindenki')) {
                        self::$telepitettmodulok[$module['slug']]['olvasas'] = 1;
                    }
                    if(self::$telepitettmodulok[$module['slug']]['iras'] === null
                        && (self::$oldalak[$route]['lathatosag'] === 'tagok' || self::$oldalak[$route]['lathatosag'] === 'mindenki')) {
                        self::$telepitettmodulok[$module['slug']]['iras'] = 1;
                    }
                }
            }
        }
    }

    public static function Menupontok(): array
    {
        self::Inicializal();
        $rendezett = array_filter(self::$oldalak, fn($item) => $item['menuben'] === true);
        uasort($rendezett, fn($a, $b) => $a['sorrend'] <=> $b['sorrend']);
        return $rendezett;
    }

    public static function GetUnauthorizedModules() : array {
        if(!empty(self::$telepitettmodulok))
            return self::$telepitettmodulok;

        $telepitve = new MySQLHandler("SELECT slug, modul_id, sorrend, 0 AS olvasas, 0 AS iras
                FROM core_modulok
                WHERE (lathatosag = 0 OR lathatosag = 1)
                    AND telepitve = 1;");
        return $telepitve->AsArray("slug");
    }

    public static function GetAuthorizedModules(?int $felhasznalo_id = null) : array {
        if(!empty(self::$telepitettmodulok))
            return self::$telepitettmodulok;

        if($felhasznalo_id === null)
            $felhasznalo_id = FELHASZNALO->uid;

        $telepitve = new MySQLHandler("SELECT slug, core_modulok.modul_id AS modul_id,
                    sorrend, lathatosag, iras, olvasas
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
        self::Inicializal();
        if(!array_key_exists($page, self::$oldalak))
            return '';
        else
            return self::$oldalak[$page]['kezelomodul'];
    }

    public static function ModulJogosultsag(string $page) : array {
        self::Inicializal();
        $slug = self::GetModuleSlug($page);
        if(!array_key_exists($slug, self::$telepitettmodulok)) {
            return ['olvasas' => 0, 'iras' => 0];
        }
        else
            return ['olvasas' => self::$telepitettmodulok[$slug]['olvasas'], 'iras' => self::$telepitettmodulok[$slug]['iras']];
    }
}
