<?php

namespace Infoinventory\Modules\Core;

use HTMLPurifier;
use HTMLPurifier_Config;
use Infoinventory\Classes\FormBuilder;
use Infoinventory\Classes\ModuleLoader;
use Infoinventory\Classes\Helpers;

class ModuleHandler extends Page
{
    use ModuleHandlerDB;
    protected array $views = [
        'modul' => 'modul.php',
        'modulok' => 'modulok.php'
    ];
    protected array $validpagemethods = ['telepit', 'kikapcsol', 'install', 'uninstall'];
    protected ?array $modulok = null;
    protected ?array $telepitett = null;
    public ?array $modul = null;

    public function Router() : Page {
        // A modulok globális mivolta miatt csak 3-as jogosultsággal van értelme a hozzáférésnek,
        // így ennél összetettebb ellenőrzésre itt nincs szükség.
        $this->irasjog = $this->moduliras == 3;
        $this->olvasasjog = $this->modulolvasas == 3;

        if(!$this->olvasasjog)
            return new SinglePage(403, "Nincs jogosultságod a modul megtekintéséhez!");
        if(!$this->irasjog)
            return new SinglePage(403, "Nincs jogosultságod a modulok kezeléséhez!");

        if($this->selectedview == "modul") {
            if($this->params['method'] == 'install') {
                if(!$this->irasjog)
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
        if($this->params['method'] == 'uj') {
            $this->title .= 'Új modul rögzítése';
            $this->sitedesc = "Új modul létrehozása";
            $this->SetViewUj(ROOT_PATH . '/modul/uj', 'Modul létrehozás', 'cim', '2-1', 2);
        }

        elseif ($this->selectedview == "modul") {

        }

        else {
            if($this->irasjog)
                $this->contextmenu[] = ['felirat' => 'Új modul', 'link' => ROOT_PATH . '/modul/uj'];
            $this->view = $this->views['modulok'];
            $this->title .= 'Modulok';
            $this->sitedesc = "A modulok listája";
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