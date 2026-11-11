<?php

namespace Infoinventory\Modules\Core;

use Infoinventory\Classes\Controller;
use Infoinventory\Classes\Logging;
use Infoinventory\Classes\MailHandler;
use Infoinventory\Classes\Personalvars;
use Infoinventory\Classes\Felhasznalo;

class Autentikacio extends Page
{
    private ?\LDAP\Connection $ldapconnection;
    protected bool $megerosit = false;
    protected ?string $megerositokod;
    protected string $viewsgyoker = __DIR__ . "/assets/autentikacio/views/";
    protected array $views = [
        'belepes' => 'belepes.php',
        'regisztracio' => 'regisztracio.php',
        'sikertelenreg' => 'sikertelenreg.php',
        'sikeresreg' => 'sikeresreg.php',
        'elfelejtettjelszo' => 'elfelejtettjelszo.php',
        'jelszoigenyelve' => 'jelszoigenyelve.php',
        'regmegerosit' => 'regmegerosit.php',
        'ujjelszo' => 'ujjelszo.php',
        'jelszocsere' => 'jelszocsere.php'
    ];

    public function Router(array $params) : Page {
        $this->validpagemethods = ['sikeres', 'megerosit', 'sikertelen'];
        $params = $this->ParseGet($params);
        $this->muvelet = $this->selectedpage;
        $megerositokod = null;

        if($this->selectedpage == 'regisztracio') {
            $this->jsfiles = ['Modules/Core/assets/autentikacio/js/inputValid.js'];
            if($params['method'] == 'sikeres')
                $this->view = $this->views['sikeresreg'];
            elseif($params['method'] == 'sikertelen')
                $this->aloldal = 'sikertelenreg';
            elseif($params['method'] == 'megerosit') {
                $this->megerosit =  $this->RegisztracioMegerosit($params);
                $this->view = $this->views['regmegerosit'];
            }
        }

        if($this->selectedpage == 'jelszocsere') {
            $this->jsfiles = ['Modules/Core/assets/autentikacio/js/inputValid.js'];
        }

        if($this->selectedpage == 'elfelejtettjelszo') {
            if($params['method'] == 'megerosit') {
                $this->jsfiles = ['Modules/Core/assets/autentikacio/js/inputValid.js'];
                $this->megerosit = $this->ElfelejtettJelszoMegerosit($params['elemid']);
                $this->view = $this->views['ujjelszo'];
            }
            elseif($params['method'] == 'sikeres')
                $this->view = $this->views['jelszoigenyelve'];
        }
        return $this;
    }

    public function HtmlHead() : void {
        $this->canonical = ROOT_PATH . '/' . $this->selectedpage;
        $this->robots = '<meta name="robots" content="noindex, nofollow">';
        if($this->selectedpage == "belepes") {
            $this->title .= " - Bejelentkezés";
            $this->sitedesc = "Jelentkezz be az oldal használatához.";
            $this->cimke = "bejelentkezés";
        }
        elseif($this->selectedpage == "regisztracio") {
            $this->title .= " - Regisztráció";
            $this->sitedesc = "Regisztrálj, hogy saját tartalommal bővíthesd az oldalt!";
            $this->cimke = "regisztráció";
        }
        elseif($this->selectedpage == "jelszoemlekezteto") {
            $this->title .= " - Jelszóemlékeztető";
            $this->sitedesc = "Elfelejtett jelszó";
            $this->cimke = "jelszóemlékeztető";
        }
        $this->ablakcim = $this->title;
        $this->HtmlHeadRender();
    }

    public static function CheckLDAPConnection($host) : bool {
        // Függvény, ami azt ellenőrzi hogy a megadott LDAP szerver elérhető-e
        if(@fsockopen("$host", $GLOBALS['LDAP_PORT'], $errno, $errstr, 30))
            return true;
        else
            return false;
    }

    private function LDAPLogin(string $usernev, string $password) : bool {
        $ldapusername = $usernev . "@" . $GLOBALS['LDAP_DOMAIN']; // Felhasználónév LDAP formátumra konvertálása
        foreach($GLOBALS['LDAP_SERVERS'] as $server)
        {
            if(self::CheckLDAPConnection($server))
            {
                ldap_set_option(null, LDAP_OPT_X_TLS_CACERTFILE, $GLOBALS['LDAP_CAFILE']);
                ldap_set_option(null, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_DEMAND);
                $ldapuri = "ldaps://" . $server . "." . $GLOBALS['LDAP_DOMAIN'] . ":" . $GLOBALS['LDAP_PORT'];
                $ldapconnection = ldap_connect($ldapuri); // LDAP kapcsolat inicializálása

                ldap_set_option($ldapconnection, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($ldapconnection, LDAP_OPT_REFERRALS, 0);

                // LDAP bejelentkezés, mivel nem errort, csak warningot dobhat hiba esetén, el kell nyomni a hibaüzenetet
                @$ldapbind = ldap_bind($ldapconnection, $ldapusername, $password);

                if($ldapbind) {
                    $this->ldapconnection = $ldapconnection;
                    return true;
                }
            }
        }
        $ldapconnection = null;
        return false;
    }



    private function DBLogin(string $usernev, string $password) : ?bool {
        $loginhandler = AutentikacioDB::LoginHandler($usernev);
        if(!$loginhandler)
        {
            // Ha nem létezik a usernév, akkor is futtatunk egy kamu verify-t,
            // hogy a válaszidő ne legyen árulkodó
            $dummy_hash = '$2y$10$usesomesillystringforexample';
            password_verify($_POST['jelszo'], $dummy_hash);
            return null;
        }
        else
        {
            /** @var int $uid */
            $uid = $loginhandler['felhasznalo_id'];
            $password = $loginhandler['jelszo'];
            if($password && password_verify($_POST['jelszo'], $password)) {
                return true;
            }
        }

        return false;
    }

    private function GetLDAPUserAttributes($samaccountname) : ?Felhasznalo {
        $filter = "(&(objectClass=user)(sAMAccountName=$samaccountname))";
        $ldapsearch = ldap_search($this->ldapconnection, $GLOBALS['LDAP_DIR'], $filter); // A felhasználó adatainak lekérése a DC-től
        $ldapresults = ldap_get_entries($this->ldapconnection, $ldapsearch);
        if($ldapresults) {
            @$email = $ldapresults[0]['mail'][0];
            @$nev = $ldapresults[0]['displayname'][0];
            @$osztaly = $ldapresults[0]['department'][0];
            @$leiras = $ldapresults[0]['description'][0];
            //@$szervezet = szervezetValaszto($ldapresults[0]['company'][0]);
            @$telefon = $ldapresults[0]['telephonenumber'][0];
            @$beosztas = $ldapresults[0]['title'][0];
            @$profilkep = $ldapresults[0]['thumbnailphoto'][0];
            $felhasznalo = new Felhasznalo($samaccountname, $nev, $email, $osztaly, $telefon, $beosztas, $leiras, $profilkep);
            ldap_unbind($this->ldapconnection);
            return $felhasznalo;
        }
        return null;
    }

    private function CreateUser(Felhasznalo $felhasznalo, string $hashedpassword) : bool {
        return false;
    }

    private function UpdateUser(Felhasznalo $felhasznalo, string $hashedpassword) : bool {
        return AutentikacioDB::UpdateUser($felhasznalo, $hashedpassword);
    }

    public static function GetUser(int|string $azonosito) : ?Felhasznalo {
        $getuser = AutentikacioDB::GetUser($azonosito);
        $felhasznalo = new Felhasznalo($getuser['usernev'], $getuser['nev'], $getuser['email'], $getuser['osztaly'], $getuser['telefon'], $getuser['beosztas'], $getuser['leiras'], $getuser['profilkep']);
        $felhasznalo->uid = $getuser['uid'];
        return $felhasznalo;
    }

    protected function Belepes() : bool {
        // Felhasználó beléptetése/jogosultsági szintjének ellenőrzése
        $loginsuccess = $ldapsuccess = false;
        //$this->redirtarget = null;
        $this->mixintext = "Hibás felhasználónév, vagy jelszó!";
        $usernev = $_POST['usernev'] ?? null;
        $jelszo = $_POST['jelszo'];
        if($usernev)
        {
            // Bejelentkezési rate limit
            //$kiserletszam = AutentikacioDB::FailedLoginNum($usernev);

            $kiserletszam = 0;
            if($kiserletszam > 5)
            {
                http_response_code(429);
                $this->eredmeny = "letiltva";
                $this->mixintext = "Túl sok hibás bejelentkezési kísérlet! Várj pár percet az újrapróbálkozással!";
            }
            else
            {
                // Először megnézzük, hogy az adatbázisból be lehet-e jelentkezni
                $dbsuccess = $this->DBLogin($usernev, $jelszo);

                // Ha van megadott LDAP szerver a beállításokban, akkor megpróbáljuk azt használni a belépéshez
                if(isset($GLOBALS['LDAP_SERVERS']) && !empty($GLOBALS['LDAP_SERVERS']))
                    $ldapsuccess = $this->LDAPLogin($usernev, $jelszo);

                // Lekezeljük a két bejelentkezési metódus eredményét. Mivel a $loginsuccess false-ra van inicializálva, csak a sikeres esetekkel kell foglalkozni
                if($ldapsuccess) {
                    $felhasznalo = $this->GetLDAPUserAttributes($usernev);
                    $hashedpassword = password_hash($jelszo, PASSWORD_DEFAULT); // A jelszó hash-e az adatbázisban tároláshoz
                    if(is_null($dbsuccess)) {
                        // Az LDAP belépés sikerült, a DB felhasználó még nem létezik. Létrehozni a felhasználót a DB-ben
                        $dbresult = $this->CreateUser($felhasznalo, $hashedpassword);
                        $loginsuccess = true;
                    }
                    else {
                        // Az LDAP belépés sikerült. Frissíteni a felhasználót DB-ben
                        $dbresult = $this->UpdateUser($felhasznalo, $hashedpassword);
                        $loginsuccess = true;
                    }
                }
                elseif($dbsuccess) {
                    // Az LDAP belépés nem sikerült, a DB igen, valószínű nem LDAP felhasználó lépett be, vagy nincs elérhető DC
                    $loginsuccess = true;
                }
            }
            $felhasznalo = $this->GetUser($usernev);

            if(!$loginsuccess) {
                $session_id = Logging::SessionDB();
                Logging::LogFailedLogin($session_id);
                sleep(min($kiserletszam, 5));
            } else {
                $this->eredmeny = "siker";
                $this->mixintext = 'Sikeres bejelentkezés';
                if($this->selectedpage == "belepes")
                    $this->redirtarget = ROOT_PATH;

                //unset($_SESSION);
                $_SESSION['useragent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
                $_SESSION['uid'] = $felhasznalo->uid;

                session_regenerate_id(true);

                $session_id = Logging::SessionDB($felhasznalo->uid);
                Logging::LogLogin($felhasznalo->uid, $session_id);
            }
        }

        return $loginsuccess;
    }

    protected function Regisztracio() : bool {
        $regisztracio = false;
        $this->mixintext = "A regisztráció nem sikerült!";
        $this->redirtarget = ROOT_PATH . "/regisztracio/sikertelen";

        if(!Controller::RequiredValidator('usernev', 'jelszo', 'jelszoismetles', 'email', 'beleegyezes')) {
            $this->mixintext = "Nem töltöttél ki minden kötelező mezőt!";
            return false;
        }

        if($_POST['jelszo'] != $_POST['jelszoismetles']) {
            $this->mixintext = "A jelszó, és megismételt jelszó mező tartalma nem egyezik!";
            return false;
        }

        if(strlen($_POST['jelszo']) < 12) {
            $this->mixintext = "A megadott jelszó hossza túl rövid!";
            return false;
        }

        if(!preg_match('/[a-z]/', $_POST['jelszo']) ||
            !preg_match('/[A-Z]/', $_POST['jelszo']) ||
            !preg_match('/\d/', $_POST['jelszo']) ||
            !preg_match('/[^a-zA-Z\d]/', $_POST['jelszo'])
        ) {
            $this->mixintext = "A megadott jelszó komplexsége nem megfelelő!";
            return false;
        }

        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->mixintext = "Hibás a megadott emailcím formátuma!";
            return false;
        }

        if(AutentikacioDB::UsernameCheck($_POST['usernev'])) {
            $this->mixintext = "A megadott felhasználónév már foglalt!";
            return false;
        }

        if(AutentikacioDB::EmailCheck($_POST['email'])) {
            $this->mixintext = "A megadott email címhez már tartozik felhasználói fiók!";
            return false;
        }

        $verifstring = bin2hex(random_bytes(16));
        $hashedpassword = password_hash($_POST['jelszo'], PASSWORD_DEFAULT);
        $ujuserid = AutentikacioDB::UserRegisztracio($_POST['usernev'], $_POST['email'], $hashedpassword, $verifstring);

        if($ujuserid) {
            $regisztracio = true;
            $this->mixintext = "Sikeres regisztráció!";
            $this->redirtarget = ROOT_PATH . "/regisztracio/sikeres";

            $this->Mailer($_POST['email'], $verifstring, $_POST['usernev']);
        }

        return $regisztracio;
    }

    protected function Elfelejtettjelszo() : bool {
        $ret = false;
        if(isset($_POST['email'])) {
            $this->mixintext = "Új jelszó igényelve!";
            $this->redirtarget = ROOT_PATH . "/elfelejtettjelszo/sikeres";
            $verifstring = bin2hex(random_bytes(16));
            $ret = AutentikacioDB::ElfelejtettJelszo($verifstring, $_POST['email']);
            $this->Mailer($_POST['email'], $verifstring);
        }
        elseif(Controller::RequiredValidator('megerosit', 'jelszo', 'jelszoismetles')) {
            if(!$this->ElfelejtettJelszoMegerosit($_POST['megerosit'])) {
                $this->mixintext = "A megerősítőkód nem egyezik!";
                $this->redirtarget = ROOT_PATH . "/elfelejtettjelszo/sikertelen";
                return false;
            }

            if(!preg_match('/[a-z]/', $_POST['jelszo']) ||
                !preg_match('/[A-Z]/', $_POST['jelszo']) ||
                !preg_match('/\d/', $_POST['jelszo']) ||
                !preg_match('/[^a-zA-Z\d]/', $_POST['jelszo'])
            ) {
                $this->mixintext = "A megadott új jelszó komplexsége nem megfelelő!";
                return false;
            }

            if($_POST['jelszo'] != $_POST['jelszoismetles']) {
                $this->mixintext = "A jelszó, és megismételt jelszó mező tartalma nem egyezik!";
                $this->redirtarget = ROOT_PATH . "/elfelejtettjelszo/sikertelen";
                return false;
            }

            $hashedpassword = password_hash($_POST['jelszo'], PASSWORD_DEFAULT);
            if(AutentikacioDB::ElfelejtettUj($hashedpassword, $_POST['megerosit'])) {
                $this->mixintext = "Új jelszó sikeresen beállítva!";
                $this->redirtarget = ROOT_PATH . "/belepes";
            }
        }

        return $ret;
    }

    protected function Jelszocsere() : bool {
        $this->mixintext = "A jelszó frissítése nem sikerült!";
        $this->redirtarget = $_SERVER['REQUEST_URI'];
        if(!Controller::RequiredValidator('regijelszo', 'ujjelszo', 'ujjelszoismetles')) {
            $this->mixintext = "Nem adtál meg egy kötelező mezőt!";
            return false;
        }

        if($_POST['ujjelszo'] != $_POST['ujjelszoismetles']) {
            $this->mixintext = "A megadott új jelszó, és a megerősítése nem egyezik!";
            return false;
        }

        if(!preg_match('/[a-z]/', $_POST['ujjelszo']) ||
            !preg_match('/[A-Z]/', $_POST['ujjelszo']) ||
            !preg_match('/\d/', $_POST['ujjelszo']) ||
            !preg_match('/[^a-zA-Z\d]/', $_POST['ujjelszo'])
        ) {
            $this->mixintext = "A megadott új jelszó komplexsége nem megfelelő!";
            return false;
        }

        $regijelszo = AutentikacioDB::GetJelszo(Personalvars::$uid);
        if(!password_verify($_POST['regijelszo'], $regijelszo)) {
            $this->mixintext = "Hibásan adtad meg a jelenlegi jelszavadat!";
            return false;
        }

        $hashedpassword = password_hash($_POST['ujjelszo'], PASSWORD_DEFAULT);
        if(password_verify($_POST['ujjelszo'], $regijelszo)) {
            $this->mixintext = "A jelenlegi és új jelszavad megegyezik!";
            return false;
        }

        if(AutentikacioDB::JelszoCsere(Personalvars::$uid, $hashedpassword)) {
            $this->mixintext = "A jelszavad sikeresen megváltoztattad!";
            return true;
        }

        return false;
    }

    private function UserMegerosit(string $megerositokod) : bool {
        $uid = AutentikacioDB::MegerositoKodCheck($megerositokod);
        if($uid)
            return AutentikacioDB::UserMegerosit($uid);
        else
            return false;
    }

    private function ElfelejtettJelszoMegerosit(string $megerositokod) : bool {
        if(AutentikacioDB::ElfelejtettJelszoMegerosit($megerositokod)) {
            $this->megerositokod = $megerositokod;
            return true;
        }
        else
            return false;
    }

    private function RegisztracioMegerosit($params) : bool {
        if(!isset($params['elemid']))
            $this->megerosit = false;
        else
            $this->megerosit = $this->UserMegerosit($params['elemid']);

        if($this->megerosit)
            $this->mixintext = "A felhasználói fiók megerősítése sikeres volt.";
        else
            $this->mixintext = "A felhasználói fiók megerősítése nem sikerült!";

        $this->muvelet = 'Felhasználói fiók megerősítése';
        $this->eredmeny = $this->megerosit;

        return $this->megerosit;
    }

    private function Mailer(string $email, string $verifstring, ?string $usernev = null) : void {
        $mailtargy = $message = null;
        if($usernev) {
            $mailtargy = "Regisztráció megerősítése";
            $message = "<h1>Kedves $usernev!</h1>
                <p>A KaloriaFalo.hu fiókod elkészült. A regisztrációs folyamat befejezéséhez kattints az alábbi linkre</p>
                <p><a href=" . ROOT_PATH . "/regisztracio/megerosit/$verifstring>Fiók aktiválása</a></p>
                <p>vagy másold ki a linket, és illeszd a böngésződ címsorába!</p>
                <p>" . ROOT_PATH . "/regisztracio/megerosit/$verifstring</p>";
        }
        else {
            $usernev = AutentikacioDB::UsernevFromEmail($email);
            if($usernev) {
                $mailtargy = "Elfelejtett jelszó";
                $message = "<h1>Kedves $usernev!</h1>
                    <p>Valaki a nevedben jelszóvisszaállítást kezdeményezett a KalóriaFaló.hu weboldalon.</p>
                    <p>Amennyiben te voltál az, úgy az alábbi linkre kattintva állíthatsz be magadnak új jelszót. A link mostantól egy órán keresztül fog élni.</p>
                    <p><a href=" . ROOT_PATH . "/elfelejtettjelszo/megerosit/$verifstring>Jelszó visszaállítás</a></p>
                    <p>Amennyiben nem te voltál az, levelünket nyugodtan hagyd figyelmen kívül!</p>";
            }
        }

        if($mailtargy) {
            $mail = new MailHandler($message, $email, $mailtargy);
            $mail->Send();
        }
    }
}