<?php

namespace Infoinventory\Classes;

use Infoinventory\Classes\APIResult;
use Infoinventory\Modules\Core\Page;
use Infoinventory\Modules\Core\SinglePage;

class Controller
{
    public Page $page;
    public bool $api;
    private ?string $kertoldal;
    private string $newuri;
    private bool $postenged = true;
    private ?APIResult $api_result;

    private static array $icons = [
        'siker' => 'success',
        'hiba' => 'error',
        'figyelmeztetes' => 'warning',
        'letiltva' => 'error'
    ];

    private static array $muveletek = [
        'uj' => 'hozzáadása',
        'hozzad' => 'hozzáadása',
        'szerkeszt' => 'szerkesztése',
        'bekuld' => 'beküldése',
        'torol' => 'törlése'
    ];

    public function __construct() {
        $this->kertoldal = $_GET['page'] ?? null;
        $this->api = $this->kertoldal == 'api';
    }

    public function Execute() : void {
        if($this->api) {
            $this->RunAPI();
        }
        else {
            $postresult = null;
            $this->page = self::PageSelect($this->kertoldal);
            $this->page = $this->Router($this->page);
            if($_SERVER['REQUEST_METHOD'] === 'POST' && $this->postenged)
                $postresult = $this->PostController();
            if($postresult !== null)
                $this->AfterPost($postresult);
            elseif(Helpers::ArrayKeyLetezik($_SESSION, 'eredmeny', 'mixintext', 'muvelet'))
                $this->PostMessage();
        }
    }

    private function Router(Page $page) : Page {
        $getparams = $_GET['params'] ?? '';
        $params = $getparams ? explode('/', $getparams) : [];
        $verifypage = $page->Router($params);

        if($page !== $verifypage) {
            $this->postenged = false;
            return $verifypage;
        }

        return $verifypage;
    }

    private function RunAPI() : void {
        $getparams = $_GET['params'] ?? '';
        $params = $getparams ? explode('/', $getparams) : [];
        (new APIResult($params ?? null))
            ->Router()
            ->Execute()
            ->Render();
    }

    public function PostController() : bool {
        $this->newuri = explode("?", $_SERVER['REQUEST_URI'])[0]; // Így a legegyszerűbb levágni az esteleges ismeretlen Get-eket
        $eredmeny = "hiba";

        if(!Controller::CSRFValidator() || !Controller::OriginValidator()) {
            $_SESSION['muvelet'] = $this->page->muvelet ?? null;
            $_SESSION['mixintext'] = "Támadás gyanú! A kért tartalom beküldése sikertelen!";
            $_SESSION['eredmeny'] = $eredmeny;
            unset($_SESSION['csrf_token']);
            return false;
        }
        Controller::CSRFGenerator();
        Controller::POSTCleaner();

        // A tényleges POST lefolytatása
        return $this->page->Post();
    }

    private function AfterPost(bool $dberedmeny) : void {
        if($this->page->redirtarget)
            $this->newuri = $this->page->redirtarget;

        if(isset($this->page->eredmeny))
            $eredmeny = $this->page->eredmeny;
        elseif($dberedmeny)
            $eredmeny = "siker";
        else
            $eredmeny = "hiba";

        $_SESSION['muvelet'] = $this->page->muvelet ?? null;
        $_SESSION['mixintext'] = $this->page->mixintext ?? null;
        $_SESSION['eredmeny'] = $eredmeny;

        if($this->page->redirtarget)
            header("Location: " . $this->newuri);
        exit;
    }
    
    private function PostMessage() : void {
        if(!$_SESSION['mixintext']) {
            $eredmeny = 'sikeres';
            $elemnev = $this->page->cimke;

            if($_SESSION['eredmeny'] == 'hiba')
                $eredmeny = 'nem sikerült';

            if($_SESSION['muvelet'] == 'hozzaad' || $_SESSION['muvelet'] == 'bekuld' || $_SESSION['muvelet'] == 'uj')
                $elemnev = 'Új ' . $this->page->cimke;

            $elemnev = ucfirst($elemnev ?? '');
            @$muvelet = ($_SESSION['muvelet']) ? self::$muveletek[$_SESSION['muvelet']] : self::$muveletek['szerkeszt'];

            define('SWALMIXIN', array(
                "title" => $elemnev . " " . $muvelet . " $eredmeny",
                "icon" => self::$icons[$_SESSION['eredmeny']] ?? "error"
                )
            );
        }
        elseif($_SESSION['mixintext'] != 'no-mixin') {
            define('SWALMIXIN', array(
                "title" => $_SESSION['mixintext'], 
                "icon" => self::$icons[$_SESSION['eredmeny']] ?? "error"
                )
            );
        }

        unset($_SESSION['eredmeny']);
        unset($_SESSION['mixintext']);
        unset($_SESSION['muvelet']);
    }

    public static function PageSelect(?string $type): Page {
        $olvasas = $iras = 0;
        Modulkezelo::Inicializal();
        if(!$type) {
            $class = Modulkezelo::$oldalak['kozlemenyek']['handler'] ?? SinglePage::class;
            $type = 'kozlemenyek';
            $olvasas = Modulkezelo::$oldalak[$type]['olvasas'] ?? 0;
            $iras = Modulkezelo::$oldalak[$type]['iras'] ?? 0;
        }
        elseif (!isset(Modulkezelo::$oldalak[$type])
            || !class_exists(Modulkezelo::$oldalak[$type]['handler'])) {
            if(DEBUG_MODE) {
                if(!isset(Modulkezelo::$oldalak[$type]))
                    $GLOBALS['debugmessages'][] = "A kért URI-hoz (<strong>$type</strong>) nem található ismert oldal egyik manifestben sem!";
                else
                    $GLOBALS['debugmessages'][] = "A kért URI-hoz (<strong>$type</strong>) megadott handler osztály elérési útja hibás: " . Modulkezelo::$oldalak[$type]['handler'];
            }
            $class = SinglePage::class;
            $type = '404';
        }
        else {
            $class = Modulkezelo::$oldalak[$type]['handler'];
            $olvasas = Modulkezelo::$oldalak[$type]['olvasas'] ?? 0;
            $iras = Modulkezelo::$oldalak[$type]['iras'] ?? 0;
        }

        // Oldal inicializálása
        $initclass = new $class($type, $olvasas, $iras);

        if($initclass->ValidView())
            return $initclass;
        else
            return new SinglePage('404');
    }

    public static function RequiredValidator(mixed ...$params) : bool {
        $eredmeny = true;
        foreach ($params as $mezo) {
            if (!array_key_exists($mezo, $_POST) || $_POST[$mezo] === '') {
                $eredmeny = false;
                break;
            }
        }

        return $eredmeny;
    }

    public static function CSRFValidator() : bool {
        if(!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']))
            return false;
        return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    public static function CSRFGenerator() : void {
        if(!isset($_SESSION['csrf_token']) || time() - $_SESSION['tokentime'] > 3600) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['tokentime'] = time();
        }
    }

    public static function OriginValidator() : bool {
        $valid = true;
        if (!empty($_SERVER['HTTP_ORIGIN']) && !empty($_SERVER['HTTP_HOST'])) {
            $originHost = parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST);
            if ($originHost !== false && $originHost !== $_SERVER['HTTP_HOST']) {
                $valid = false;
            }
        }
        return $valid;
    }

    public static function POSTCleaner() : void {
        foreach($_POST as $key => $value) {
            if (!is_array($value))
                $value = trim($value);
            $_POST[$key] = $value;
            if ($value === "NULL" || $value === "")
                $_POST[$key] = null;
        }
    }
}
