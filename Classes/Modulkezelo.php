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
        'engedelyezettek' => 3,
        'foadminok' => 4,
    ];

    public static function Inicializal(): void
    {
        if (self::$inicializalva) {
            return;
        }
        self::$inicializalva = true;

        if(!FELHASZNALO->uid)
            self::$telepitettmodulok = self::GetUnauthorizedModules();
        else
            self::$telepitettmodulok = self::GetAuthorizedModules();

        // print_r(self::$telepitettmodulok);

        foreach (glob(ROOT_DIR . '/Modules/*/manifest*.php') ?: [] as $manifest) {
            $module = require $manifest;
            if (!is_array($module) || !isset($module['handler'], $module['pages']) || !is_array($module['pages'])) {
                throw new \UnexpectedValueException("Hibás modul manifest: {$manifest}");
            }

            foreach ($module['pages'] as $route => $page) {
                if ($route === '' || isset(self::$oldalak[$route])) {
                    throw new \UnexpectedValueException("Üres vagy duplikált útvonal ({$route}) a manifestben: {$manifest}");
                }

                if(array_key_exists($module['slug'], self::$telepitettmodulok)) {
                    self::$oldalak[$route] = array_replace([
                        'menupont' => '', 'lathatosag' => 'mindenki', 'menuben' => false,
                        'sorrend' => null, 'szulo' => null, 'ikon' => null,
                    ], $page, ['handler' => $module['handler'], 'olvasas' => self::$telepitettmodulok[$module['slug']]['olvasas'], 'iras' => self::$telepitettmodulok[$module['slug']]['iras'], 'kezelomodul' => $module['slug']]);
                    if(self::$telepitettmodulok[$module['slug']]['olvasas'] === null && (self::$oldalak[$route]['lathatosag'] === 'tagok' || self::$oldalak[$route]['lathatosag'] === 'mindenki')) {
                        self::$telepitettmodulok[$module['slug']]['olvasas'] = 1;
                    }
                    if(self::$telepitettmodulok[$module['slug']]['iras'] === null&& (self::$oldalak[$route]['lathatosag'] === 'tagok' || self::$oldalak[$route]['lathatosag'] === 'mindenki')) {
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

    public static function GetAuthorizedModules() : array {
        if(!empty(self::$telepitettmodulok))
            return self::$telepitettmodulok;

        $telepitve = new MySQLHandler("SELECT slug, modul_id, sorrend, lathatosag, iras, olvasas
                FROM core_modulok
                    LEFT JOIN core_modulok_jogosultsagok USING (modul_id)
                WHERE (felhasznalo_id = ? OR lathatosag = 1 OR lathatosag = 2)
                    AND telepitve = 1;", FELHASZNALO->uid);
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
