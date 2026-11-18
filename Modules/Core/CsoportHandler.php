<?php
namespace Infoinventory\Modules\Core;

use Infoinventory\Classes\FormBuilder;

class CsoportHandler extends Page
{
    use CsoportHandlerDB;
    protected ?array $csoport = [];
    protected ?array $csoportok = [];

    protected string $viewsgyoker = __DIR__ . "/assets/csoport/views/";
    protected array $views = [
        'csoport' => 'csoport.php',
        'csoportok' => 'csoportok.php',
        'uj' => 'uj.php'
    ];
    protected array $validpagemethods = ['szerkeszt', 'uj'];
    protected array $aloldalak = ['tagok'];

    protected array $csoportstatuszok = [
        0 => 'általános célú csoport, jogosultságok osztásához',
        1 => 'belső szervezet',
        2 => 'partner szervezet',
        3 => 'külső szervezet'
    ];

    public function Router(array $params) : Page
    {
        if(!$this->modulolvasas)
            return new SinglePage(403, "Nincs jogosultságod az oldal megtekintéséhez!");

        $params = $this->ParseGet($params);
        if($this->selectedpage == 'csoport') {
            if($params['method'] == 'uj') {
                return $this->SetSelectedUj();
            }

            if(!isset($params['elemid']))
                return new SinglePage(404, "Nem választottál ki csoportot!");

            $this->csoport = self::GetCsoport($params['elemid']);

            if(!$this->csoport)
                return new SinglePage(404, "A kiválasztott csoport nem található!");

            $this->irasjog = $this->GetIrasjog(FELHASZNALO->uid, $this->csoport['csoport_id']);
            $this->olvasasjog = $this->GetOlvasasjog(FELHASZNALO->uid, $this->csoport['csoport_id']);

            if($params['method'] == 'szerkeszt') {
                if(!$this->irasjog)
                    return new SinglePage(403, "Nincs jogosultságod a csoport szerkesztéséhez!");
                $this->formvars = array_replace($this->formvars,
                    ['pagetitle' => $this->csoport['csoport_nev'] . ' szerkesztése',
                    'actiontarget' => ROOT_PATH . '/csoport/' . $this->csoport['slug'] . '/szerkeszt',
                    'buttontext' => 'Csoport mentése']);
                return $this->SetSelectedSzerkeszt($this->csoport, 'csoport');
            } else {
                if(!$this->olvasasjog)
                    return new SinglePage(403, "Nincs jogosultságod a csoport megtekintéséhez!");

                $tagok = self::GetCsoportTagok($this->csoport['csoport_id']);
                $ldap = self::DB_GetLDAPAzonositok($this->csoport['csoport_id']) ?? null;
                $this->csoport = ['adatok' => $this->csoport, 'tagok' => $tagok, 'ldap' => $ldap];

                $this->contextmenu = [
                    ['felirat' => 'Csoport szerkesztése', 'link' => ROOT_PATH . '/csoport/' . $this->csoport['adatok']['slug'] . '/szerkeszt'],
                    ['felirat' => 'Tagok szerkesztése', 'link' => ROOT_PATH . '/csoport/' . $this->csoport['adatok']['slug'] . '/tagok/szerkeszt']
                ];
                
                $this->view = $this->views['csoport'];
            }
        } elseif ($this->selectedpage == 'csoportok') {
            return $this->SetSelectedLista($params, $this->csoportok);
        }
        return $this;
    }

    protected static function GetCsoport(string|int $csoportid) : ?array {
        $csoport = self::DB_GetCsoport($csoportid);
        if($csoport['redir_id']) // Egy csoport redir_id-ja csak akkor nem null, ha névváltozás történt. Névváltozáskor létrejön egy redir_id-s elem a régi adatokkal, hogy a slug linkelés ne törjön el
            $csoport = self::DB_GetCsoport($csoport['redir_id']);

        return $csoport;
    }

    protected static function GetCsoportTagok(int $csoport_id) : array {
        return self::DB_GetCsoportTagok($csoport_id);
    }

    protected function GetList(int $startindex, int $elemperoldal, ?string $kereses) : array {
        $dbresult = null;
        if($this->modulolvasas == 3 || FELHASZNALO->godadmin) {
            $dbresult = self::DB_GetCsoportList(3, $kereses, $startindex, $elemperoldal);
        } elseif ($this->modulolvasas == 2 || $this->modulolvasas == 1) {
            $paramsfordb = FELHASZNALO->GetCsoportIDs();
            $paramsfordb = array_merge($paramsfordb, [$startindex, $elemperoldal]);
            $dbresult = self::DB_GetCsoportList(2, $kereses, ...$paramsfordb);
        }

        if(!$dbresult)
            return [];

        return $dbresult;
    }

    protected function Form(mixed $csoport = null) : FormBuilder {
        $form = (new FormBuilder())
            ->Hidden('csoport_id')
            ->Text('csoport_nev', 'A csoport neve', true)
            ->Text('rovid_nev', 'A csoport rövid megnevezése')
            ->Text('slug', 'A csoport azonosítója')
            ->Select('statusz', 'Csoport típusa', $this->csoportstatuszok, true)
            ->TextBox('leiras', 'A csoport leírása')
            ->SetHelperText('csoport_nev', 'A csoport, vagy szervezet teljes, hivatalos megnevezése')
            ->SetHelperText('rovid_nev', 'A csoport, vagy nevének hivatalos rövidített alakja')
            ->SetHelperText('slug', 'Ez az azonosító jelenik meg a címsorban. Bármilyen ékezetek nélküli egyedi szöveg lehet, de ajánlatos, hogy egyértelműen utaljon a csoport nevére')
            ->SetHelperText('statusz', 'A csoport, vagy szervezet típusa. Az ' . $this->csoportstatuszok[0] . ' lehetőséget csak akkor válasszuk, ha a csoport nem egy TELJES szervezeti elemre utal.')
            ->SetHelperText('leiras', 'A csoporthoz adható rövid leírás');
        if($csoport) {
            $form->SetValue('csoport_id', $csoport['csoport_id'])
                ->SetValue('csoport_nev', $csoport['csoport_nev'])
                ->SetValue('rovid_nev', $csoport['rovid_nev'])
                ->SetValue('slug', $csoport['slug'])
                ->SetValue('statusz', $csoport['statusz'])
                ->SetValue('leiras', $csoport['leiras']);
        }

        return $form;
    }
}