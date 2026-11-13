<?php

namespace Infoinventory\Modules\Core;
use Exception;
use Infoinventory\Classes\Settings;
use Infoinventory\Classes\Felhasznalo;
use Infoinventory\Classes\Modulkezelo;

Class Page
{
    // <--- HTML META TAG-EK ---> //
    public string $cimke = "";
    protected array $keywords = array();
    protected ?string $canonical = null;
    protected ?string $title;
    protected ?string $ogtype = 'website';
    protected ?string $publishtime = null;
    protected ?string $shareimage = null;
    protected ?string $ablakcim = null;
    protected ?string $robots = null;
    protected ?string $sitedesc = null;

    // <--- Funkcionális feladatokat ellátó osztály változók ---> //

    // A felhasználó olvasási jogának szintje a modulon
    protected int $olvasas;

    // A felhasználó írási jogának szintje a modulon
    protected int $iras;

    // A POST művelet eredményére adott szöveges üzenet. Hiba/siker/figyelmeztetes/letiltva
    public ?string $eredmeny = null;

    // Ha meg van adva, POST után erre a címre irányít a Controller.
    public ?string $redirtarget = null;

    // Ez választja ki a betöltendő view-t. A $_GET['page'] értéke kerül bele
    public string $selectedpage;

    // Ez választja ki a POST folyamatot lebonyolító metódust
    public ?string $muvelet = null;

    // POST utáni üzeneteket tartalmazó változó
    public ?string $mixintext = null;

    // Az osztály elérhető API metódusai. Ami ebben nem szerepel, az nem hívható meg API-ként. A GET-re és a POST-ra is külön tömb-öt kell megadni!
    public array $apimethods = [];

    // A lapozáshoz (ahol elérhető) használt lapszámok indexei.
    protected array $lapozas = [
        'elozo' => null,
        'kovetkezo' => null
    ];

    // A GET-ből érkező paraméterek. Csak akkor van használatban, ha a View-nak tudnia kell róla. A Routerben kap értéket
    protected array $params = [];

    // Változók, amiket a PHP-ból generálunk, de a JS felületen akarunk használni. Közvetlenül a jsfiles tömb előtt kerül renderelésre.
    protected array $PHPvarsToJS = [];

    // Az osztály által használt JS fájlok elérési útjai. A tömbbe megadott fájlokat a rendszer az oldal legalján tölti be.
    protected array $jsfiles = [];

    // Egyedi CSS fájl, amennyiben egy oldalhoz szükség lenne rá.
    protected ?string $localCSS = null;

    // Az oldalhoz tartozó feltöltések uploads mappán belüli mappája. KIZÁRÓLAG A NÉV! Az 'uploads' hardcode-olva van a feltöltésekhez, abból egyik oldal sem tud kitörni.
    protected ?string $feltoltesgyoker;

    // Az oldal feltöltésgyökerén belüli egyedi mappa. Pl, ha a feltöltésgyökér a "receptek", akkor az egyedi mappa a '2026'. Tartalmazhat slash-t (mondjuk 2026/04), de path traversal ellen akkor is védett.
    protected ?string $egyedimappa;

    // Egy oldal használható metódusai. Ebből tudja a rendszer, hogy egy $_GET-ből érkező tömbelem az ID, vagy valami metódus
    protected array $validpagemethods = [];

    // Egy oldal context menüjének menüpontjai. Pl szerkesztés, törlés, új elem gombok. A formátuma: [szoveg => "Gomb felirata", link => "oldal.nev/menupont"]. Nem a deklaráció során kell megadni, hanem a Router()-ben, elemenként hozzáadni az elérhető gombokat
    public array $contextmenu = [];

    // Az oldal view fájljainak gyökere
    protected string $viewsgyoker = __DIR__ . "/views/";

    // A jelenleg kiválasztott view fájl
    protected ?string $view = null;

    // A jelenleg kiválasztott header fájl
    protected ?string $headerview = null;

    // Ez a változó tárolja el, hogy az oldalon egy adott felhasználó írhat-e.
    protected ?bool $irasjog = null;

    // Az aloldal egy view fájl, amit a fő view fájl elé renderel az oldal. Üzenetek átadására szolgál.
    protected ?string $aloldal = null;

    // Az elérhető view fájlok listája, ahol a kulcs a $_GET['page']-ből várt érték, az érték pedig a tényleges PHP fájl neve
    protected array $views = [];

    // Az oldal fejlécéhez elérhető view fájlok listája
    protected array $headerviews = [];

    // Fájlokat tartalmazó POST esetén ezeket a fájlokat fogadja el a rendszer valid bevitelként. MIME check is történik!
    protected array $mediatypes = [
        'image/jpeg',
        'image/png',
        'image/bmp',
        'image/webp'
    ];

    // Itt kell meghatározni, hogy milyen fájlformátumokat fogad a fájlfeltöltő menü a felhasználói UI-n. Csak kozmeztika, és kézzel kell megadni!
    public string $form_mediatypes = "'image/jpeg', 'image/png', 'image/bmp', 'image/webp'";

    public function __construct(string $type, ?int $olvasas = 0, ?int $iras = 0) {
        if(is_null($olvasas) || is_null($iras))
            throw new Exception("A Page osztály példányosításához meg kell adni az olvasási és írási jog szintjét!");
        $this->title = $GLOBALS['ablakcim'];
        $this->selectedpage = $type;
        $this->canonical = ROOT_PATH . '/' . $type;
        $this->redirtarget = $_SERVER['REQUEST_URI'];
        $this->olvasas = $olvasas;
        $this->iras = $iras;

        if(isset($this->views[$type]))
            $this->view = $this->views[$type];
    }

    public function HtmlHead() : void {
        if (function_exists('HtmlHead')) {
            HtmlHead($this->keywords, $this->canonical, $this->title, $this->ogtype, $this->publishtime, $this->shareimage, $this->robots, $this->ablakcim, $this->sitedesc, $this->cimke);
            $this->HtmlHeadRender();
        }
    }

    protected function HtmlHeadRender() : void {
        include(ROOT_DIR . "/template/assets/htmlheader.php");
    }

    public function LdJSON() : void {
    }

    public function RenderHeader() : void {
        if(!$this->headerview || !file_exists($this->viewsgyoker . $this->headerview))
            return;
        include($this->viewsgyoker . $this->headerview);
    }

    public function Render() : void {
        if(!file_exists($this->viewsgyoker . $this->view)) {
            $GLOBALS['debugmessages'][] = "A kért view fájl (<strong>$this->view</strong>) nem található a megadott elérési útvonalon: " . $this->viewsgyoker . $this->view;
            return;
        }

        foreach ($this->PHPvarsToJS as $key => $value) {
            Settings::$PHPvarsToJS[$key] = $value;
        }
        foreach ($this->jsfiles as $file) {
            Settings::$jsfiles[] = $file;
        }

        if($this->aloldal) {
            if(!file_exists($this->viewsgyoker . $this->views[$this->aloldal])) {
                $GLOBALS['debugmessages'][] = "A kért aloldal view fájl (<strong>$this->views[$this->aloldal]</strong>) nem található a megadott elérési útvonalon: " . $this->viewsgyoker . $this->views[$this->aloldal];
                return;
            }
            include($this->viewsgyoker . $this->views[$this->aloldal]);
        }
        include($this->viewsgyoker . $this->view);
    }

    public function Post() : bool {
        $fname = ucfirst($this->muvelet);
        if(!method_exists($this, $fname)) {
            $GLOBALS['debugmessages'][] = "A kért művelethez (<strong>$fname</strong>) nem található metódus!";
            return false;
        }
        return $this->$fname();
    }

    protected function ParseGet(?array $params) : array {
        if(!$params || count($params) == 0)
            return ['elemid' => null, 'method' => null];

        $keys = array_keys($params);
        switch(count($params)) {
            case 1: // Csak egy paraméter van, az lehet ID, vagy metódus. Ha a metódus a validpagemethods tömbben van, akkor az a metódus, különben ID.
                if(is_numeric($params[$keys[0]]))
                    return ['elemid' => (int)$params[$keys[0]], 'method' => null];
                if(in_array($params[$keys[0]], $this->validpagemethods))
                    return ['method' => $params[$keys[0]], 'elemid' => null];
                return ['elemid' => $params[$keys[0]], 'method' => null];
            case 2: // Két paraméter van, az első az ID a második a metódus.
                if(is_numeric($params[$keys[0]]))
                    return ['elemid' => (int)$params[$keys[0]], 'method' => $params[$keys[1]]];
                return ['elemid' => $params[$keys[0]], 'method' => $params[$keys[1]]];
            default:
                return ['elemid' => null, 'method' => null];
        }
    }

    protected function Lapozo() : void {
        include(ROOT_DIR. "/template/assets/lapozo.php");
    }

    protected function LapozasPrepare(int &$startindex, int &$elemperoldal, array $params) {
        $elemperoldal = 20;
        $startindex = 0;
        if($params['method'] == 'oldal' && isset($params['elemid'])){
            if($params['elemid'] != 1)
                $startindex = ($params['elemid'] - 1) * $elemperoldal + 1;
            $this->lapozas['elozo'] = ($params['elemid'] > 1) ? $params['elemid'] - 1 : null;
            $this->lapozas['kovetkezo'] = $params['elemid'] + 1;
        }
        else {
            $this->lapozas['kovetkezo'] = 2;
        }
    }

    protected function LapozasFinalize(?array $viewtomb, int $elemperoldal) : array {
        if(!$viewtomb) {
            $viewtomb = [];
        }

        if(count($viewtomb) < $elemperoldal) {
            $this->lapozas['kovetkezo'] = null;
        }

        return $viewtomb;
    }

    public function Router(array $params) : Page {
        return $this;
    }

    public function GetIrasjog(?int $elemfelhasznalo = null, ?int $elem_csoportid = null) : bool {
        return $this->GetJog('iras', $elemfelhasznalo, $elem_csoportid);
    }

    public function GetOlvasasjog(?int $elemfelhasznalo = null, ?int $elem_csoportid = null) : bool {
        return $this->GetJog('olvasas', $elemfelhasznalo, $elem_csoportid);
    }

    protected function GetJog(string $tipus, ?int $elemfelhasznalo = null, ?int $elem_csoportid = null) : bool {
        $GLOBALS['debugmessages'][] = "Jog ellenőrzés: $tipus <br>elemfelhasznalo: $elemfelhasznalo<br>elem_csoportid: $elem_csoportid<br>selectedpage: $this->selectedpage<br>felhasznalo: " . FELHASZNALO->uid . "<br>godadmin: " . (FELHASZNALO->godadmin ? 'true' : 'false') . "<br>$tipus: " . Modulkezelo::ModulJogosultsag($this->selectedpage)[$tipus];
        $GLOBALS['debugmessages'][] = print_r(Modulkezelo::ModulJogosultsag($this->selectedpage), true);
        if($tipus == 'iras')
            $csoportids = FELHASZNALO->GetIrhatoCsoportIDs();
        else
            $csoportids = FELHASZNALO->GetCsoportIDs();

        if(FELHASZNALO->godadmin)
            return true;
        if(Modulkezelo::ModulJogosultsag($this->selectedpage)[$tipus] == 3)
            return true;
        if(Modulkezelo::ModulJogosultsag($this->selectedpage)[$tipus] == 2
                && in_array($elem_csoportid, $csoportids))
            return true;
        if(Modulkezelo::ModulJogosultsag($this->selectedpage)[$tipus] >= 1
                && $elemfelhasznalo == FELHASZNALO->uid)
            return true;
        return false;
    }

    public function ValidView() : bool {
        return isset($this->views[$this->selectedpage]);
    }


}