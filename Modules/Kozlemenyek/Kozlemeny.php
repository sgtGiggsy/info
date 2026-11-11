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
    protected string $viewsgyoker = __DIR__ . "/views/";
    protected FormBuilder $form;
    protected array $views = [
        'kozlemeny' => 'kozlemeny.php',
        'kozlemenyek' => 'kozlemenyek.php',
        'szerkeszt' => 'szerkeszt.php',
        'uj' => 'uj.php'
    ];
    public ?array $kozlemeny = null;

    public function Router(array $params) : Page {
        $this->validpagemethods = ['uj', 'szerkeszt', 'torol'];
        $params = $this->ParseGet($params);
        $this->params = $params;

        if($this->selectedpage == "kozlemeny") {
            if($params['method'] == 'uj' && FELHASZNALO->uid) {
                $this->muvelet = 'uj';
                $this->form = $this->Form('uj');
                $this->view = $this->views['uj'];
                return $this;
            }

            if(!isset($params['elemid'])) {
                $kozlemeny = new Kozlemeny('kozlemenyek');
                $kozlemeny->Router($params);
                return $kozlemeny;
            }

            $this->kozlemeny = KozlemenyDB::GetKozlemeny($params['elemid']);

            if(!$this->kozlemeny)
                return new SinglePage('404');

            $this->irasjog = $this->GetIrasjog($this->kozlemeny['kozlemeny_id']);

            if($params['method'] == 'szerkeszt' && FELHASZNALO->uid && $this->irasjog) {
                $this->muvelet = 'szerkeszt';
                $this->form = $this->Form('szerkeszt', $this->kozlemeny['kozlemeny_id']);
                $this->view = $this->views['szerkeszt'];
            }
            elseif($params['method'] == 'torol' && FELHASZNALO->uid && $this->irasjog) {
                $this->redirtarget = ROOT_PATH . '/kozlemenyek';
                $this->muvelet = 'torol';
            }
            elseif($params['method'] != 'szerkeszt' && !$this->GetOlvasasjog($params['elemid']))
                return new SinglePage('403');

            $this->cimke = $this->kozlemeny['cim'];
            $this->view = $this->views['kozlemeny'];
        }
        elseif($this->selectedpage == "kozlemenyek") {
            $this->kozlemeny = KozlemenyDB::GetKozlemenyLista();
            $this->view = $this->views['kozlemenyek'];
        }

        //print_r($this->kozlemeny);
        return $this;
    }

    public function HtmlHead() : void {
        $this->title .= ' - ';
        //ucfirst($this->cimke);
        if ($this->selectedpage == "kozlemeny") {
            $this->title .= 'Közlemény: ';
            if ($this->muvelet == "uj") {
                $this->title .= 'Új rögzítése';
                $this->sitedesc = "Új közlemény létrehozása";
            } else {
                if ($this->muvelet == "szerkeszt"){
                    $this->title .= ucfirst(Helpers::NeveloHatarozo($this->kozlemeny['nev'])) . 'szerkesztése';
                    $this->sitedesc = "Az alapanyag szerkesztése";
                }
                else {
                    $this->title .= $this->kozlemeny['cim'];
                    $this->sitedesc = $this->title;
                }
                $this->canonical = ROOT_PATH . '/kozlemeny/' . $this->kozlemeny['slug'];
            }
        }
        $this->ablakcim = $this->title;
        $this->HtmlHeadRender();
    }

    private function Form(string $muvelet, ?array $kozlemeny = null) : FormBuilder {
        $form = (new FormBuilder())
            ->TextBox('bevezetes', 'Bevezetés')
            ->TextBox('szovegtorzs', 'Közlemény törzs')
            ->Text('cim', 'Cím')
            ->Checkbox('publikalt', 'A közlemény állapota');
        if($muvelet == 'szerkeszt' && $kozlemeny != null && $this->GetIrasjog($kozlemeny['kozlemeny_id'])) {
            $form->Text('slug', 'Link címe')
                ->Hidden('kozlemeny_id')
                ->SetValue('kozlemeny_id', $kozlemeny['kozlemeny_id'])
                ->SetValue('slug', $kozlemeny['slug'])
                ->SetValue('bevezetes', $kozlemeny['bevezetes'])
                ->SetValue('szovegtorzs', $kozlemeny['szovegtorzs'])
                ->SetValue('cim', $kozlemeny['cim'])
                ->SetValue('publikalt', $kozlemeny['publikalt']);
        }
        return $form;
    }

}