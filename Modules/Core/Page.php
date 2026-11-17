<?php

namespace Infoinventory\Modules\Core;
use Exception;
use Infoinventory\Classes\FormBuilder;
use Infoinventory\Classes\Settings;

abstract Class Page
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

    /**
     * A felhasználó olvasási jogának szintje a modulon. A modul kiválasztása során a rendszer automatikusan beállítja a felhasználó jogosultságai alapján.
     * Leginkább csak kényelmi feladatokat lát el, mert a ModulKezelo-től bármikor lekérhető lenne az információ
     * 
     * Lehetséges értékei: 0: semmi jog, 1: saját tulajdonú elemek, 2: csoporttagságok által engedélyezett elemek, 3: modulon belül bármely elem
     * 
     * @var int $olvasas
     */
    protected readonly int $modulolvasas;

    /**
     * A felhasználó írási jogának szintje a modulon. A modul kiválasztása során a rendszer automatikusan beállítja a felhasználó jogosultságai alapján.
     * Leginkább csak kényelmi feladatokat lát el, mert a ModulKezelo-től bármikor lekérhető lenne az információ
     * 
     * Lehetséges értékei: 0: semmi jog, 1: saját tulajdonú elemek, 2: csoporttagságok által engedélyezett elemek, 3: modulon belül bármely elem
     * 
     * @var int $iras
     */
    protected readonly int $moduliras;

    /** 
     * A POST művelet eredményére adott szöveges üzenet. Hiba/siker/figyelmeztetes/letiltva
     */
    public ?string $eredmeny = null;

    protected array $debugmessages;

    // Ha meg van adva, POST után erre a címre irányít a Controller.
    public ?string $redirtarget = null;

    // Ez választja ki a betöltendő view-t. A $_GET['page'] értéke kerül bele
    public string $selectedpage;

    // Ezt használja a rendszer a lapozás, és egyéb olyan folyamatok során, ahol a rendszer az oldal alap funkcióin túl más paramétert és át akar adni
    public string $urlparams = "";

    // Ez választja ki a POST folyamatot lebonyolító metódust
    public ?string $muvelet = null;

    // POST utáni üzeneteket tartalmazó változó
    public ?string $mixintext = null;

    // Az osztály elérhető API metódusai. Ami ebben nem szerepel, az nem hívható meg API-ként. A GET-re és a POST-ra is külön tömb-öt kell megadni!
    public array $apimethods = [];

    // A lapozáshoz (ahol elérhető) használt lapszámok indexei.
    protected array $lapozas = [
        'elozo' => null,
        'kovetkezo' => null,
        'aktualis' => null,
        'osszlapszam' => null
    ];

    // A kereséshez használt placeholder szöveg.
    protected ?string $keresesplaceholder = "Keresés...";

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

    // Ez a változó tárolhatja el, hogy egy elemet/elemeket az adott felhasználó olvashatja-e.
    protected ?bool $olvasasjog = null;

    // Az aloldal egy view fájl, amit a fő view fájl elé renderel az oldal. Üzenetek átadására szolgál.
    protected ?string $aloldal = null;

    // Az elérhető view fájlok listája, ahol a kulcs a $_GET['page']-ből várt érték, az érték pedig a tényleges PHP fájl neve
    protected array $views = [];

    // A form builder osztály példánya, ha az oldal használja a FormBuilder osztályt
    protected FormBuilder $form;

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
        $this->title = ABLAK_CIM;
        $this->selectedpage = $type;
        $this->canonical = ROOT_PATH . '/' . $type;
        $this->redirtarget = $_SERVER['REQUEST_URI'];
        $GLOBALS['debugmessages'][$type] = [];
        $this->debugmessages = &$GLOBALS['debugmessages'][$type];
        if(FELHASZNALO->godadmin) {
            $this->modulolvasas = $this->moduliras = 3;
        } else {
            $this->modulolvasas = $olvasas;
            $this->moduliras = $iras;
        }

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
            $this->debugmessages[] = "A kért view fájl (<strong>$this->view</strong>) nem található a megadott elérési útvonalon: " . $this->viewsgyoker . $this->view;
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
                $this->debugmessages[] = "A kért aloldal view fájl (<strong>$this->views[$this->aloldal]</strong>) nem található a megadott elérési útvonalon: " . $this->viewsgyoker . $this->views[$this->aloldal];
            }
            else {
                include($this->viewsgyoker . $this->views[$this->aloldal]);
            }
        }
        include($this->viewsgyoker . $this->view);
    }

    public function Post() : bool {
        $fname = ucfirst($this->muvelet);
        if(!method_exists($this, $fname)) {
            $this->mixintext = "A kért művelethez (<strong>$fname</strong>) nem található metódus!";
            return false;
        }
        return $this->$fname();
    }

    protected function ParseGet(?array $params) : array {
        function ParseHelper(string $needle, array &$params, array &$returnarray, mixed $type = 'string') : void {
            $index = array_search($needle, $params);
            if($index === false)
                return;

            if(isset($params[$index + 1])) {
                if($type == "int") {
                    if(is_numeric($params[$index + 1]))
                        $val = (int)$params[$index + 1];
                    else
                        $val = null;
                } else
                    $val = $params[$index + 1];
                $returnarray[$needle] = $val;
                array_splice($params, $index, 2);
            } else {
                $returnarray[$needle] = null;
                array_splice($params, $index, 1);
            }
        }

        $returnarray = ['elemid' => null, 'method' => null];

        if(!$params || count($params) == 0)
            return $returnarray;

        ParseHelper('oldal', $params, $returnarray, 'int');
        ParseHelper('kereses', $params, $returnarray, 'string');

        if(count($params) == 0)
            return $returnarray;

        $keys = array_keys($params);
        switch(count($params)) {
            case 1: // Csak egy paraméter van, az lehet ID, vagy metódus. Ha a metódus a validpagemethods tömbben van, akkor az a metódus, különben ID.
                if(is_numeric($params[$keys[0]]))
                    $returnarray['elemid'] = (int)$params[$keys[0]];
                elseif(!in_array($params[$keys[0]], $this->validpagemethods))
                    $returnarray['elemid'] = $params[$keys[0]];
                else
                    $returnarray['method'] = $params[$keys[0]];
                break;
            case 2: // Két paraméter van, ha az első egy valid metódus, az lesz a metódus, különben az első az ID a második a metódus.
                if(in_array($params[$keys[0]], $this->validpagemethods)) {
                    $returnarray['method'] = $params[$keys[0]];
                    $returnarray['elemid'] = $params[$keys[1]];
                }
                else {
                    if(is_numeric($params[$keys[0]]))
                        $returnarray['elemid'] = (int)$params[$keys[0]];
                    else
                        $returnarray['elemid'] = $params[$keys[0]];
                    $returnarray['method'] = $params[$keys[1]];
                }
                break;
        }
        return $returnarray;
    }

    protected function Lapozo() : void {
        include(ROOT_DIR. "/template/assets/lapozo.php");
    }

    protected function SetKereses(array $params) : ?string {
        $this->jsfiles[] = 'template/assets/js/kereses.js';
        $this->PHPvarsToJS['selectedPage'] = $this->selectedpage;
        $kereses = $params['kereses'] ?? null;
        if($kereses) {
            $this->urlparams = $this->selectedpage . "/kereses/$kereses";
            $this->keresesplaceholder = $kereses;
        } else {
            $this->urlparams = $this->selectedpage;
        }
        return $kereses;
    }

    protected function Kereso() : void {
        include(ROOT_DIR. "/template/assets/kereso.php");
    }

    /** 
     * 
     * Beállítja a lapozáshoz szükséges változók ($this->lapozas['elozo'] és $this->lapozas['kovetkezo']) értékét,
     * valamint módosítja a referenciaként megkapott változók ($startindex és $elemperoldal) értékét is a pillanatnyi lap alapján.
     * 
     * Az elemperoldal értéke a függvény futásának vége előtt +1-el módosításra kerül, erre az utolsó lap érzékeléséhez van szükség
     * 
     * @param int $startindex Az elsőként lekérni kívánt elem indexszáma
     * @param int $elemperoldal Az egy lapon megjeleníteni kívánt elemek száma
     * @param array $params A ParseGet függvény által feldolgozott értékek tömbje
     * @return void
     * 
     */
    protected function SetPagingVars(int &$startindex, int &$elemperoldal, array $params) : void {
        if(isset($params['oldal'])) {
            $this->lapozas['aktualis'] = $params['oldal'];
            if($params['oldal'] != 1)
                $startindex = ($params['oldal'] - 1) * $elemperoldal;
            $this->lapozas['elozo'] = ($params['oldal'] > 1) ? $params['oldal'] - 1 : null;
            $this->lapozas['kovetkezo'] = $params['oldal'] + 1;
        }
        else {
            $this->lapozas['kovetkezo'] = 2;
        }
        $elemperoldal++;
        $this->debugmessages[] = "SetPagingVars: startindex=$startindex, elemperoldal=$elemperoldal, params=" . print_r($params, true);
    }

    /**
     * Primitív függvény ami megállapítja, hogy a pillanatnyi oldal az utolsó-e.
     * A SetPagingVars úgy állítja az értékeket, hogy mindig 1-gyel több elemet kérdezzen le a rendszer az adatbázisból, mint amennyit a felhasználó akart.
     * Így tehát ha egy olyan oldalra jutunk, ahol pontosan annyi elem van, amennyit a felhasználó kért, akkor tudjuk, hogy az utolsó oldalon járunk.
     * Hogy megjelenítve pontosan annyi elem legyen, amennyit a felhasználó kért, ha van következő oldal, az utolsó elem törlésre kerül a tömbből
     * 
     * @param array $pagecontents Az adatbázis lekrédezés elemeit tartalmazó tömb
     * @param int $elemperoldal Az adatbázis lekérdezés során használt LIMIT darabszám értéke
     * @return void
     * 
     */
    protected function DetectLastPage(array &$pagecontents, int $elemperoldal) : void {
        if(count($pagecontents) < $elemperoldal) {
            $this->lapozas['kovetkezo'] = null;
        } else {
            array_pop($pagecontents);
        }
    }

    /**
     * Függvény, ami megállapítja, hogy a pillanatnyilag beállított értékek alapján hány oldal van összesen
     * @param int $osszrekodszam A megadott feltételek melletti összes rekord száma az adatbázisban
     * @param int $elemperoldal Az egy lapon megjeleníteni kívánt elemek száma
     * @return void
     */
    public function SetOsszlapszam(int $osszrekodszam, int $elemperoldal) : void {
        $this->lapozas['osszlapszam'] = (int)ceil($osszrekodszam / ($elemperoldal - 1));
        $this->debugmessages[] = "SetOsszlapszam: osszlapszam=" . $this->lapozas['osszlapszam'] . ", osszrekordszam=$osszrekodszam, elemperoldal=$elemperoldal";
    }

    /**
     * Függvény, ami kiválasztja a megfelelő aloldalt, a hozzá tartozó jogosultságokat, view fájlt, és megfelelő adatbázis lekérdezést.
     * @param array $params A $_GET tömb nyers adatait tartalmazó tömb. A ParseGet függvénnyel lehet feldolgozni.
     */
    public abstract function Router(array $params) : Page;

    /**
     * Az osztály által generált form(ok)-at beállító metódus. A leszármazott osztályokban implementálni KELL, ha az osztály generált formot akar használni.
     * @param mixed $elem Bármilyen objektum, tömb, vagy primitív érték, amit az osztály szerkesztésnél használ
     */
    protected function Form(mixed $elem = null) : FormBuilder {
        return new FormBuilder();
    }

    /**
     * Az osztály lista megjelenítéshez használt tömbjét előállító metódus. A HASZNÁLATHOZ A LESZÁRMAZOTT OSZTÁLYBAN IMPLEMENTÁLNI KELL!!!
     * Előkészíti a nyers adatokat, meghívja az adatbázis kapcsolat metódusát, és az adatbázistól kapott eredmények alapján előállítja a feldolgozott tömböt.
     * @param   int       $startindex     A lekérdezés során kért 0. elem indexszáma. NEM a PRIMARY_KEY-e
     * @param   int       $elemperoldal   A lekérdezés során kért elemek száma. Az alap elgondolás, hogy valószínűleg sosem akarjuk majd az összes elemet egyszerre megjeleníteni egy listaoldalon.
     * @param   ?string    $kereses        A keresőmezőből vett string értéke. Ha üres, vagy null, akkor nem történik keresés
     * @return  array A feldolgozott elemek tömbje, vagy üres tömb
     */
    protected function GetList(int $startindex, int $elemperoldal, ?string $kereses) : array {
        return [];
    }

    public function GetIrasjog(?int $elemfelhasznalo = null, ?int $elem_csoportid = null) : bool {
        return $this->GetJog('moduliras', $elemfelhasznalo, $elem_csoportid);
    }

    public function GetOlvasasjog(?int $elemfelhasznalo = null, ?int $elem_csoportid = null) : bool {
        return $this->GetJog('modulolvasas', $elemfelhasznalo, $elem_csoportid);
    }

    protected function GetJog(string $tipus, ?int $elemfelhasznalo, ?int $elem_csoportid) : bool {
        $moduljog = $this->$tipus;
        $this->debugmessages[] = "Jog ellenőrzés: $tipus <br>elemfelhasznalo: $elemfelhasznalo<br>elem_csoportid: $elem_csoportid<br>selectedpage: $this->selectedpage<br>felhasznalo: " . FELHASZNALO->uid . "<br>godadmin: " . (FELHASZNALO->godadmin ? 'true' : 'false') . "<br>$tipus: " . FELHASZNALO->moduljogosultsagok[$this->selectedpage][$tipus];
        $this->debugmessages[] = print_r($moduljog, true);

        // Ha a felhasználó godadmin, akkor mindenhez van joga
        if(FELHASZNALO->godadmin)
            return true;

        // Ha a modulhoz a felhasználónak 3-as szintű joga van, akkor a modulban bármit írhat/olvashat, függően attól, hogy a $tipus 'iras' vagy 'olvasas'
        if($moduljog == 3)
            return true;

        if($tipus == 'iras')
            $csoportids = FELHASZNALO->GetIrhatoCsoportIDs();
        else
            $csoportids = FELHASZNALO->GetCsoportIDs();

        // Ha a modulhoz a felhasználónak 2-es szintű joga van, akkor két eshetőség van
        if($moduljog == 2) {
            // Ha nincs elem megadva, akkor azt ellenőrizzük, hogy a felhasználó létrehozhat-e új elemet.
            if(is_null($elemfelhasznalo) && is_null($elem_csoportid))
                return true;

            // Ha van elem megadva, akkor azt ellenőrizzük, hogy a részére engedélyezett csoportok egyike ad-e jogot hozzá.
            if(in_array($elem_csoportid, $csoportids))
                return true;
        }

        // Ha a modulhoz a felhasználónak 1-es szintű joga van, akkor csak a saját elemeihez van joga.
        if($moduljog >= 1 && $elemfelhasznalo == FELHASZNALO->uid)
            return true;

        return false;
    }

    public function ValidView() : bool {
        return isset($this->views[$this->selectedpage]);
    }

    protected function SetSelectedUj() : Page {
        if(!$this->GetIrasjog()) {
            return new SinglePage(403, "Nincs jogosultságod új elem létrehozásához!");
        }

        $this->irasjog = true;
        $this->muvelet = 'uj';
        $this->view = $this->views['uj'];
        $this->form = $this->Form();

        return $this;
    }

    protected function SetSelectedSzerkeszt(mixed $elem, string $elemnev = 'elem') : Page {
        if(!$this->irasjog) {
            return new SinglePage(403, "A kiválasztott $elemnev szerkesztéséhez nincs jogosultságod!");
        }

        $this->muvelet = 'szerkeszt';
        $this->view = $this->views['szerkeszt'];
        $this->form = $this->Form($elem);

        return $this;
    }

    protected function SetSelectedLista(array $params, array &$returnarray) : Page {
        $kereses = $this->SetKereses($params);
        $this->irasjog = $this->GetIrasjog();
        $this->view = $this->views[$this->selectedpage];
        $startindex = 0;
        $elemperoldal = 20;
        $this->SetPagingVars($startindex, $elemperoldal, $params);
        $returnarray = $this->GetList($startindex, $elemperoldal, $kereses);
        $this->DetectLastPage($returnarray, $elemperoldal);

        return $this;
    }
}