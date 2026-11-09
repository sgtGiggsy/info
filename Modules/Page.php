<?php

namespace Infoinventory\Modules;
use Exception;
use Infoinventory\Classes\Settings;

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

    // Az oldal view fájljainak gyökere
    protected string $viewsgyoker = ROOT_DIR . "/views/";

    // A jelenleg kiválasztott view fájl
    protected ?string $view = null;

    // A jelenleg kiválasztott header fájl
    protected ?string $headerview = null;

    // Ez a változó tárolja el, hogy az oldalon egy adott felhasználó írhat-e.
    protected bool $irasjog = false;

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

    public function __construct(string $type) {
        $this->title = $GLOBALS['ablakcim'];
        $this->selectedpage = $type;
        $this->canonical = ROOT_PATH . '/' . $type;
        $this->redirtarget = $_SERVER['REQUEST_URI'];

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
        //echo $this->viewsgyoker . $this->view;
        if(!file_exists($this->viewsgyoker . $this->view))
            return;

        foreach ($this->PHPvarsToJS as $key => $value) {
            Settings::$PHPvarsToJS[$key] = $value;
        }
        foreach ($this->jsfiles as $file) {
            Settings::$jsfiles[] = $file;
        }

        if($this->aloldal) {
            if(!file_exists($this->viewsgyoker . $this->views[$this->aloldal]))
                return;
            include($this->viewsgyoker . $this->views[$this->aloldal]);
        }
        include($this->viewsgyoker . $this->view);
    }

    public function Post() : bool {
        $fname = ucfirst($this->muvelet);
        if(!method_exists($this, $fname))
            return false;
        return $this->$fname();
    }

    protected function ParseGet(?array $params) : array {
        if(!$params || count($params) == 0)
            return ['elemid' => null, 'method' => null];

        $keys = array_keys($params);
        switch(count($params)) {
            case 1:
                if(is_numeric($params[$keys[0]]))
                    return ['elemid' => (int)$params[$keys[0]], 'method' => null];
                if(in_array($params[$keys[0]], $this->validpagemethods))
                    return ['method' => $params[$keys[0]], 'elemid' => null];
                return ['elemid' => $params[$keys[0]], 'method' => null];
            case 2:
                if(is_numeric($params[$keys[1]]))
                    return ['elemid' => (int)$params[$keys[1]], 'method' => $params[$keys[0]]];
                return ['elemid' => $params[$keys[1]], 'method' => $params[$keys[0]]];
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

    public function GetIrasjog(int|string $elem_id) : bool {
        if(Settings::$admin)
            return true;
        return false;
    }

    public function GetOlvasasjog(int|string $elem_id) : bool {
        if(Settings::$admin)
            return true;
        return false;
    }

    public function ValidView() : bool {
        return isset($this->views[$this->selectedpage]);
    }
}