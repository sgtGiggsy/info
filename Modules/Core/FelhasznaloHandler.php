<?php

namespace Infoinventory\Modules\Core;

use Infoinventory\Classes\FeltoltesHandler;
use Infoinventory\Classes\FormBuilder;
use Infoinventory\Classes\Felhasznalo;
use Infoinventory\Classes\Modulkezelo;

class FelhasznaloHandler extends Page
{
    protected Felhasznalo $felhasznalo;
    protected array $felhasznalok = [];
    protected FormBuilder $form;
    protected string $viewsgyoker = __DIR__ . "/assets/felhasznalo/views/";
    protected array $views = [
        'felhasznalo' => 'felhasznalo.php',
        'felhasznalok' => 'felhasznalok.php',
        'profil' => 'felhasznalo.php',
        'uj' => 'uj.php',
        'szerkesztes' => 'szerkesztes.php'
    ];

    public function Router(array $params) : Page {
        $this->validpagemethods = ['szerkesztes', 'uj'];
        $selecteduser = null;
        $params = $this->ParseGet($params);
        if($this->selectedpage == 'felhasznalo' || $this->selectedpage == 'profil') {
            if(!FELHASZNALO->uid)
                return new SinglePage(403);

            if($params['method'] == 'uj') {
                if(Modulkezelo::ModulJogosultsag('felhasznalo')['iras'] < 2) {
                    return new SinglePage(403);
                }

                $this->jsfiles = ['Modules/Core/assets/_kozos/js/inputValid.js'];
                $this->muvelet = 'uj';
                $this->view = $this->views['uj'];
                $this->form = $this->Form();
                return $this;
            }

            if(!$params['elemid'])
                $selecteduser = FELHASZNALO->uid;
            else
                $selecteduser = $params['elemid'];

            $this->felhasznalo = self::GetUser($selecteduser);
            $this->irasjog = $this->GetIrasjog($this->felhasznalo->uid, $this->felhasznalo->szervezet);

            if(!$params['method']) {
                $this->view = $this->views['felhasznalo'];
            }

            if($params['method'] == 'szerkesztes') {
                if(!$this->irasjog) {
                    return new SinglePage(403);
                }

                $this->muvelet = 'szerkesztes';
                $this->view = $this->views['szerkesztes'];
                $this->form = $this->Form($this->felhasznalo);
            }
        } elseif ($this->selectedpage == "felhasznalok") {
            if(!FELHASZNALO->uid)
                return new SinglePage(403);

            $this->view = $this->views['felhasznalok'];
            $startindex = 0;
            $elemperoldal = 20;
            $this->LapozasPrepare($startindex, $elemperoldal, $params);
            $this->felhasznalok = $this->LapozasFinalize($this->GetUserList(), $elemperoldal);
        }

        return $this;
    }

    public function HtmlHead() : void {
        if (($this->selectedpage == "felhasznalo" || $this->selectedpage == "profil") && $this->muvelet != 'uj') {
            $this->title .= " - " . $this->felhasznalo->nev . " profilja";
            $this->sitedesc = "Felhasználói profiloldal";
        } elseif ($this->selectedpage == "felhasznalo" && $this->muvelet == 'uj') {
            $this->title .= " - Új felhasználó felvétele";
            $this->sitedesc = "Új felhasználó felvétele";
        } elseif ($this->selectedpage == "felhasznalo" && $this->muvelet == 'szerkesztes') {
            $this->title .= " - " . $this->felhasznalo->nev . " profiljának szerkesztése";
            $this->sitedesc = "Felhasználó szerkesztése";
        } elseif ($this->selectedpage == "felhasznalok") {
            $this->title .= ' - Felhasználók';
            $this->sitedesc = "Felhasználók listája";
        }
        $this->ablakcim = $this->title;
        include(ROOT_DIR . "/template/assets/htmlheader.php");
    }

    protected function Form(?Felhasznalo $felhasznalo = null) : FormBuilder {
        $form = (new FormBuilder())
            ->Hidden('felhasznalo_id')
            ->Text('usernev', 'Felhasználónév', true)
            ->Text('nev', 'Név')
            ->Text('descript', 'Leírás')
            ->Text('telefon', 'Telefon')
            ->Email('email', 'Email cím')
            ->Select('szervezet', 'Szervezet', $this->GetCsoportList(), true)
            ->Text('osztaly', 'Osztály')
            ->Text('beosztas', 'Beosztás')
            ->Checkbox('aktiv', 'Fiók engedélyezve')
            ->SetHelperText('usernev', 'A felhasználó bejelentkezéshez használt felhasználóneve. Amennyiben AD felhasználót veszünk fel előre, úgy pontosan kell megadni, máskülönben az AD-val történő szinkronizálás során a rendszer nem fogja tudni beazonosítani. Ha nem AD felhasználót veszünk fel, úgy a felhasználónév tetszőleges lehet.')
            ->SetHelperText('nev', 'A felhasználó megjelenő neve.')
            ->SetHelperText('telefon', 'A felhasználó munkahelyi telefonszáma.')
            ->SetHelperText('email', 'A felhasználó e-mail címe.')
            ->SetHelperText('szervezet', 'A felhasználó szervezete. A szervezet a felhasználó csoporttagságai közül az a csoport, amelynek a felhasználó a szervezetszerű dolgozója. A szervezet kiválasztása kötelező, mivel a felhasználó jogosultságai a szervezetéhez tartozó csoporttagságok alapján kerülnek meghatározásra.')
            ->SetHelperText('osztaly', 'A felhasználó osztálya.')
            ->SetHelperText('beosztas', 'A felhasználó beosztása.')
            ->SetHelperText('descript', 'A felhasználóhoz tartozó rövid leírás.')
            ->SetHelperText('aktiv', 'A felhasználó fiókjának engedélyezett állapota. Amennyiben a fiók le van tiltva, úgy a felhasználó nem tud bejelentkezni a rendszerbe.');
        if($felhasznalo) {
            $form->SetValue('felhasznalo_id', $felhasznalo->uid)
                ->SetValue('usernev', $felhasznalo->usernev)
                ->SetValue('email', $felhasznalo->email)
                ->SetValue('nev', $felhasznalo->nev)
                ->SetValue('telefon', $felhasznalo->telefon)
                ->SetValue('beosztas', $felhasznalo->beosztas)
                ->SetValue('descript', $felhasznalo->leiras)
                ->SetValue('osztaly', $felhasznalo->osztaly)
                ->SetValue('szervezet', $felhasznalo->szervezet)
                ->SetValue('aktiv', $felhasznalo->aktiv);
        }
        if(FELHASZNALO->godadmin) {
            $form->Checkbox('godadmin', 'Root adminisztrátor')
            ->SetHelperText('godadmin', 'A root adminisztrátorok a rendszer minden részéhez hozzáférnek, és minden jogosultságot megkapnak. Csak a root adminisztrátorok tudnak más felhasználókat root adminisztrátorrá tenni.');
            if($felhasznalo) {
                $form->SetValue('godadmin', $felhasznalo->godadmin);
            }
        }

        return $form;
    }

    protected function Szerkesztes() : bool {
        $this->redirtarget = ROOT_PATH . "/felhasznalo";
        if(!isset($_POST['felhasznalo_id'])) {
            $this->mixintext = "Nem lett kiválasztva módosítani kívánt felhasználó!";
            return false;
        }

        if(isset($_POST['godadmin']) && !FELHASZNALO->godadmin) {
            $this->mixintext = "Root adminisztrátori jogosultság megadása csak a root adminisztrátorok számára engedélyezett!";
            return false;
        }

        if(isset($_POST['aktiv']) && !$this->GetIrasjog($_POST['felhasznalo_id'])) {
            $this->mixintext = "Fiókot engedélyezni, vagy letiltani kizárólag adminok tudnak!";
            return false;
        }

        if(!$this->GetIrasjog($_POST['felhasznalo_id']) && $_POST['felhasznalo_id'] != FELHASZNALO->uid) {
            $this->mixintext = "Felhasználók csak a saját fiókjukat szerkeszthetik!";
            return false;
        }

        foreach ($_POST as $key => $value) {
            if($value !== null)
                $_POST[$key] = htmlspecialchars($value);
        }

        $pkepid = $this->UploadProfilePicture();
        $eredmeny = FelhasznaloHandlerDB::FelhasznaloSzerkeszt($_POST, $pkepid);
        if($eredmeny) {
            $this->mixintext = "A fiók módosítása sikerült!";
            if($_POST['szervezet'] && $_POST['szervezet'] != $this->felhasznalo->szervezet) {
                $this->CsoportbolTorol($_POST['felhasznalo_id']);
                $csoport = $this->CsoporthozAd($_POST['felhasznalo_id'], $_POST['szervezet']);
                if($csoport) {
                    $this->mixintext .= "<br>A felhasználó hozzáadása a szervezethez sikerült!";
                }
                else {
                    $this->mixintext .= "<br>A felhasználó hozzáadása a szervezethez sikertelen!";
                }
            }
        }
        else
            $this->mixintext = "A fiók módosítása sikertelen!";

        return $eredmeny;
    }

    protected function Uj() : bool {
        if(!isset($_POST['usernev'])) {
            $this->mixintext = "Nem lett megadva a felhasználónév!";
            return false;
        }

        foreach ($_POST as $key => $value) {
            if($value !== null)
                $_POST[$key] = htmlspecialchars($value);
        }

        $pkepid = $this->UploadProfilePicture();
        $hashedpassword = null;
        if(isset($_POST['jelszo'])) {
            $hashedpassword = password_hash($_POST['jelszo'], PASSWORD_DEFAULT);
        }
        $eredmeny = FelhasznaloHandlerDB::UjFelhasznalo($_POST, $hashedpassword, $pkepid);

        if($eredmeny) {
            $this->mixintext = "A fiók létrehozása sikerült!";
            if($_POST['szervezet']) {
                $csoport = $this->CsoporthozAd($_POST['felhasznalo_id'], $_POST['szervezet']);
                if($csoport) {
                    $this->mixintext .= "<br>A felhasználó hozzáadása a szervezethez sikerült!";
                }
            } else {
                $this->mixintext .= "<br>A felhasználó hozzáadása a szervezethez sikertelen!";
            }
            $this->redirtarget = ROOT_PATH . "/felhasznalo/" . $_POST['usernev'];
        }
        else
            $this->mixintext = "A fiók létrehozása sikertelen!";

        return $eredmeny;
    }

    private function UploadProfilePicture() : ?int {
        if(isset($_FILES['profilkep'])) {
            $profilkep = new FeltoltesHandler($this->mediatypes, 'profilkepek', date('Y'), 'profilkep', FELHASZNALO->uid);
            $profilkep = $profilkep->Feltoltes($_FILES['profilkep']);
            if($profilkep['eredmeny']) {
                return $profilkep['uploadids'][0];
            }
        }
        return null;
    }

    private function CsoporthozAd(int $felhasznaloid, int $csoportid) : bool {
        return FelhasznaloHandlerDB::CsoporthozAd($felhasznaloid, $csoportid);
    }

    private function CsoportbolTorol(int $felhasznaloid) : bool {
        return FelhasznaloHandlerDB::CsoportbolTorol($felhasznaloid);
    }

    public static function GetLDAPUserAttributes(string $samaccountname, ?\LDAP\Connection $ldapconnection) : ?Felhasznalo {
        $filter = "(&(objectClass=user)(sAMAccountName=$samaccountname))";
        $ldapsearch = ldap_search($ldapconnection, $GLOBALS['LDAP_DIR'], $filter); // A felhasználó adatainak lekérése a DC-től
        $ldapresults = ldap_get_entries($ldapconnection, $ldapsearch);
        if($ldapresults) {
            @$email = $ldapresults[0]['mail'][0];
            @$nev = $ldapresults[0]['displayname'][0];
            @$osztaly = $ldapresults[0]['department'][0];
            @$leiras = $ldapresults[0]['description'][0];
            @$szervezet = self::SzervezetValaszto($ldapresults[0]['company'][0]);
            @$telefon = $ldapresults[0]['telephonenumber'][0];
            @$beosztas = $ldapresults[0]['title'][0];
            @$profilkep = $ldapresults[0]['thumbnailphoto'][0];
            $felhasznalo = new Felhasznalo($samaccountname, $nev, $email, null, $osztaly, $telefon, $beosztas, $leiras, $profilkep);
            $felhasznalo->SetSajatSzervezet($szervezet);
            ldap_unbind($ldapconnection);
            return $felhasznalo;
        }
        return null;
    }

    public static function CreateUserFromLDAP(Felhasznalo $felhasznalo, string $hashedpassword) : bool {
        return FelhasznaloHandlerDB::CreateUserFromLDAP($felhasznalo, $hashedpassword);
    }

    public static function UpdateUserFromLDAP(Felhasznalo $felhasznalo, string $hashedpassword) : bool {
        return FelhasznaloHandlerDB::UpdateUserFromLDAP($felhasznalo, $hashedpassword);
    }

    public static function GetUser(int|string $azonosito) : ?Felhasznalo {
        $getuser = FelhasznaloHandlerDB::GetUser($azonosito);
        $felhasznalo = new Felhasznalo($getuser['usernev'], $getuser['nev'], $getuser['email'], $getuser['elsobelepes'], $getuser['osztaly'], $getuser['telefon'], $getuser['beosztas'], $getuser['leiras'], $getuser['profilkep'], $getuser['aktiv']);
        $felhasznalo->uid = $getuser['uid'];
        $felhasznalo->godadmin = ($getuser['godadmin'] == 1);

        $felhasznalo->SetCsoportTagsagok($getuser['csoporttagsagok'], $getuser['csoportnevek'], $getuser['irasjogok']);
        $felhasznalo->SetSajatSzervezet($getuser['sajatszervezet'], $getuser['sajatszervezetnev']);

        return $felhasznalo;
    }

    private function GetUserList() : array {
        $felhasznalok = [];
        foreach(FelhasznaloHandlerDB::GetUserList() as $felhasznalo) {
            $temp = new Felhasznalo($felhasznalo['usernev'], $felhasznalo['nev'], $felhasznalo['email'], $felhasznalo['elsobelepes'], $felhasznalo['osztaly'], $felhasznalo['telefon'], $felhasznalo['beosztas'], $felhasznalo['leiras'], $felhasznalo['profilkep'], $felhasznalo['aktiv']);
            $temp->uid = $felhasznalo['uid'];
            $temp->SetSajatSzervezet($felhasznalo['sajatszervezet'], $felhasznalo['sajatszervezetnev']);
            $felhasznalok[] = $temp;
        }
        return $felhasznalok;
    }

    private function GetCsoportList() : array {
        return FelhasznaloHandlerDB::GetCsoportList();
    }

    private static function SzervezetValaszto(string $needle) {
        return FelhasznaloHandlerDB::SzervezetValaszto($needle);
    }
}