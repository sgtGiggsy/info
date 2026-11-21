<?php
namespace Infoinventory\Modules\Core;

use Infoinventory\Classes\FormBuilder;

class CsoportHandler extends Page
{
    use CsoportHandlerDB;
    protected ?array $csoport = [];
    protected ?array $csoportok = [];

    protected array $views = [
        'csoport' => 'csoport.php',
        'csoportok' => 'csoportok.php'
    ];
    protected array $validpagemethods = ['szerkeszt', 'uj'];
    protected array $aloldalak = ['tagok', 'ldap-azonositok'];

    protected array $csoportstatuszok = [
        0 => 'létesítményhez tartozó csoport',
        1 => 'belső szervezet',
        2 => 'partner szervezet',
        3 => 'külső szervezet',
        4 => 'általános célú, nem szervezethez kötött csoport',
    ];

    public function Router() : Page
    {
        if(!$this->modulolvasas)
            return new SinglePage(403, "Nincs jogosultságod az oldal megtekintéséhez!");

        if($this->selectedview == 'csoport') {
            if($this->params['method'] == 'uj') {
                return $this->SetRouteUj();
            }

            if(!isset($this->params['elemid']))
                return new SinglePage(404, "Nem választottál ki csoportot!");

            $this->csoport = self::GetCsoport($this->params['elemid']);

            if(!$this->csoport)
                return new SinglePage(404, "A kiválasztott csoport nem található!");

            //! NEM JÓ A LOGIKA ///
            $this->irasjog = $this->GetIrasjog(FELHASZNALO->uid, $this->csoport['csoport_id']);
            $this->olvasasjog = $this->GetOlvasasjog(FELHASZNALO->uid, $this->csoport['csoport_id']);

            if($this->params['method'] == 'szerkeszt') {
                if(!$this->irasjog)
                    return new SinglePage(403, "Nincs jogosultságod a csoport szerkesztéséhez!");

                if($this->params['aloldal'] == 'tagok') {
                    if(!$this->post_method)
                        $this->csoport = ['adatok' => $this->csoport, 'tagok' => self::GetCsoportTagok($this->csoport['csoport_id'])];
                    $this->post_method = 'SzerkesztCsoportTagok';
                } elseif($this->params['aloldal'] == 'ldap-azonositok') {
                    if(!$this->post_method)
                        $this->csoport = ['adatok' => $this->csoport, 'ldap' => self::DB_GetLDAPAzonositok($this->csoport['csoport_id']) ?? null];
                    $this->post_method = 'SzerkesztCsoportLDAPAzonositok';
                } else {
                    $this->csoport = ['adatok' => $this->csoport];
                    $this->post_method = 'SzerkesztCsoport';
                }
                return $this;
            } else {
                if(!$this->olvasasjog)
                    return new SinglePage(403, "Nincs jogosultságod a csoport megtekintéséhez!");

                $tagok = self::GetCsoportTagok($this->csoport['csoport_id']);
                $ldap = self::DB_GetLDAPAzonositok($this->csoport['csoport_id']) ?? null;
                $this->csoport = ['adatok' => $this->csoport, 'tagok' => $tagok, 'ldap' => $ldap];
            }
        } elseif ($this->selectedview == 'csoportok') {
            return $this->SetSelectedLista($this->params, $this->csoportok);
        }
        return $this;
    }

    public function SetRender() : void {
        $this->viewsgyoker = __DIR__ . "/assets/csoport/views/";
        if($this->params['method'] == 'uj') {
            $this->title .= " - Új csoport felvétele";
            $this->sitedesc = "Új csoport felvétele";
            $this->SetViewUj(ROOT_PATH . '/csoport/uj', 'Csoport létrehozás');
        } elseif($this->params['aloldal']) {
            $this->contextmenu[] = ['felirat' => 'Csoport szerkesztése', 'link' => ROOT_PATH . '/csoport/' . $this->csoport['adatok']['slug'] . '/szerkeszt'];
            if ($this->params['aloldal'] == 'tagok') {
                $this->SetViewSzerkeszt($this->csoport['tagok'], 'csoport', ROOT_PATH . '/csoport/' . $this->csoport['adatok']['slug'] . '/tagok/szerkeszt', 'Csoport tagjainak mentése');
                $this->title .= " - Csoport: " . $this->csoport['adatok']['csoport_nev'] . " tagjainak szerkesztése";
                $this->sitedesc = "Csoport tagjainak szerkesztése";
                $this->contextmenu[] = ['felirat' => 'LDAP azonosítók szerkesztése', 'link' => ROOT_PATH . '/csoport/' . $this->csoport['adatok']['slug'] . '/ldap-azonositok/szerkeszt'];
            } elseif ($this->params['aloldal'] == 'ldap-azonositok') {
                $this->SetViewSzerkeszt($this->csoport['ldap'], 'csoport', ROOT_PATH . '/csoport/' . $this->csoport['adatok']['slug'] . '/ldap-azonositok/szerkeszt', 'LDAP azonosítók mentése');
                $this->title .= " - Csoport: " . $this->csoport['adatok']['csoport_nev'] . " LDAP azonosítóinak szerkesztése";
                $this->sitedesc = "Csoport LDAP azonosítóinak szerkesztése";
                $this->contextmenu[] = ['felirat' => 'Tagok szerkesztése', 'link' => ROOT_PATH . '/csoport/' . $this->csoport['adatok']['slug'] . '/tagok/szerkeszt'];
            }
        } elseif ($this->selectedview == "csoport") {
            $this->contextmenu = [
                ['felirat' => 'Csoport szerkesztése', 'link' => ROOT_PATH . '/csoport/' . $this->csoport['adatok']['slug'] . '/szerkeszt'],
                ['felirat' => 'Tagok szerkesztése', 'link' => ROOT_PATH . '/csoport/' . $this->csoport['adatok']['slug'] . '/tagok/szerkeszt'],
                ['felirat' => 'LDAP azonosítók szerkesztése', 'link' => ROOT_PATH . '/csoport/' . $this->csoport['adatok']['slug'] . '/ldap-azonositok/szerkeszt']
            ];
            if($this->params['method'] == 'szerkeszt') {
                $this->SetViewSzerkeszt($this->csoport['adatok'], 'csoport', ROOT_PATH . '/csoport/' . $this->csoport['adatok']['slug'] . '/szerkeszt', 'Csoport mentése');
                $this->title .= " - Csoport: " . $this->csoport['adatok']['csoport_nev'] . " szerkesztése";
                $this->sitedesc = "Csoport szerkesztése";
            } else {
                $this->title .= " - Csoport: " . $this->csoport['adatok']['csoport_nev'];
                $this->sitedesc = "Csoport adatlapja";
            }
        } else {
            $this->title .= " - Csoportok";
            $this->sitedesc = "Csoportok listája";
        }
        $this->ablakcim = $this->title;
    }

    protected static function GetCsoport(string|int $csoportid) : ?array {
        $csoport = self::DB_GetCsoport($csoportid);
        if($csoport && $csoport['redir_id']) // Egy csoport redir_id-ja csak akkor nem null, ha névváltozás történt. Névváltozáskor létrejön egy redir_id-s elem a régi adatokkal, hogy a slug linkelés ne törjön el
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
        if(is_null($csoport))
            return $this->CsoportSzerkesztForm();

        if($this->params['aloldal'] == 'tagok')
            return $this->CsoportTagokForm($csoport);
        if($this->params['aloldal'] == 'ldap-azonositok')
            return $this->CsoportLDAPForm($csoport);
        return $this->CsoportSzerkesztForm($csoport);
    }

    protected function CsoportSzerkesztForm(?array $csoport = null) : FormBuilder {
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
            ->SetHelperText('statusz', 'A csoport, vagy szervezet típusa. Az ' . $this->csoportstatuszok[4] . ' lehetőséget csak akkor válasszuk, ha a csoport nem egy TELJES szervezeti elemre utal.')
            ->SetHelperText('leiras', 'A csoporthoz adható rövid leírás')
            ->SetValue('statusz', 1);
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

    protected function CsoportTagokForm(?array $csoport = null) : FormBuilder{
        return (new FormBuilder());
    }

    protected function CsoportLDAPForm(?array $csoport = null) : FormBuilder{
        return (new FormBuilder());
    }
}