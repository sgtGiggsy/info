<?php

namespace Infoinventory\Modules\Core;
use Exception;
use Infoinventory\Classes\FormBuilder;
use Infoinventory\Classes\Settings;

/**
 * Alap absztrakt oldalvezérlő osztály, amely közös keretet biztosít a modulokhoz tartozó oldalak működéséhez.
 *
 * Feladata az oldalhoz kapcsolódó metaadatok, jogosultsági állapotok, POST feldolgozási információk,
 * lapozási adatok, nézetválasztás, kliensoldali erőforrások és feltöltési beállítások egységes kezelése.
 *
 * Az osztály leszármazottai határozzák meg az adott oldal konkrét útvonalkezelését, megjelenítési logikáját,
 * elérhető nézeteit, API műveleteit és az oldalhoz tartozó üzleti folyamatokat.
 */
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

    // <--- Jogosultságok ---> //

    /**
     * Ez a változó tárolja el, hogy az oldalon egy adott felhasználó írhat-e.
     * Csak kényelmi célokat szolgál, a Router-ben kerül beállításra.
     * @var bool|null $irasjog
     */
    protected ?bool $irasjog = null;

    /**
     * Ez a változó tárolhatja el, hogy egy elemet/elemeket az adott felhasználó olvashatja-e.
     * @var bool|null $olvasasjog
     */
    protected ?bool $olvasasjog = null;
    /**
     * A felhasználó olvasási jogának szintje a modulon. A modul kiválasztása során a rendszer automatikusan beállítja a felhasználó jogosultságai alapján.
     * Leginkább csak kényelmi feladatokat lát el, mert a ModulKezelo-től bármikor lekérhető lenne az információ
     *
     * Lehetséges értékei: 0: semmi jog, 1: saját tulajdonú elemek, 2: csoporttagságok által engedélyezett elemek, 3: modulon belül bármely elem
     *
     * @var int $modulolvasas
     */
    protected readonly int $modulolvasas;

    /**
     * A felhasználó írási jogának szintje a modulon. A modul kiválasztása során a rendszer automatikusan beállítja a felhasználó jogosultságai alapján.
     * Leginkább csak kényelmi feladatokat lát el, mert a ModulKezelo-től bármikor lekérhető lenne az információ
     *
     * Lehetséges értékei: 0: semmi jog, 1: saját tulajdonú elemek, 2: csoporttagságok által engedélyezett elemek, 3: modulon belül bármely elem
     *
     * @var int $moduliras
     */
    protected readonly int $moduliras;

    // <--- Funkcionális feladatokat ellátó osztály változók ---> //

    /**
     * A POST művelet eredményére adott szöveges üzenet. Hiba/siker/figyelmeztetes/letiltva
     */
    public ?string $eredmeny = null;

    /**
     * Ez határozza meg, hogy POST művelet után pontosan milyen címre történjen az átirányítás.
     * Ha null, akkor a rendszer a $_SERVER['REQUEST_URI']-t használja átirányítási célként.
     * @var string|mixed|null $redirtarget
     */
    public ?string $redirtarget = null;

    /**
     * Ez választja ki a betöltendő view-t. A $_GET['page'] értéke kerül bele.
     */
    public string $selectedview;

    /**
     * A $debugmessages globális tömb egyszerűbb eléréshez használt helyi változó. A konstruktor a globális tömb pointerére irányítja
     * @var array $debugmessages
     */
    protected array $debugmessages;

    /**
     * Ezt használja a rendszer a lapozás, és egyéb olyan folyamatok során, ahol a rendszer a $_GET['page']-en túl más paramétert is $_GET-en keresztül akar továbbítani.
     * A Router-ben kell neki értéket adni, és a rendszer a $urlparams változó alapján generálja a linkeket.
     * Alapértelmezetten a $selectedview értékét veszi fel
     * @var string $urlparams
     */
    public string $urlparams;

    /**
     * Ez választja ki a POST folyamatot lebonyolító metódust. A Router-ben kell neki értéket adni.
     * Alapból false-ra inicializálódik. Ha volt POST kérés, a Controller true-ra állítja, és a Router onnan tudja, hogy POST kérést kapott-e.
     * @var bool|string $post_method
     */
    public bool|string $post_method = false;

    /**
     * POST utáni felugró mixin üzeneteket tartalmazó változó.
     * Ha nincs megadva, akkor a modul, és a folyamat sikeessége alapján a Controller maga generál helyette üzenetet.
     * @var string|null $mixintext
     */
    public ?string $mixintext = null;

    /**
     * A kereséshez használt placeholder szöveg.
     *
     * @var string|null $keresesplaceholder
     */
    protected ?string $keresesplaceholder = "Keresés...";

    /**
     * Az osztály elérhető API metódusai.
     * Ami ebben nem szerepel, az nem hívható meg API-ként. A GET-re és a POST-ra is külön tömb-öt kell megadni!
     *
     * @var array $apimethods
     */
    public array $apimethods = [];

    /**
     * A lapozáshoz (ahol elérhető) használt lapszámok indexei.
     *
     * Eltárolja az előző és következő lapok indexét, valamint az aktuális lapot, és az összes lap számát
     * @var array $lapozas
     */
    protected array $lapozas = [
        'elozo' => null,
        'kovetkezo' => null,
        'aktualis' => null,
        'osszlapszam' => null
    ];

    /**
     * @var int $elemperoldal A lapozás során egy oldalon megjelenő elemek száma.
     */
    protected int $elemperoldal = 20;

    /**
     * Egy oldal context menüjének menüpontjai. Pl szerkesztés, törlés, új elem gombok.
     * A formátuma: ['felirat' => "Gomb felirata", 'link' => "oldal.nev/menupont"].
     * Nem a deklaráció során kell megadni, hanem a SetRender()-ben, elemenként hozzáadni az elérhető gombokat
     * @var array $contextmenu
     */
    public array $contextmenu = [];

    // <--- Viewkkal kapcsolatos osztály változók ---> //

    /**
     * Az oldal view fájljainak gyökere
     * @var string $viewsgyoker
     */
    protected string $viewsgyoker;

    /**
     * Az elérhető view fájlok listája, ahol a kulcs a $_GET['page']-ből várt érték, az érték pedig a tényleges PHP fájl neve
     * @var array $views
     */
    protected array $views = [];

    /**
     * Az oldal törzsét adó kiválasztott view fájl neve.
     * A SetRender()-ben kell beállítani az értékét, de csak akkor szükséges, ha a $_GET['page'] értékétől eltérő nevű view fájlt akarunk használni.
     * @var string|null $view
     */
    protected ?string $view = null;

    /**
     * Az oldal fejlécéhez elérhető view fájlok listája
     * A Router az ebben felsorolt elemek alapján dönti el (ahol használatban van), hogy melyik headerview fájl legyen kiválasztva
     * @var array $headerviews
     */
    protected array $headerviews = [];

    /**
     * A jelenleg kiválasztott header fájl
     * Ezzel lehet oldalanként, vagy elemenként külön header-t használni
     * Ha nincs beállítva, úgy az oldal nem kap fejlécet
     * @var string|null $headerview
     */
    protected ?string $headerview = null;

    /**
     * Üzenetek átadására szolgáló view fájl elérési útja. A mixin üzenetek statikus kiegészítésére használható.
     * A fő view fájl elé renderel az oldal.
     * @var string|null $uzenet
     */
    protected ?string $uzenet = null;

    /**
     * Azon változók tömbje, amiket a PHP-ból generálunk, de a JS felületen akarunk használni.
     * Közvetlenül a jsfiles tömb előtt kerül renderelésre.
     * @var array $PHPvarsToJS
     */
    protected array $PHPvarsToJS = [];

    /**
     * Az osztály által használt JS fájlok elérési útjai. A tömbbe megadott fájlokat a rendszer az oldal legalján tölti be.
     * @var array $jsfiles
     */
    protected array $jsfiles = [];

    /**
     * Egyedi CSS fájl, amennyiben egy oldalhoz szükség lenne rá.
     * @var string|null $localCSS
     */
    protected ?string $localCSS = null;

    // <--- Feltöltésekkel kapcsolatos osztály változók ---> //

    /**
     * Az oldalhoz tartozó feltöltések uploads mappán belüli mappája.
     * KIZÁRÓLAG A NÉV! Az 'uploads' hardcode-olva van a feltöltésekhez, az UploadHandler osztály by design csak oda hajlandó fájlokat tölteni.
     * @var string|null $feltoltesgyoker
     */
    protected ?string $feltoltesgyoker;

    /**
     * Az oldal feltöltésgyökerén belüli egyedi mappa.
     * Pl, ha a feltöltésgyökér a "receptek", akkor az egyedi mappa a '2026'. Tartalmazhat slash-t (mondjuk 2026/04), de path traversal ellen akkor is védett.
     * @var string|null $egyedimappa
     */
    protected ?string $egyedimappa;

    /**
     * Fájlokat tartalmazó POST esetén ezeket a fájlokat fogadja el a rendszer valid bevitelként.
     * MIME check is történik! Az a típus, ami nincs fent ezen a listán, a feltöltési folyamat során eldobásra kerül
     * @var array $mediatypes
     */
    protected array $mediatypes = [
        'image/jpeg',
        'image/png',
        'image/bmp',
        'image/webp'
    ];

    /**
     * A felhasználói UI-n megjelenő elfogadott feltölthető fájlok formátumának string formátumú listája
     * Csak UX célokat szolgál, a feltöltött fájlok ellenőrzésében nem vesz részt
     * @var string $form_mediatypes
     */
    public string $form_mediatypes = "'image/jpeg', 'image/png', 'image/bmp', 'image/webp'";

    // <--- $_GET Parsing ---> //

    /**
     * A GET-ből érkező paraméterek tömbje.
     * A konstruktorból meghívott ParseGet függvény tölti fel tartalommal.
     * @var array $params
     */
    protected array $params = [];

    /**
     * Egy oldal használható metódusai.
     * Ebből tudja a ParseGet, hogy egy $_GET-ből érkező tömbelem az metódus-e.
     * A rendszer nem próbál olyan paramétert metódusként használni, ami itt nincs megadva.
     * @var array $validpagemethods
     */
    protected array $validpagemethods = [];

    /**
     * Egy oldal aloldalai.
     * Ebből tudja a ParseGet, hogy egy $_GET-ből érkező tömbelem az aloldal-e.
     * A rendszer nem talál meg olyan aloldalt, ami itt nincs megadva.
     * @var array $aloldalak
     */
    protected array $aloldalak = [];

    // <--- FORM ---> //

    /**
     * A form builder osztály példánya, ha az oldal használja a FormBuilder osztályt
     * @var FormBuilder $form
     */
    protected FormBuilder $form;

    /**
     * A másodlagos form változója. Amennyiben van, ebbe kerül bele egy második form, amit az első formmal együtt akarunk megjeleníteni
     * @var FormBuilder $form2
     */
    protected Formbuilder $form2;

    // <--- CORE METÓDUSOK ---> //

    public function __construct(string $page, ?int $olvasas = null, ?int $iras = null) {
        $this->title = ABLAK_CIM;
        $this->selectedview = $page;
        $this->urlparams = $page;
        $this->canonical = ROOT_PATH . '/' . $page;
        $this->redirtarget = $_SERVER['REQUEST_URI'];
        $GLOBALS['debugmessages'][$page] = [];
        $this->debugmessages = &$GLOBALS['debugmessages'][$page];
        $this->params = $this->ParseGet();
        $this->viewsgyoker = ROOT_DIR . "/Modules/". ucfirst(MODULOK->oldalak[$page]['kezelomodul']) . "/views/";
        if(FELHASZNALO->godadmin) {
            $this->modulolvasas = $this->moduliras = 3;
        } else {
            $this->modulolvasas = $olvasas;
            $this->moduliras = $iras;
        }

        if(isset($this->views[$page]))
            $this->view = $this->views[$page];
    }

    /**
     * A modulon belüli útválasztásért felelős metódus.
     *
     * A Router() választja ki az aloldalt, metódust, betölteni kívánt elemet, valamint elvégzi a lekérdezést és a jogosultságok ellenőrzését.
     * Adatbázis lekérdezés KIZÁRÓLAG a Router()-ben történhet.
     * Az írási és olvasási jogosultságok ellenőrzése KIZÁRÓLAG ITT TÖRTÉNHET!!!
     *
     * Kötelező felülírni!
     */
    public abstract function Router() : Page;

    /**
     * API hívások esetén a modulon belüli útválasztásért felelős metódus.
     *
     * Adatbázis lekérdezés KIZÁRÓLAG a Router()-ben történhet.
     * Az írási és olvasási jogosultságok ellenőrzése KIZÁRÓLAG ITT TÖRTÉNHET!!!
     *
     * Kötelező felülírni!
     */
    public function APIRouter() : Page { return $this; }

    /**
     * A Post művelet kiválasztására szolgáló osztály.
     *
     * A Router állítja be, hogy a GET alapján milyen művelet probál POST-olni az oldalon.
     * A POST működéséhez a routerben kiválasztott $this->post_method értékével azonos nevü metódusnak LÉTEZNIE KELL az osztályban.
     */
    public function Post() : bool {
        $fname = ucfirst($this->post_method);
        if(!method_exists($this, $fname)) {
            $this->mixintext = "A kért művelethez (<strong>$fname</strong>) nem található metódus!";
            return false;
        }
        return $this->$fname();
    }

    /**
     * A $_GET-ből vett értékeket feldolgozó metódus
     *
     * A $_GET tömb első eleme MINDIG a kiválasztott modul, így itt a második elemtől kezdődik csak a feldolgozás
     * A metódus előbb feldolgoz minden felismerhető elemet a tömbből, majd ha marad még benne elem, akkor a 0-at kiválasztja, mint 'elemid'
     *
     * @param array|null $params A $_GET 1-es indexű elemétől kezdődő tömbje
     * @return null[] Feldolgozott $paramétereket tartalmazó tömb
     */
    protected function ParseGet() : array {
         $parsElem = static function (string $needle, array &$params, array &$returnarray, mixed $type = 'string') : void {
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
        };

        $parseTomb = static function (array $haystack, array &$params, array &$returnarray, string $getelem) : void {
            foreach($params as $param) {
                if(in_array($param, $haystack)) {
                    $returnarray[$getelem] = $param;
                    array_splice($params, array_search($param, $params), 1);
                    break;
                }
            }
        };

        $params = $_GET['params'] ?? '';
        $params = $params ? explode('/', $params) : [];

        $returnarray = ['elemid' => null, 'aloldal' => null, 'method' => null, 'oldal' => null, 'kereses' => null];

        if(!$params || count($params) == 0)
            return $returnarray;

        $parsElem('oldal', $params, $returnarray, 'int');
        $parsElem('kereses', $params, $returnarray, 'string');
        $parseTomb($this->validpagemethods, $params, $returnarray, 'method');
        $parseTomb($this->aloldalak, $params, $returnarray, 'aloldal');

        if(count($params) > 0)
            $returnarray['elemid'] = $params[0];

        return $returnarray;
    }

    /**
     * Függvény annak megállapítására, hogy egy kiválasztott view fájl létezik-e a $this->views tömbben
     *
     * @return bool
     */
    public function ValidView() : bool {
        if(isset($this->views[$this->selectedview]))
            return true;

        $this->debugmessages['View hiba'] = "A kiválasztott view fájl: $this->selectedview nem szerepel a this->views tömb elemei között!";
        return false;
    }


    /**
     * Új elem létrehozására szolgáló oldalt kiválasztó, és alapbeállításait elvégző függvény
     *
     * Csak a boilerplate-et végzi el.
     *
     * @param string $elemnev
     * @param string $post_method
     * @return Page|$this
     */
    protected function SetRouteUj(string $elemnev = 'elem', string $post_method = 'Uj') : Page {
        $this->irasjog = $this->GetIrasjog();
        if(!$this->irasjog)
            return new SinglePage(403, "Nincs jogosultságod új $elemnev létrehozásához!");
        $this->post_method = $post_method;
        return $this;
    }

    // <--- JOGOOSULTSÁGELLENŐRZÉST VÉGZŐ METÓDUSOK ---> //

    /**
     * A felhasználó írási jogát ellenőrző metódus.
     *
     * A metódus a felhasználó modulhoz tartozó írási jogszintjét valamint csoporttagságait
     * és az elem tulajdonosát valamint csoportját vizsgálja,
     * és ennek alapján dönti el, hogy a felhasználó írhatja-e az elemet.
     *
     * A vizsgálat adatbázis művelettel nem jár, kizárólag a már elérhető információk alapján hoz döntést.
     *
     * @param int|null $elemfelhasznalo A vizsgált elem tulajdonosának felhasználói azonosítója
     * @param int|null $elem_csoportid A vizsgált elemhez tartozó csoport azonosítója
     * @return bool A felhasználó írási jogát jelző logikai érték
     */
    public function GetIrasjog(?int $elemfelhasznalo = null, ?int $elem_csoportid = null) : bool {
        return $this->GetJog('moduliras', $elemfelhasznalo, $elem_csoportid);
    }

    /**
     * A felhasználó olvasási jogát ellenőrző metódus.
     *
     *  A metódus a felhasználó modulhoz tartozó olvasási jogszintjét valamint csoporttagságait
     *  és az elem tulajdonosát valamint csoportját vizsgálja,
     *  és ennek alapján dönti el, hogy a felhasználó olvashatja-e az elemet.
     *
     *  A vizsgálat adatbázis művelettel nem jár, kizárólag a már elérhető információk alapján hoz döntést.
     *
     * @param int|null $elemfelhasznalo A vizsgált elem tulajdonosának felhasználói azonosítója
     * @param int|null $elem_csoportid A vizsgált elemhez tartozó csoport azonosítója
     * @return bool A felhasználó olvasási jogát jelző logikai érték
     */
    public function GetOlvasasjog(?int $elemfelhasznalo = null, ?int $elem_csoportid = null) : bool {
        return $this->GetJog('modulolvasas', $elemfelhasznalo, $elem_csoportid);
    }

    private function GetJog(string $jogtipus, ?int $elemfelhasznalo, ?int $elem_csoportid) : bool {
        $moduljog = $this->$jogtipus;
        $debugmsg = [
            'Az ellenőrzés típusa' => $jogtipus,
            'A jelen felhasználó ID-je' => FELHASZNALO->uid,
            'A jelen felhasználó GodAdmin' => (FELHASZNALO->godadmin) ? 'igen' : 'nem',
            'A modul szerint felhasználó szintje' => $moduljog,
            'A felhasználó szerint a felhasználó szintje' => FELHASZNALO->moduljogosultsagok[$this->selectedview][$jogtipus],
            'A felhasználó jognak megfelelő csoportjai' => [],
            'Az elem tulajdonosának ID-je' => $elemfelhasznalo,
            'Az elem csoportjának ID-je' => $elem_csoportid,
            'Kiválasztott oldal' => $this->selectedview
        ];

        if($moduljog != FELHASZNALO->moduljogosultsagok[$this->selectedview][$jogtipus]) {
            $debugmsg['Figyelmeztetés'] = "A modulhoz tartozó jogszint ($moduljog) nem egyezik a felhasználó jogosultságával (" . FELHASZNALO->moduljogosultsagok[$this->selectedview][$jogtipus] . ")";
            $debugmsg['Eredmény'] = "Az anomália miatt a hozzáférés megtagadva!";
            $this->debugmessages['Jogellenőrzés'] = $debugmsg;
            return false;
        }

        // Ha a felhasználó godadmin, akkor mindenhez van joga
        if(FELHASZNALO->godadmin) {
            $debugmsg['Eredmény'] = "A felhasználó godadmin, így mindenhez van joga.";
            $this->debugmessages['Jogellenőrzés-' . $jogtipus] = $debugmsg;
            return true;
        }

        // Ha a modulhoz a felhasználónak 3-as szintű joga van, akkor a modulban bármit írhat/olvashat, függően attól, hogy a $tipus 'iras' vagy 'olvasas'
        if($moduljog == 3) {
            $debugmsg['Eredmény'] = "A felhasználó a modulhoz 3-as szintű joggal rendelkezik, így mindenhez van joga.";
            $this->debugmessages['Jogellenőrzés-' . $jogtipus] = $debugmsg;
            return true;
        }

        if($jogtipus == 'moduliras')
            $csoportids = FELHASZNALO->GetIrhatoCsoportIDs();
        else
            $csoportids = FELHASZNALO->GetCsoportIDs();
        $debugmsg['A felhasználó jognak megfelelő csoportjai'] = implode(', ', $csoportids);

        // Ha a modulhoz a felhasználónak 2-es szintű joga van, akkor két eshetőség van
        if($moduljog == 2) {
            // Ha nincs elem megadva, akkor azt ellenőrizzük, hogy a felhasználó létrehozhat-e új elemet.
            if(is_null($elemfelhasznalo) && is_null($elem_csoportid)) {
                $debugmsg['Eredmény'] = "A felhasználó a modulhoz 2-es szintű joggal rendelkezik, így új elemet hozhat létre.";
                $this->debugmessages['Jogellenőrzés-' . $jogtipus] = $debugmsg;
                return true;
            }

            // Ha van elem megadva, akkor azt ellenőrizzük, hogy a részére engedélyezett csoportok egyike ad-e jogot hozzá.
            if(!is_null($elem_csoportid) && in_array($elem_csoportid, $csoportids)) {
                $debugmsg['Eredmény'] = "A felhasználó a modulhoz 2-es szintű joggal rendelkezik, és az elem csoportja az engedélyezett csoportjai között van, így hozzáférhet az elemhez.";
                $this->debugmessages['Jogellenőrzés-' . $jogtipus] = $debugmsg;
                return true;
            }
        }

        // Ha a modulhoz a felhasználónak 1-es szintű joga van, akkor csak a saját elemeihez van joga.
        if(($moduljog == 1 || $moduljog == 2) && $elemfelhasznalo == FELHASZNALO->uid) {
            $debugmsg['Eredmény'] = "A felhasználó az elem tulajdonosa, így hozzáférhet az elemhez.";
            $this->debugmessages['Jogellenőrzés-' . $jogtipus] = $debugmsg;
            return true;
        }

        $debugmsg['Eredmény'] = "A felhasználónak nincs joga az elemhez.";
        $this->debugmessages['Jogellenőrzés-' . $jogtipus] = $debugmsg;
        return false;
    }

    // <--- RENDERELÉST ELŐKÉSZÍTŐ METÓDUSOK ---> //

    /**
     * Az oldalak renderelése során használt elmeinek előkészítéséért felelős metódus.
     * Az írási és olvasási jogok ellenőrzésének már a Router-ben meg kell történnie, ide már csak olyan felhasználó juthat, akinek a kiválasztott erőforráshoz engedélye van.
     * A jogok újboli ellenőrzére így itt már nincs szükség, a Router()-ben beállított jogosultságokat kell használni a megjelenő elemek kiválasztásához.
     * !!! JOGOSULTSÁGI HIBA MIATTI EXCEPTIONT ITT MÁR NEM SZABAD DOBNI !!!
     * HA EGY FELHASZNÁLÓ NEM RENDELKEZIK VALAMI SZÜKSÉGES JOGGAL, IDE NEM JUTHAT EL!!!
     * @return void
     */
    public abstract function SetRender() :void;

    /**
     * Az osztály által generált form(ok)-at beállító metódus.
     *
     * A leszármazott osztályokban implementálni KELL, ha az osztály generált formot akar használni.
     *
     * @param mixed $elem Bármilyen objektum, tömb, vagy primitív érték, amit az osztály szerkesztésnél használ
     */
    protected function Form(mixed $elem = null) : FormBuilder { return new FormBuilder(); }

    /**
     * Az osztály lista megjelenítéshez használt tömbjét előállító metódus.
     *
     * A HASZNÁLATHOZ A LESZÁRMAZOTT OSZTÁLYBAN IMPLEMENTÁLNI KELL!!!
     * Előkészíti a nyers adatokat, meghívja az adatbázis kapcsolat metódusát, és az adatbázistól kapott eredmények alapján előállítja a feldolgozott tömböt.
     *
     * @param   int       $startindex     A lekérdezés során kért 0. elem indexszáma. NEM a PRIMARY_KEY-e
     * @param   ?string    $kereses        A keresőmezőből vett string értéke. Ha üres, vagy null, akkor nem történik keresés
     * @return  array A feldolgozott elemek tömbje, vagy üres tömb
     */
    protected function GetList(int $startindex, ?string $kereses) : array {
        return [];
    }

    /**
     * A HTML <head> dinamikus részének tartalmát beállító osztály
     * @return void
     */
    protected function SetHTMLHeadMetaData() : void {
        if (function_exists('HtmlHead')) {
            HtmlHead($this->keywords, $this->canonical, $this->title, $this->ogtype, $this->publishtime, $this->shareimage, $this->robots, $this->ablakcim, $this->sitedesc, $this->cimke);
            $this->RenderHTMLHeadMetadata();
        }
    }

    /**
     * A HTML <head> LdJSON részét előállító osztály.
     *
     * Kizárólag SEO célokat szolgál, tehát keresőbotok elől rejtett weboldal esetén nincs értelme foglalkozni vele
     * @return void
     */
    public function SetLdJSON() : void {}

    /**
     * Függvény a lapozáshoz szükséges változók értékének beállítására
     *
     * Itt kap értéket a ($this->lapozas['elozo'] és $this->lapozas['kovetkezo']),
     * valamint itt kerül módosításra a referenciaként megkapott változók ($startindex és $elemperoldal) értékét is a pillanatnyi lap alapján.
     *
     * Az elemperoldal értéke a függvény futásának vége előtt +1-el módosításra kerül, erre az utolsó lap érzékeléséhez van szükség
     *
     * @param int $startindex Az elsőként lekérni kívánt elem indexszáma
     * @param array $params A ParseGet függvény által feldolgozott értékek tömbje
     * @return void
     *
     */
    protected function SetPagingVars(int &$startindex, array $params) : void {
        if(isset($params['oldal'])) {
            $this->lapozas['aktualis'] = $params['oldal'];
            if($params['oldal'] != 1)
                $startindex = ($params['oldal'] - 1) * $this->elemperoldal;
            $this->lapozas['elozo'] = ($params['oldal'] > 1) ? $params['oldal'] - 1 : null;
            $this->lapozas['kovetkezo'] = $params['oldal'] + 1;
        }
        else {
            $this->lapozas['kovetkezo'] = 2;
        }
        $this->elemperoldal++;
        $this->debugmessages['Lapozás'] = [
            'Kezdő index' => $startindex,
            'Oldalankénti elemek' => $this->elemperoldal,
            'params' => print_r($params, true)
        ];
    }

    /**
     * A kereséshez használt változók beállításáért felelős metódus.
     *
     * @param array $params A ParseGet függvény által feldolgozott értékek tömbje
     * @return string|null A keresési kifejezés, ha van, egyébként null
     */
    protected function SetKereses(array $params) : ?string {
        $this->jsfiles[] = 'template/assets/js/kereses.js';
        $this->PHPvarsToJS['selectedPage'] = $this->selectedview;
        $kereses = $params['kereses'] ?? null;
        if($kereses) {
            $this->urlparams = $this->selectedview . "/kereses/$kereses";
            $this->keresesplaceholder = $kereses;
        }
        return $kereses;
    }

    /**
     * Primitív függvény ami megállapítja, hogy a pillanatnyi oldal az utolsó-e.
     *
     * A SetPagingVars úgy állítja az értékeket, hogy mindig 1-gyel több elemet kérdezzen le a rendszer az adatbázisból, mint amennyit a felhasználó akart.
     * Így tehát ha egy olyan oldalra jutunk, ahol pontosan annyi elem van, amennyit a felhasználó kért, akkor tudjuk, hogy az utolsó oldalon járunk.
     * Hogy megjelenítve pontosan annyi elem legyen, amennyit a felhasználó kért, ha van következő oldal, az utolsó elem törlésre kerül a tömbből
     *
     * @param array $pagecontents Az adatbázis lekrédezés elemeit tartalmazó tömb
     * @return void
     *
     */
    protected function DetectLastPage(array &$pagecontents) : void {
        if(count($pagecontents) < $this->elemperoldal) {
            $this->lapozas['kovetkezo'] = null;
        } else {
            array_pop($pagecontents);
        }
    }

    /**
     * Függvény, ami megállapítja, hogy a pillanatnyilag beállított értékek alapján hány oldal van összesen
     *
     * @param int $osszrekodszam A megadott feltételek melletti összes rekord száma az adatbázisban
     * @return void
     */
    public function SetOsszlapszam(int $osszrekodszam) : void {
        $this->lapozas['osszlapszam'] = (int)ceil($osszrekodszam / ($this->elemperoldal - 1));
        $this->debugmessages['Lapozás paraméterek'] = [
            'osszlapszam' => $this->lapozas['osszlapszam'],
            'osszrekordszam' =>$osszrekodszam,
            'elemperoldal' =>$this->elemperoldal
            ];
    }

    /**
     * Egy FORM teljeskörű, automatikus generálását előkészítő metódus
     *
     * Ezt meghívva, amennyiben már generáltunk valid FormBuilder objektumot,
     * a rendszer a teljes formot létrehozza a számunkra, nem szükséges még view fájlt sem készíteni hozzá
     *
     * @param string|null $formaction A form submit-jének target url-je. Kötelező megadni. Lehet null is, ezesetben a form a saját URL-jére küldi a POST kérést
     * @param string $buttontext
     * @param string|null $masodikoszlop_elsoelem
     * @param string|null $format
     * @param int|null $submitoszlop
     * @return void
     */
    protected function SetFullForm(?string $formaction, string $buttontext = "Beküld", ?string $masodikoszlop_elsoelem = null, ?string $format = '1-1', ?int $submitoszlop = 0) : void {
        $this->form->SetFormHead($formaction);
        $this->form->SetTwoColForm($masodikoszlop_elsoelem, $format);
        $this->form->SetSubmit($buttontext, $submitoszlop);
    }

    /**
     * Új elem létrehozására szolgáló oldalt kiválasztó, és alapbeállításait elvégző függvény
     *
     * Paraméter nélkül meghívva csak ellenőrzi az írásjogot, beállítja a view fájlt, és legenerálja az alap FORM-ot a Form() függvény alapján.
     * Legalább két paraméterrel ($formaction és $buttontext) meghívva legenerálja az alap FORM oldalt is. Egyszerű, csak elemi beviteli mezőket tartalmazó FORM-ok esetén ez bőven elég.
     *
     * @param string|null $formaction A form action='' rész tartalma. Értelemszerűen egy valós link, ami a beküldött FORM tartalmát képes lesz értelmezni
     * @param string|null $buttontext A submit gomb felirata
     * @param string|null $masodikoszlop_elsoelem Ha kétoszlopos megjelenítést akarunk a FORM-nak, itt kell megadni, hogy melyik legyen a második oszlop első eleme. Csak a mező nevét kell megadni.
     * @param string|null $format Kétoszlopos megjelenítés esetén itt adhatjuk meg, hogy az oszlopok szélessége milyen eloszlásban legyen. Alapértelmezetten 1-1, de valós érték még a 2-1, 3-1 és 3-2 is.
     * @param int|null $submitoszlop A submit mező alapértelmezetten alul-középen van, de ezt a változót 1-esre, vagy 2-esre állítva megadhatjuk, hogy valamelyik oszlop utolsó eleme legyen
     * @return Page
     */
    protected function SetViewUj(?string $formaction = null, ?string $buttontext = null, ?string $masodikoszlop_elsoelem = null, ?string $format = null, ?int $submitoszlop = 0) : Page {
        $this->irasjog = true;
        if(isset($this->views['uj']))
            $this->view = $this->views['uj'];
        else {
            $this->viewsgyoker = ROOT_DIR;
            $this->view = "/template/assets/basic_form.php";
        }
        $this->form = $this->Form();
        if($formaction && $buttontext)
            $this->SetFullForm($formaction, $buttontext, $masodikoszlop_elsoelem, $format, $submitoszlop);

        return $this;
    }

    /**
     * Egy már meglévő elem szerkesztésére szolgáló oldalt kiválasztó, és alapbeállításait elvégző függvény
     *
     * Egy paraméterrel (ami KÖTELEZŐEN a szerkesztendő objektum) meghívva csak ellenőrzi az írásjogot, beállítja a view fájlt, és legenerálja az alap FORM-ot a Form() függvény alapján.
     * Legalább négy paraméterrel ($elem, $elemnev, $formaction és $buttontext) meghívva legenerálja az alap FORM oldalt is. Egyszerű, csak elemi beviteli mezőket tartalmazó FORM-ok esetén ez bőven elég.
     *
     * @param mixed $elem A szerkeszteni kívánt elem, vagy bármi olyan egyéb változó, ami alapján az osztály Form függvénye tudja, hogy hogyan kell feldolgoznia
     * @param string|null $elemnev A 403-as hibaüzenet pontos, menüponthoz illeszkedő szövegének generálásához használt szöveg
     * @param string|null $formaction A form action='' rész tartalma. Értelemszerűen egy valós link, ami a beküldött FORM tartalmát képes lesz értelmezni
     * @param string|null $buttontext A submit gomb felirata
     * @param string|null $masodikoszlop_elsoelem Ha kétoszlopos megjelenítést akarunk a FORM-nak, itt kell megadni, hogy melyik legyen a második oszlop első eleme. Csak a mező nevét kell megadni.
     * @param string|null $format Kétoszlopos megjelenítés esetén itt adhatjuk meg, hogy az oszlopok szélessége milyen eloszlásban legyen. Alapértelmezetten 1-1, de valós érték még a 2-1, 3-1 és 3-2 is.
     * @param int|null $submitoszlop A submit mező alapértelmezetten alul-középen van, de ezt a változót 1-esre, vagy 2-esre állítva megadhatjuk, hogy valamelyik oszlop utolsó eleme legyen
     * @return Page
     */
    protected function SetViewSzerkeszt(mixed $elem, ?string $elemnev = 'elem', ?string $formaction = null, ?string $buttontext = null, ?string $masodikoszlop_elsoelem = null, ?string $format = null, ?int $submitoszlop = 0) : Page {
        if(!$this->irasjog) {
            return new SinglePage(403, "A kiválasztott $elemnev szerkesztéséhez nincs jogosultságod!");
        }

        if(isset($this->views['szerkeszt']))
            $this->view = $this->views['szerkeszt'];
        else {
            $this->viewsgyoker = ROOT_DIR;
            $this->view = "/template/assets/basic_form.php";
        }

        $this->form = $this->Form($elem);

        if($formaction && $buttontext)
            $this->SetFullForm($formaction, $buttontext, $masodikoszlop_elsoelem, $format, $submitoszlop);

        return $this;
    }

    protected function SetSelectedLista(array $params, array &$returnarray) : Page {
        $kereses = $this->SetKereses($params);
        $this->irasjog = $this->GetIrasjog();
        $startindex = 0;
        $this->SetPagingVars($startindex, $params);
        $returnarray = $this->GetList($startindex, $kereses);
        $this->DetectLastPage($returnarray);

        return $this;
    }

    // <--- RENDERELÉST VÉGZŐ METÓDUSOK ---> //

    /**
     * A HTML <head> részét renderelő osztály
     * @return void
     */
    public function RenderHTMLHeadMetadata() : void {
        include(ROOT_DIR . "/template/assets/htmlheadmetadata.php");
        $this->SetLdJSON();
    }

    /**
     * Az oldal <body> részében a <header> részt, tehát a vizuális fejlécet renderelő osztály.
     *
     * @return void
     */
    public function RenderHeader() : void {
        if(!$this->headerview || !file_exists($this->viewsgyoker . $this->headerview))
            return;
        include($this->viewsgyoker . $this->headerview);
    }

    /**
     * Az oldal fő tartalmát renderelő osztály.
     *
     * Hacsak nincs valami nagyon extra dolog abban, ahogy a modul renderelni akarja az oldalait, nem kell felülírni
     * @return void
     */
    public function Render() : void {
        if(!file_exists($this->viewsgyoker . $this->view)) {
            $this->debugmessages['View hiba'] = "A kért view fájl (<strong>$this->view</strong>) nem található a megadott elérési útvonalon: " . $this->viewsgyoker . $this->view;
            return;
        }

        foreach ($this->PHPvarsToJS as $key => $value) {
            Settings::$PHPvarsToJS[$key] = $value;
        }
        foreach ($this->jsfiles as $file) {
            Settings::$jsfiles[] = $file;
        }

        if($this->uzenet) {
            if(!file_exists($this->viewsgyoker . $this->views[$this->uzenet])) {
                $this->debugmessages['Üzenet view hiba'] = "A kért üzenetátadó view fájl (<strong>$this->views[$this->uzenet]</strong>) nem található a megadott elérési útvonalon: " . $this->viewsgyoker . $this->views[$this->uzenet];
            }
            else {
                include($this->viewsgyoker . $this->views[$this->uzenet]);
            }
        }
        include($this->viewsgyoker . $this->view);
    }

    /**
     * A context menüt renderelő osztály.
     *
     * Csak akkor csinál bármit, ha a Router-ben hozzá lett adva legalább egy elem.
     *
     * A context menü az adott oldalon belül elérhető funkciók elérésére szolgál. Tehát új elem, szerkesztés, és hasonlók.
     * @return void
     */
    public function RenderContextMenu() : void {
        if(count($this->contextmenu) > 0)
            include(ROOT_DIR . "/template/assets/contextmenu.php");
    }

    /**
     * A lapozó gombokat renderelő osztály.
     *
     * @return void
     */
    protected function RenderLapozo() : void {
        include(ROOT_DIR. "/template/assets/lapozo.php");
    }

    protected function RenderKereso() : void {
        include(ROOT_DIR. "/template/assets/kereso.php");
    }

    /**
     * Egy fuzzy kereséshez szükséges stringeket generáló statikus függvény.
     *
     * @param string $basestring A kiinduló string.
     * @return array A generált stringek tömbje.
     */
    public static function FuzzySearchStringGen (string $basestring) : array {
        $needle = '%'; $needle2 = [];
        $chars = preg_split('//u', $basestring, -1, PREG_SPLIT_NO_EMPTY);
        $tmp = '';
        $limit = min(count($chars), 4);
        for($i=0; $i < $limit; $i++) {
            $needle .= $chars[$i] . '_';
        }
        for ($i = 0; $i < $limit - 1; $i++) {
            $tmp = $chars;
            [$tmp[$i], $tmp[$i + 1]] = [$tmp[$i + 1], $tmp[$i]];
            $needle2[] = '%' . implode('', $tmp) . '%';
        }

        return [$basestring . "%", "%" . $basestring . "%" , $needle, ...$needle2];
    }
}