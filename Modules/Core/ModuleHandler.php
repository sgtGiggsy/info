<?php

namespace Infoinventory\Modules\Core;

use HTMLPurifier;
use HTMLPurifier_Config;
use Infoinventory\Classes\FormBuilder;
use Infoinventory\Classes\ModuleLoader;
use Infoinventory\Classes\Helpers;

/**
 * class ModuleHandler
 *
 * Az osztály a modulok telepítéséért, és a jogosultságok felhasználók részére történő kiosztásáért felelős.
 * A modulok telepítéséhez a felhasználónak 3-as szintű joggal kell rendelkeznie.
 * A modulhoz 2-es joggal rendelkező felhasználók más felhasználók részére adhatnak hozzáférést a modulhoz (2-es szintig)
 */

class ModuleHandler extends Page
{
    use ModuleHandlerDB;
    protected array $views = [
        'modul' => 'modul.php',
        'modulok' => 'modulok.php'
    ];
    protected array $validpagemethods = ['telepit', 'kikapcsol', 'install', 'uninstall'];
    protected ?array $modulok = null;
    protected ?string $modulnev = null;
    protected ?array $telepitett = null;
    public ?array $modul = null;
    protected array $jogszintek = [
        0 => 'Nincs hozzáférés',
        1 => 'Csak saját tartalmak',
        2 => 'Saját csoportok tartalmai',
        3 => 'Teljes hozzáférés'
    ];
    protected array $lathatosagok = [
        0 => 'Csak vendégek',
        1 => 'Mindenki',
        2 => 'Bármely regisztrált felhasználó',
        3 => 'Csak jogosult felhasználók'
    ];

    public function Router() : Page {
        $this->irasjog = $this->moduliras >= 2;
        $this->olvasasjog = $this->modulolvasas >= 2;

        if(!$this->olvasasjog)
            return new SinglePage(403, "Nincs jogosultságod a modulok megtekintéséhez!");
        if(!$this->irasjog)
            return new SinglePage(403, "Nincs jogosultságod a modulok kezeléséhez!");

        if($this->selectedview == "modul") {
            if($this->params['method'] == 'install') {
                if($this->moduliras != 3)
                    return new SinglePage(403, "Nincs jogosultságod a modul telepítéséhez!");

                if(!isset($this->params['elemid'])) {
                    return new SinglePage(404, "Nincs telepítésre kiválasztott modul!");
                }

                $this->modulok = ModuleLoader::GetAllModules(true);
                $this->modul = $this->modulok[$this->params['elemid']];

                $this->post_method = 'InstallModule';

                return $this;
            }

            if(!isset($this->params['elemid'])) {
                return new SinglePage(404, "Nincs kiválasztott modul!");
            }

            $this->modul = self::GetModul($this->params['elemid'], 1);
            var_dump($this->modul);
        }

        elseif($this->selectedview == "modulok") {
            $this->modulok = ModuleLoader::GetAllModules(true);
            $this->telepitett = self::GetInstalledModules();
        }

        return $this;
    }

    public function SetRender() : void {
        $this->viewsgyoker = ROOT_DIR . '/Modules/Core/assets/modul/views/';
        $this->title .= ' - ';
        if ($this->selectedview == "modul") {
            $this->modulnev = MODULOK->GetModuleDetails($this->params['elemid'])['nev'];
            $this->view = $this->views['modul'];
            $this->title .= $this->modulnev;
            $this->sitedesc = "A modul részletes adatai";
        }
        else {
            $this->view = $this->views['modulok'];
            $this->title .= 'Modulok';
            $this->sitedesc = "Az elérhető modulok listája";
        }
    }

    private static function GetModul(string $slug, ?int $telepitve = null) : ?array {
        return self::DB_GetModul($slug, $telepitve);
    }

    private static function GetInstalledModules() : ?array {
        return self::DB_GetInstalledModules();
    }

    protected function InstallModule() : bool {

        $class = $this->modul['handler'];
        $module = new $class(null, 3, 3);

        $install = $module->Install();

        if($install === false) {
            $this->mixintext = "A modul telepítése sikertelen!";
            return false;
        }
        else {
            $this->mixintext = "A modul telepítése sikeres!";
            return true;
        }
    }
}