<?php

namespace Infoinventory\Modules;

use Infoinventory\Classes\FeltoltesHandler;
use Infoinventory\Classes\FormBuilder;
use Infoinventory\Classes\Settings;

class Felhasznalo extends Page
{
    protected array $felhasznalo;
    protected string $viewsgyoker = __DIR__ . "/views/felhasznalo/";
    protected FormBuilder $form;
    protected array $views = [
        'felhasznalo' => 'felhasznalo.php',
        'felhasznalok' => 'felhasznalok.php',
        'profil' => 'felhasznalo.php',
        'szerkesztes' => 'szerkesztes.php'
    ];

    protected array $szintek = [
        1 => 'Felhasználó',
        2 => 'Admin',
        3 => 'Oldal tulajdonos'
    ];

    public function Router(array $params) : Page {
        $this->validpagemethods = ['szerkesztes'];
        $selecteduser = null;
        $params = $this->ParseGet($params);
        if($this->selectedpage == 'felhasznalo' || $this->selectedpage == 'profil') {
            if(!FELHASZNALO->uid)
                return new SinglePage(403);

            if(!Settings::$admin || (Settings::$admin && !$params['elemid']))
                $selecteduser = FELHASZNALO->uid;
            else
                $selecteduser = $params['elemid'];

            $this->felhasznalo = FelhasznaloDB::GetFelhasznalo($selecteduser);
            if(!$params['method']) {
                $this->view = $this->views['felhasznalo'];
            }

            if($params['method'] == 'szerkesztes') {
                if(!Settings::$foadmin && $this->felhasznalo['szint'] == 3) {
                    return new SinglePage(403);
                }

                $this->muvelet = 'szerkesztes';
                $this->view = $this->views['szerkesztes'];
                $this->form = $this->Form($this->felhasznalo);
            }
        } elseif ($this->selectedpage == "felhasznalok") {
            if(!Settings::$admin)
                return new SinglePage(403);

            $this->view = $this->views['felhasznalok'];
            $startindex = 0;
            $elemperoldal = 20;
            $this->LapozasPrepare($startindex, $elemperoldal, $params);
            $this->felhasznalo = $this->LapozasFinalize(FelhasznaloDB::GetFelhasznalok($startindex, $elemperoldal), $elemperoldal);
        }

        return $this;
    }

    public function HtmlHead() : void {
        if ($this->selectedpage == "felhasznalo" || $this->selectedpage == "profil") {
            $this->title .= " - " . $this->felhasznalo['usernev'] . " profilja";
            $this->sitedesc = "Felhasználói profiloldal";
        } elseif ($this->selectedpage == "felhasznalok") {
            $this->title .= ' - Hűtőszekrény';
            $this->sitedesc = "Felhasználói hűtőszekrény";
        }
        $this->ablakcim = $this->title;
        include(__DIR__ . "/views/_assets/htmlheader.php");
    }

    protected function Form(array $felhasznalo) : FormBuilder {
        $form = (new FormBuilder())
            ->Hidden('felhasznalo_id')
            ->Text('usernev', 'Felhasználónév', true)
            ->Email('email', 'Email cím', true)
            ->Text('teljesnev', 'Név')
            ->SetValue('felhasznalo_id', $felhasznalo['felhasznalo_id'])
            ->SetValue('usernev', $felhasznalo['usernev'])
            ->SetValue('email', $felhasznalo['email'])
            ->SetValue('teljesnev', $felhasznalo['teljesnev']);
        if(Settings::$foadmin) {
            $form->Select('szint', 'Felhasználói szint', $this->szintek, true)
                ->SetValue('szint', $felhasznalo['szint']);
        }
        if(Settings::$admin) {
            $form->Select('allapot', 'Fiók állapota', [2 => 'Engedélyezett', 1 => 'Letiltott'], true)
                ->SetValue('allapot', $felhasznalo['allapot']);
        }

        return $form;
    }

    protected function Szerkesztes() : bool {
        $this->redirtarget = ROOT_PATH . "/felhasznalo";
        $pkepid = null;
        if(!isset($_POST['felhasznalo_id'])) {
            $this->mixintext = "Nem lett kiválasztva módosítani kívánt felhasználó!";
            return false;
        }

        if(!Settings::$foadmin && $this->felhasznalo['szint'] == 3) {
            $this->mixintext = "Egy főadmin fiókját csak főadmin szerkesztheti!";
            return false;
        }

        if(isset($_POST['szint']) && !Settings::$foadmin) {
            $this->mixintext = "A felhasználói szint megváltoztatására kizárólag a főadminnak van joga!";
            return false;
        }

        if(isset($_POST['allapot']) && !Settings::$admin) {
            $this->mixintext = "Fiókot engedélyezni, vagy letiltani kizárólag adminok tudnak!";
            return false;
        }

        if(!Settings::$admin && $_POST['felhasznalo_id'] != FELHASZNALO->uid) {
            $this->mixintext = "Felhasználók csak a saját fiókjukat szerkeszthetik!";
            return false;
        }

        foreach ($_POST as $key => $value) {
            if($value !== null)
                $_POST[$key] = htmlspecialchars($value);
        }

        if(isset($_FILES['profilkep'])) {
            $profilkep = new FeltoltesHandler($this->mediatypes, 'profilkepek', date('Y'), 'profilkep', FELHASZNALO->uid);
            $profilkep = $profilkep->Feltoltes($_FILES['profilkep']);
            print_r($profilkep);
            if($profilkep['eredmeny']) {
                $pkepid = $profilkep['uploadids'][0];
            }
        }

        $eredmeny = FelhasznaloDB::FelhasznaloSzerkeszt($_POST, $pkepid);
        if($eredmeny)
            $this->mixintext = "A fiók módosítása sikerült!";
        else
            $this->mixintext = "A fiók módosítása sikertelen!";

        return $eredmeny;
    }
}