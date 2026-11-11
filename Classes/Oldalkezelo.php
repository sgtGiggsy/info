<?php

namespace Infoinventory\Classes;

class Oldalkezelo
{
    public static array $oldalak = [];
    private static bool $inicializalva = false;

    public static array $jogszintek = [
        'mindenki' => 0,
        'vendeg' => 1,
        'tagok' => 2,
        'adminok' => 3,
        'foadminok' => 4,
    ];

    public static function Inicializal(): void
    {
        if (self::$inicializalva) {
            return;
        }
        self::$inicializalva = true;

        foreach (glob(ROOT_DIR . '/Modules/*/manifest*.php') ?: [] as $manifest) {
            $module = require $manifest;
            if (!is_array($module) || !isset($module['handler'], $module['pages']) || !is_array($module['pages'])) {
                throw new \UnexpectedValueException("Hibás modul manifest: {$manifest}");
            }

            foreach ($module['pages'] as $route => $page) {
                if ($route === '' || isset(self::$oldalak[$route])) {
                    throw new \UnexpectedValueException("Üres vagy duplikált útvonal ({$route}) a manifestben: {$manifest}");
                }
                self::$oldalak[$route] = array_replace([
                    'menupont' => '', 'lathatosag' => 'mindenki', 'menuben' => false,
                    'sorrend' => null, 'szulo' => null, 'ikon' => null,
                ], $page, ['handler' => $module['handler']]);
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
}
