<?php

namespace Infoinventory\Modules\Kozlemenyek;

use HTMLPurifier;
use HTMLPurifier_Config;
use Infoinventory\Classes\FormBuilder;
use Infoinventory\Modules\Core\Page;
use Infoinventory\Modules\Core\SinglePage;
use Infoinventory\Classes\Helpers;

class Kozlemeny extends Page
{
    use KozlemenyDB;
    protected array $views = [
        'kozlemeny' => 'kozlemeny.php',
        'kozlemenyek' => 'kozlemenyek.php'
    ];
    protected array $validpagemethods = ['uj', 'szerkeszt', 'torol'];
    public ?array $kozlemeny = null;

    public function Router() : Page {
        if($this->selectedview == "kozlemeny") {
            if($this->params['method'] == 'uj') {
                return $this->SetRouteUj();
            }

            if(!isset($this->params['elemid'])) {
                return new SinglePage(404, "Nincs kiválasztott közlemény!");
            }

            $this->kozlemeny = self::GetKozlemeny($this->params['elemid'], 1);

            if(!$this->kozlemeny)
                return new SinglePage(404, "A kiválasztott közlemény nem található!");

            $this->irasjog = $this->GetIrasjog($this->kozlemeny['felhasznalo_id'], $this->kozlemeny['csoport_id']);
            $this->olvasasjog = $this->GetOlvasasjog($this->kozlemeny['felhasznalo_id'], $this->kozlemeny['csoport_id']);

            if(!$this->olvasasjog)
                return new SinglePage(403, "Nincs jogosultságod a közlemény megtekintéséhez!");

            if($this->params['method'] == 'szerkeszt') {
                if (!$this->irasjog)
                    return new SinglePage(403, "Nincs jogosultságod a közlemény szerkesztéséhez!");

                $this->post_method = 'Szerkeszt';
            }
        }

        elseif($this->selectedview == "kozlemenyek") {
            $this->irasjog = $this->GetIrasjog();
            $this->kozlemeny = self::GetKozlemenyLista(0, 4, $this->modulolvasas, FELHASZNALO->GetCsoportIDs(), 1);
        }

        return $this;
    }

    public function SetRender() : void {
        $this->title .= ' - ';
        if($this->params['method'] == 'uj') {
            $this->title .= 'Új közlemény rögzítése';
            $this->sitedesc = "Új közlemény létrehozása";
            $this->SetViewUj(ROOT_PATH . '/kozlemeny/uj', 'Közlemény létrehozás', 'cim', '2-1', 2);
        }

        elseif ($this->selectedview == "kozlemeny") {
            $this->canonical = ROOT_PATH . '/kozlemeny/' . $this->kozlemeny['slug'];
            $this->title .= 'Közlemény: ';
            if($this->params['method'] == 'szerkeszt') {
                $this->SetViewSzerkeszt($this->kozlemeny, 'közlemény', ROOT_PATH . '/kozlemeny/' . $this->kozlemeny['slug'] . '/szerkeszt', 'Közlemény mentése');
                $this->title .= ucfirst(Helpers::NeveloHatarozo($this->kozlemeny['cim'])) . ' szerkesztése';
                $this->sitedesc = "A közlemény szerkesztése";
            } else {
                if($this->irasjog)
                    $this->contextmenu[] = ['felirat' => 'Közlemény szerkesztése', 'link' => ROOT_PATH . '/kozlemeny/' . $this->kozlemeny['slug'] . '/szerkeszt'];
                $this->title .= $this->kozlemeny['cim'];
                $this->sitedesc = $this->title;
            }
        }

        else {
            if($this->irasjog)
                $this->contextmenu[] = ['felirat' => 'Új közlemény', 'link' => ROOT_PATH . '/kozlemeny/uj'];
            $this->view = $this->views['kozlemenyek'];
            $this->title .= 'Közlemények';
            $this->sitedesc = "A közlemények listája";
        }
    }

    protected function Form(mixed $kozlemeny = null) : FormBuilder {
        $form = (new FormBuilder())
            ->TextBox('bevezetes', 'Bevezetés')
            ->TextBox('szovegtorzs', 'Közlemény törzs')
            ->Text('cim', 'Cím')
            ->Select('cimke', 'Címke', self::GetCimkeLista())
            ->Checkbox('publikalt', 'A közlemény állapota');
        if($kozlemeny != null && $this->irasjog) {
            $form->Text('slug', 'Link címe')
                ->Hidden('kozlemeny_id')
                ->SetValue('kozlemeny_id', $kozlemeny['kozlemeny_id'])
                ->SetValue('slug', $kozlemeny['slug'])
                ->SetValue('bevezetes', $kozlemeny['bevezetes'])
                ->SetValue('szovegtorzs', $kozlemeny['szovegtorzs'])
                ->SetValue('cim', $kozlemeny['cim'])
                ->SetValue('cimke', $kozlemeny['cimke'])
                ->SetValue('publikalt', $kozlemeny['publikalt']);
        }
        return $form;
    }

    public static function GetKozlemeny(int|string $kozlemeny_id, ?int $publikalt = 1) : ?array {
        return self::DB_GetKozlemeny($kozlemeny_id, $publikalt);
    }

    protected function GetKozlemenyLista(int $startindex, int $limit, int $moduljog, array $csoporttagsagok, int $publikalt) : ?array {
        return self::DB_GetKozlemenyLista($startindex, $limit, $moduljog, $csoporttagsagok, $publikalt);
    }

    private function PreparePostContent(string $muvelet) : array {
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $publikalt = isset($_POST['publikalt']) ? 1 : 0;
        $bevezetes = $purifier->purify($_POST['bevezetes']);
        $szovegtorzs = $purifier->purify($_POST['szovegtorzs']);

        $dbarray = [
            'cim' => $_POST['cim'],
            'bevezetes' => $bevezetes,
            'szovegtorzs' => $szovegtorzs,
            'publikalt' => $publikalt,
            'felhasznalo_id' => FELHASZNALO->uid,
            'cimke' => $_POST['cimke'] ?? null,
            'csoport_id' => $_POST['csoport_id'] ?? FELHASZNALO->szervezet ?? null
        ];
        if($muvelet == 'uj') {
            $slug = Helpers::SlugGenerator($_POST['cim']);
            $slug = Helpers::SlugVerifier($slug, [Kozlemeny::class, 'GetKozlemeny']);
            $dbarray['slug'] = $slug;
        }
        return $dbarray;
    }

    protected function Uj() : bool {
        if(!self::RequiredValidator('cim', 'bevezetes')) {
            $this->mixintext = "Kötelezően megjelölt mező hiányzik!";
            return false;
        }

        $dbarray = $this->PreparePostContent('uj');

        $eredmeny = self::DB_InsertKozlemeny('kozlemenyek_kozlemenyek', $dbarray);

        if($eredmeny === false) {
            $this->mixintext = "Új közlemény rögzítése sikertelen!";
            return false;
        }
        else {
            $this->mixintext = "Új közlemény rögzítése sikeres!";
            $this->redirtarget = ROOT_PATH . '/kozlemeny/' . $dbarray['slug'];
            return true;
        }
    }

    protected function Szerkeszt() : bool {
        $kozlemeny_id = $_POST['kozlemeny_id'] ?? null;
        if(!$kozlemeny_id) {
            $this->mixintext = "A közlemény azonosítója hiányzik!";
            return false;
        }

        if(!$this->kozlemeny) {
            $this->mixintext = "A kiválasztott közlemény nem található!";
            return false;
        }

        if(!self::RequiredValidator('cim', 'bevezetes')) {
            $this->mixintext = "Kötelezően megjelölt mező hiányzik!";
            return false;
        }

        $dbarray = $this->PreparePostContent('szerkeszt');

        $eredmeny = self::DB_UpdateKozlemeny('kozlemenyek_kozlemenyek', 'kozlemeny_id', $kozlemeny_id, $dbarray);
        if($eredmeny === false) {
            $this->mixintext = "A közlemény szerkesztése sikertelen!";
            return false;
        }
        else {
            $this->mixintext = "A közlemény szerkesztése sikeres!";
            $this->redirtarget = ROOT_PATH . '/kozlemeny/' . $this->kozlemeny['slug'];
            return true;
        }
    }

    public static function GetCimkeLista() : ?array {
        return self::DB_GetCimkeLista();
    }
}