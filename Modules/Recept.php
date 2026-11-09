<?php

namespace Infoinventory\Modules;

use HTMLPurifier;
use HTMLPurifier_Config;
use Infoinventory\Classes\FeltoltesHandler;
use Infoinventory\Classes\FormBuilder;
use Infoinventory\Classes\Helpers;
use Infoinventory\Classes\Settings;

class Recept extends Page
{
    public string $cimke = "recept";
    public ?array $recept = null;
    public string $keresesplaceholder = "Mit eszünk ma?";
    public array $apimethods = [
        'GET' => ['kereses' => 'ReceptFuzzyList',
            'receptkategoriak' => 'ReceptKategoriak'],
        'POST' => ['ertekel' => 'Ertekel',
            'bookmark' => 'Bookmark']
    ];
    protected string $viewsgyoker = __DIR__ . "/views/recept/";
    protected bool $irasjog = false;

    protected FormBuilder $form;
    protected array $headerviews = [
        'recept' => 'receptheader.php'
    ];
    protected array $views = [
        'recept' => 'recept.php',
        'receptek' => 'receptek.php',
        'szakacskonyv' => 'szakacskonyv.php',
        'szerkeszt' => 'szerkeszt.php',
        'cimkefelho' => 'cimkefelho.php',
        'uj' => 'uj.php'
    ];

    protected array $mediatypes = ['image/jpeg', 'image/bmp', 'image/png', 'image/webp'];

    private array $mertekegysegek = [
        'g' => 1,
        'ml' => 1,
        'cl' => 10,
        'dkg' => 10,
        'dl' => 100,
        'kg' => 1000,
        'l' => 1000,
        'csipet' => 1,
        'kk' => 5,
        'tk' => 8,
        'ek' => 15,
        'pohár' => 200,
        'csésze' => 250,
        'bögre' => 250,
        'fej' => 100,
        'db' => 60,
        'szelet' => 120,
        'csomag' => 80,
        'gerezd' => 6
    ];

    private array $mertekegysegmap = [
        'g' => 'g',
        'gr' => 'g',
        'gram' => 'g',
        'gramm' => 'g',
        'dkg' => 'dkg',
        'deka' => 'dkg',
        'dg' => 'dkg',
        'dekagram' => 'dkg',
        'dekagramm' => 'dkg',
        'kg' => 'kg',
        'kiló' => 'kg',
        'kilo' => 'kg',
        'kilogram' => 'kg',
        'kilogramm' => 'kg',
        'kilógram' => 'kg',
        'kilógramm' => 'kg',
        'ml' => 'ml',
        'mili' => 'ml',
        'milli' => 'ml',
        'mililiter' => 'ml',
        'milliliter' => 'ml',
        'cl' => 'cl',
        'cent' => 'cl',
        'centiliter' => 'cl',
        'dl' => 'dl',
        'deci' => 'dl',
        'deciliter' => 'dl',
        'l' => 'l',
        'liter' => 'l',
        'kk' => 'kk',
        'kávéskanál' => 'kk',
        'tk' => 'tk',
        'teáskanál' => 'tk',
        'ek' => 'ek',
        'evőkanál' => 'ek',
        'db' => 'db',
        'darab' => 'db',
        'fej' => 'fej',
        'csipet' => 'csipet',
        'csg' => 'csomag',
        'csomag' => 'csomag',
        'köteg' => 'köteg',
        'pohár' => 'pohár',
        'bögre' => 'bögre',
        'csésze' => 'csésze',
        'szelet' => 'szelet'
    ];

    public function Router(array $params) : Page {
        $this->validpagemethods = ['uj', 'szerkeszt', 'torol', 'kereses', 'kategoriak'];
        $params = $this->ParseGet($params);
        $this->params = $params;
        if($this->selectedpage == 'receptek' || $this->selectedpage == 'recept' || $this->selectedpage == 'szakacskonyv') {
            $this->jsfiles[] = 'Pages/views/recept/assets/quickactions.js';
        }
        if($this->selectedpage == 'recept') {
            if($params['method'] == 'uj' || $params['method'] == 'szerkeszt') {
                $this->jsfiles[] = 'Pages/views/_assets/js/fileupload.js';
                $this->jsfiles[] = 'Pages/views/recept/assets/listautocomplete.js';
                $this->jsfiles[] = 'includes/external/ckeditor5/ckeditor.js';
                $this->jsfiles[] = 'Pages/views/_assets/js/ckeditorinitializer.js';
                $this->PHPvarsToJS['selectorname'] = 'recept_szoveg';
                $this->PHPvarsToJS['uploadcontainer'] = "kepek";
                if($params['method'] == 'uj')
                    $this->PHPvarsToJS['iterator'] = 0;
            }

            if($params['method'] == 'uj' && Settings::$uid) {
                $this->muvelet = 'uj';
                $this->form = $this->Form('uj');
                $this->view = $this->views['uj'];
                return $this;
            }

            if(!isset($params['elemid'])) {
                $recept = new Recept('receptek');
                $recept->Router($params);
                return $recept;
            }

            $this->jsfiles[] = 'Pages/views/recept/assets/recept.js';
            $this->recept = ReceptDB::GetRecept($params['elemid']);

            if(!$this->recept)
                return new SinglePage('404');

            $this->irasjog = $this->GetIrasjog($this->recept['recept']['recept_id']);
            if($params['method'] == 'szerkeszt' && Settings::$uid && $this->irasjog) {
                $this->muvelet = 'szerkeszt';
                $this->form = $this->Form('szerkeszt', $this->recept['recept']);
                $this->view = $this->views['szerkeszt'];
                $this->PHPvarsToJS['iterator'] = count($this->recept['alapanyagok']);
            }
            elseif($params['method'] == 'torol' && Settings::$uid && $this->irasjog) {
                $this->redirtarget = ROOT_PATH . '/receptek';
                $this->muvelet = 'torol';
            }
            elseif($params['method'] != 'szerkeszt' && !$this->GetOlvasasjog($params['elemid']))
                return new SinglePage('403');
            else {
                $this->PHPvarsToJS['recept_id'] = $this->recept['recept']['recept_id'];
                $this->view = $this->views['recept'];
                $this->headerview = $this->headerviews['recept'];
            }
            return $this;
        }

        if($this->selectedpage == 'receptek') {
            $this->jsfiles[] = 'Pages/views/recept/assets/kereses.js';
            $this->jsfiles[] = 'Pages/views/recept/assets/receptek.js';
            $kereses = null;
            if(isset($params['method']) && isset($params['elemid']) && $params['method'] == 'kereses')
                $kereses = $params['elemid'];

            if($params['method'] == 'kategoriak') {
                $this->muvelet = 'kategoriak';
            }

            if(!$kereses) {
                $startindex = 0;
                $elemperoldal = 20;
                $this->LapozasPrepare($startindex, $elemperoldal, $params);
                if(isset($_SESSION['kategoriacimkek']))
                    $this->recept = $this->LapozasFinalize(ReceptDB::GetReceptek($startindex, $elemperoldal, $_SESSION['kategoriacimkek']), $elemperoldal);
                else
                    $this->recept = $this->LapozasFinalize(ReceptDB::GetReceptek($startindex, $elemperoldal), $elemperoldal);
            }
            else {
                $this->recept = $this->ReceptKereses($kereses);
                $this->keresesplaceholder = $kereses;
            }
            $this->view = $this->views['receptek'];
            return $this;
        }

        if($this->selectedpage == 'szakacskonyv' && !Settings::$uid) {
            return new SinglePage('401');
        }
        elseif($this->selectedpage == 'szakacskonyv') {
            $this->recept = ReceptDB::GetSzakacskonyv(Settings::$uid);
            $this->view = $this->views['szakacskonyv'];
            return $this;
        }
        return $this;
    }

    public function HtmlHead() : void {
        $this->title .= ' - ' . ucfirst($this->cimke);
        if ($this->selectedpage == "recept") {
            if ($this->muvelet == "uj") {
                $this->title .= ' - Új recept felvitele';
                $this->sitedesc = "Új recept rögzítése a rendszerbe";
            } else {
                $recept = $this->recept['recept'];
                if ($this->muvelet == "szerkeszt"){
                    $this->title .= ': ' . ucfirst(Helpers::NeveloHatarozo($recept['recept_nev'])) . 'szerkesztése';
                    $this->sitedesc = "Az alapanyag szerkesztése";
                }
                else {
                    $this->title .= ": " . $recept['recept_nev'];
                    $this->sitedesc = ucfirst(Helpers::NeveloHatarozo($recept['recept_nev'])) . " elkészítésének módja";
                }
                $this->canonical = ROOT_PATH . '/recept/' . $recept['slug'];
            }
        }
        $this->ablakcim = $this->title;
        $this->HtmlHeadRender();
    }

    public function LdJSON() : void {
        if(!($this->selectedpage == 'recept' && !$this->muvelet))
            return;
        $recept = $this->recept['recept'];
        $alapanyagok = $this->recept['alapanyagok'];
        $kepek = $this->recept['kepek'];
        $receptkepek = [];
        $osszetevok = [];
        foreach($alapanyagok as $alapanyag) {
            $mennyiseg = ($alapanyag['mennyiseg']) ? $alapanyag['mennyiseg'] . ' ' : '';
            $mertekegyseg = ($alapanyag['mertekegyseg']) ? $alapanyag['mertekegyseg'] . ' ' : '';
            $osszetevok[] = $mennyiseg . $mertekegyseg . $alapanyag['alapanyag_nev'];
        }
        foreach($kepek as $kep) {
            $receptkepek[] = ROOT_PATH . '/' . $kep['fajl'];
        }
        ?><script type="application/ld+json">
        {
            "@context": "https://schema.org",
          "@type": "Recipe",
          "name": "<?=$recept['recept_nev']?>",
          "description": "<?=$recept['recept_nev']?>",
          <?php if($recept['ertekelesek_szama'] > 0) { ?>
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "<?=$recept['ertekeles']?>",
                "ratingCount": "<?=$recept['ertekelesek_szama']?>"
              },
          <?php } ?>
        
          "recipeIngredient": [
            <?='"' . implode('",
            "', $osszetevok) . '"'?>
        ],
        
          "recipeYield": "<?=$recept['adagmeret']?> adag",
        
          "prepTime": "PT<?=$recept['elokeszuletek']?>M",
          "cookTime": "PT<?=$recept['sutesido']?>M",
          "totalTime": "PT<?=$recept['elokeszuletek'] + $recept['sutesido']?>M",
        
          "datePublished": "<?=Helpers::SQLTimeStampToDate($recept['letrehozas_ideje'])?>",
        
          "image": [
              <?='"' . implode('",
              "', $receptkepek) . '"'?>
          ]
        }
        </script><?php
    }

    private function Form(string $muvelet, ?array $recept = null) : FormBuilder {
        $form = (new FormBuilder())
            ->TextBox('recept_szoveg', 'Recept szövege')
            ->Number('elokeszuletek', 'Mennyi ideig tart a főzés/sütés előkészítése?')
            ->Number('sutesido', 'Mennyi a főzés/sütés ideje?')
            ->Number('adagmeret', 'Adag (Hány főre elég?)')
            ->Checkbox('lathatosag', 'Recept látható nyilvánosan');
        if($muvelet == 'szerkeszt' && $recept != null && $this->GetIrasjog($recept['recept_id'])) {
            $form->Hidden('slug')
                ->Hidden('recept_id')
                ->SetValue('slug', $recept['slug'])
                ->SetValue('recept_id', $recept['recept_id'])
                ->SetValue('adagmeret', $recept['adagmeret'])
                ->SetValue('recept_szoveg', $recept['recept_szoveg'])
                ->SetValue('lathatosag', $recept['lathatosag'])
                ->SetValue('elokeszuletek', $recept['elokeszuletek'])
                ->SetValue('sutesido', $recept['sutesido']);
        }
        return $form;
    }

    protected function Uj () : bool
    {
        if(Settings::$uid) {
            $kepidk = null;
            $slug = Helpers::SlugGenerator($_POST['recept_nev']);
            $slug = Helpers::SlugVerifier($slug, [ReceptDB::class, 'GetRecept']);
            $alapanyagok = $this->ParseAlapanyagok($_POST['alapanyagok']);
            $tapanyagok = $this->TapanyagKalkulacio($alapanyagok['alapanyagok']);
            $config = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($config);
            $recept_szoveg = $purifier->purify($_POST['recept_szoveg']);
            $eredmeny = ReceptDB::UjRecept($_POST['recept_nev'], $recept_szoveg, $_POST['lathatosag'] ?? 0, $slug, $_POST['adagmeret'], $alapanyagok, $_POST['elokeszuletek'] ?? null, $_POST['sutesido'] ?? null, $tapanyagok, Settings::$uid);
            if($eredmeny) {
                $this->redirtarget = ROOT_PATH . '/recept/' . $slug;

                $cimkek = $_POST['cimke'] ?? [];
                $cimkementes = ReceptDB::ReceptCimkek($eredmeny, $cimkek);
                if (isset($_FILES['kepek'])) {
                    $kepek = new FeltoltesHandler($this->mediatypes, 'receptkepek', date('Y'), 'receptkepek', Settings::$uid);
                    $kepidk = $kepek->Feltoltes($_FILES['kepek']);
                }
                if($kepidk['eredmeny']) {
                    $eredmeny = ReceptDB::ReceptKepek($eredmeny, $kepidk['uploadids']);
                }
            }
            return $eredmeny;
        }
        else
            return false;
    }

    protected function Szerkeszt () : bool
    {
        if($this->GetIrasjog($_POST['recept_id'])) {
            $kepidk = null;
            $alapanyagok = $this->ParseAlapanyagok($_POST['alapanyagok']);
            $tapanyagok = $this->TapanyagKalkulacio($alapanyagok['alapanyagok']);
            $config = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($config);
            $recept_szoveg = $purifier->purify($_POST['recept_szoveg']);
            $eredmeny = ReceptDB::ReceptSzerkeszt($_POST['recept_nev'], $recept_szoveg, $_POST['lathatosag'] ?? 0, $_POST['adagmeret'], $alapanyagok, $_POST['elokeszuletek'] ?? null, $_POST['sutesido'] ?? null, $tapanyagok, Settings::$uid, $_POST['recept_id']);
            if($eredmeny) {
                $this->redirtarget = ROOT_PATH . '/recept/' . $_POST['slug'];

                $cimkek = $_POST['cimke'] ?? [];
                ReceptDB::ReceptCimkek($eredmeny, $cimkek);

                if(isset($_POST['meglevokepek']))
                    ReceptDB::KepAllapot($eredmeny, $_POST['meglevokepek']);
                else
                    ReceptDB::KepAllapot($eredmeny);

                if(isset($_FILES['kepek'])) {
                    $kepek = new FeltoltesHandler($this->mediatypes, 'receptkepek', date('Y'), 'receptkepek', Settings::$uid);
                    $kepidk = $kepek->Feltoltes($_FILES['kepek']);
                }
                if($kepidk['eredmeny']) {
                    $eredmeny = ReceptDB::ReceptKepek($eredmeny, $kepidk['uploadids']);
                }

                if(isset($_POST['boritokep']) && is_numeric($_POST['boritokep'])) {
                    ReceptDB::ElsodlegesKep($eredmeny, $_POST['boritokep']);
                }
            }
            return $eredmeny;
        }
        else
            return false;
    }

    protected function Torol () : bool {
        if(!$this->GetIrasjog($_POST['recept_id']))
            return false;

        return ReceptDB::ReceptTorol($_POST['recept_id']);
    }

    protected function Kategoriak() : bool {
        $_SESSION['kategoriacimkek'] = $_POST['cimke'] ?? [];
        $this->mixintext = 'no-mixin';
        return true;
    }

    private function ParseAlapanyagok(?array $alapanyagok) : array {
        if(!$alapanyagok)
            return [];
        $feldolgozott = [];
        $allergenek = ['cukor' => 0, 'gluten' => 0, 'laktoz' => 0];
        $alapdb = AlapanyagDB::GetAlapanyagok(0, -1);
        foreach($alapanyagok as $alapanyag) {
            $megtalalt = null;
            $osszetevo = $alapanyag['alapanyag'];

            foreach($alapdb as $alap) {
                if($alap['alapanyag_id'] == $osszetevo) {
                    if($alap['cukor'] == '*')
                        $allergenek['cukor'] = 1;
                    if($alap['glutén'] == '*')
                        $allergenek['gluten'] = 1;
                    if($alap['laktóz'] == '*')
                        $allergenek['laktoz'] = 1;
                    $megtalalt = $alap;
                    break;
                }
            }
            if($megtalalt) {
                $mertek = $this->ParseMennyisegek($alapanyag['mennyiseg']);
                $feldolgozott[] = ['mennyiseg' => $mertek['mennyiseg'] ?? null, 'mertekegyseg' => $mertek['mertekegyseg'] ?? null, 'alapanyag_id' => $osszetevo, 'kaloria' => $megtalalt['kalória'], 'zsir' => $megtalalt['zsír'], 'feherje' => $megtalalt['fehérje'], 'szenhidrat' => $megtalalt['szénhidrát']];
            }
        }

        return ['alapanyagok' => $feldolgozott, 'allergenek' => $allergenek];
    }

    private function ParseMennyisegek(?string $mennyiseg) : array {
        if(!$mennyiseg)
            return [];
        $tmp = preg_split('/\s+/', trim($mennyiseg));
        $feldolgozott = [];
        switch(count($tmp)) {
            case 1:
                if(is_numeric($tmp[0]) && is_int((int)$tmp[0]))
                    $feldolgozott['mennyiseg'] = (int)$tmp[0];
                else
                    $feldolgozott['mennyiseg'] = null;
                break;
            case 2:
                if(is_numeric($tmp[0]) && is_int((int)$tmp[0]))
                    $feldolgozott['mennyiseg'] = (int)$tmp[0];
                else
                    $feldolgozott['mennyiseg'] = null;
                $feldolgozott['mertekegyseg'] = $this->mertekegysegmap[$tmp[1]] ?? $tmp[1];
        }
        return $feldolgozott;
    }

    private function TapanyagKalkulacio(array $osszetevok) : ?string {
        $kaloria = $zsir = $feherje = $szenhidrat = 0;
        foreach($osszetevok as $osszetevo) {
            $hasznaltmennyiseg = $this->AlapraNormalizalas($osszetevo['mennyiseg'], $osszetevo['mertekegyseg']);
            $kaloria += $osszetevo['kaloria'] * $hasznaltmennyiseg;
            $zsir += $osszetevo['zsir'] * $hasznaltmennyiseg;
            $feherje += $osszetevo['feherje'] * $hasznaltmennyiseg;
            $szenhidrat += $osszetevo['szenhidrat'] * $hasznaltmennyiseg;
        }

        $tapanyagok = ['kaloria' => round($kaloria, 2), 'zsir' => round($zsir, 2), 'feherje' => round($feherje, 2), 'szenhidrat' => round($szenhidrat, 2)];
        return json_encode($tapanyagok, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    }

    private function GetCimkek(?array $receptcimkei = null, bool $inform = false) : array {
        if($receptcimkei)
            $kivalasztottcimkek = $receptcimkei;
        elseif($inform === false && isset($_SESSION['kategoriacimkek']))
            $kivalasztottcimkek = $_SESSION['kategoriacimkek'];
        else
            $kivalasztottcimkek = [];

        $rcimkereturn = [];
        $mindencimke = CimkeDB::GetCimkek();
        foreach($mindencimke as &$cimke) {
            if(in_array($cimke['receptcimke_id'], $kivalasztottcimkek)) {
                $cimke['kivalasztva'] = true;
                if($receptcimkei)
                    $rcimkereturn[] = $cimke;
            }
        }

        if($receptcimkei && !$inform)
            return $rcimkereturn;

        return $mindencimke;
    }

    protected function CimkeForm(?array $receptcimkei = null, bool $inform = false) : void {
        $cimkek = $this->GetCimkek($receptcimkei, $inform);
        $this->form = (new FormBuilder());
        include __DIR__ . "/views/recept/cimkefelho.php";
    }

    public function GetIrasjog(int|string|null|bool $elem_id) : bool {
        if(!$elem_id)
            return false;
        if(Settings::$admin)
            return true;

        return ReceptDB::GetReceptIrasjog(Settings::$uid, $elem_id);
    }

    public function GetOlvasasjog(int|string $elem_id) : bool {
        if(Settings::$admin)
            return true;
        if($this->recept['recept']['lathatosag'] == 1)
            return true;
        if($this->recept['recept']['felhasznalo_id'] == Settings::$uid)
            return true;
        return false;
    }

    private function ReceptKereses(string $needle) : array {
        $searcharr = Helpers::FuzzySearchStringGen($needle);

        $firstpass = ReceptDB::GetReceptekFuzzy($searcharr, 'recept_id');
        if(count($firstpass) == 0)
            return [];

        return Helpers::FuzzySearch($firstpass, $needle, 'recept_nev');
    }

    public function Bookmark() : array|bool {
        if(!isset($_POST['recept_id'])) {
            $data['eredmeny'] = false;
            $data['message'] = 'Nem lett kiválasztva menteni kívánt recept!';
            return $data;
        }

        if(!Settings::$uid) {
            $data['eredmeny'] = false;
            $data['message'] = 'Csak bejelentkezett felhasználók menthetnek el receptet!';
            return $data;
        }

        $eredmeny = ReceptDB::BookmarkRecept($_POST['recept_id'], Settings::$uid);
        if($eredmeny === null) {
            $data['eredmeny'] = true;
            $data['message'] = 'A recept eltávolításra került a Szakácskönyvedből!';
        }
        else {
            $data['eredmeny'] = $eredmeny;
            $data['message'] = $eredmeny ? 'A recept mentése sikeres volt' : 'A recept mentése nem sikerült!';
        }

        return $data;
    }

    public function Ertekel() : array|bool {
        if($_SERVER['REQUEST_METHOD'] !== 'POST'
            || !isset($_POST['recept_id'])
            || !isset($_POST['ertekeles'])
        )
            return false;

        if(!Settings::$uid) {
            return ['message' => 'Csak bejelentkezett felhasználók értékelhetnek receptet!',
                'status' => 403];
        }

        if(is_numeric($_POST['ertekeles']) && in_array((int)$_POST['ertekeles'], [1, 2, 3, 4, 5]))
            $ertekeles = (int)$_POST['ertekeles'];
        else
            return ['message' => 'Hibás értékelés! Csak 1-től 5-ig lehet értékelni!',
                'status' => 400];

        $ertekeles = ReceptDB::Ertekeles(Settings::$uid, $_POST['recept_id'], $ertekeles);
        $data['eredmeny'] = $ertekeles;
        $data['message'] = $ertekeles ? 'Az értékelésed sikeresen hozzáadva' : 'A receptet nem sikerült értékelni!';

        return $data;
    }

    /** Mivel az alapanyagok normalizált mennyisége a 100-as alap mértékegységben van megadva (tehát 100 gramm, 100 mili, stb)
     * ezért először át kell váltani a mértékegységet, majd leosztani 100-zal,
     * hogy megkapjuk a szorzószámot a tápanyag és kalória számításhoz.
     * (pl.: 18 dkg rizs -> 180 gramm rizs -> 1,8 normalizált érték -> 1,8 * 130kcal = 234 kcal a felvitt mennyiségben
     *
     * !!! HOZZÁVETŐLEGES eredményt ad a nem triviális mértékegységek esetében (csipet, kk, ek, fej, csomag, stb)
    */
    private function AlapraNormalizalas(?int $mennyiseg, ?string $mertekegyseg) : float {
        if(!$mennyiseg || !$mertekegyseg || !array_key_exists($mertekegyseg, $this->mertekegysegek))
            return 0;

        return $mennyiseg * $this->mertekegysegek[$mertekegyseg] / 100;
    }
}