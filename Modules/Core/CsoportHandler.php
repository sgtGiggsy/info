<?php
namespace Infoinventory\Modules\Core;

use Infoinventory\Classes\FormBuilder;

class CsoportHandler extends Page
{
    use CsoportHandlerDB;
    protected array $csoport = [];
    protected array $csoportok = [];

    protected string $viewsgyoker = __DIR__ . "/assets/csoport/views/";
    protected array $views = [
        'csoport' => 'csoport.php',
        'csoportok' => 'csoportok.php',
        'uj' => 'uj.php',
        'szerkeszt' => 'szerkeszt.php'
    ];
    protected array $validpagemethods = ['szerkeszt', 'uj'];

    protected array $csoportstatuszok = [
        0 => 'általános',
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
                return $this->SetSelectedSzerkeszt($this->csoport, 'csoport');
            } else {
                if(!$this->olvasasjog)
                    return new SinglePage(403, "Nincs jogosultságod a csoport megtekintéséhez!");

                $tagok = self::GetCsoportTagok($this->csoport['csoport_id']);
                $this->csoport = ['csoport' => $this->csoport, 'tagok' => $tagok];
                $this->view = $this->views['csoport'];
            }
        } elseif ($this->selectedpage == 'csoportok') {
            return $this->SetSelectedLista($params, $this->csoportok);
        }
        return $this;
    }

    protected static function GetCsoport(string|int $csoportid) : ?array {
        return self::DB_GetCsoport($csoportid);
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
}