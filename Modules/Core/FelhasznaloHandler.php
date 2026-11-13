<?php

namespace Infoinventory\Modules\Core;

use Infoinventory\Classes\FeltoltesHandler;
use Infoinventory\Classes\FormBuilder;
use Infoinventory\Classes\Settings;
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
        'szerkesztes' => 'szerkesztes.php'
    ];

    protected array $szintek = [
        1 => 'Felhasználó',
        2 => 'Admin',
        3 => 'Oldal tulajdonos'
    ];

    public function Router(array $params) : Page {
        $this->validpagemethods = ['szerkesztes'];
        $selecteduser = null;
        $params = $this->ParseGet($params);
        if($this->selectedpage == 'felhasznalo' || $this->selectedpage == 'profil') {
            if(!FELHASZNALO->uid)
                return new SinglePage(403);

            if(!$params['elemid'])
                $selecteduser = FELHASZNALO->uid;
            else
                $selecteduser = $params['elemid'];

            $this->felhasznalo = self::GetUser($selecteduser);
            if(!$params['method']) {
                $this->view = $this->views['felhasznalo'];
            }

            if($params['method'] == 'szerkesztes') {
                if(!$this->GetIrasjog($this->felhasznalo->uid, $this->felhasznalo->szervezet)) {
                    return new SinglePage(403);
                }

                $this->muvelet = 'szerkesztes';
                $this->view = $this->views['szerkesztes'];
                $this->form = $this->Form($this->felhasznalo);
            }
        } elseif ($this->selectedpage == "felhasznalok") {
            if(!$this->GetOlvasasjog())
                return new SinglePage(403);

            $this->view = $this->views['felhasznalok'];
            $startindex = 0;
            $elemperoldal = 20;
            $this->LapozasPrepare($startindex, $elemperoldal, $params);
            $this->felhasznalok = $this->LapozasFinalize(FelhasznaloHandlerDB::GetFelhasznalok($startindex, $elemperoldal), $elemperoldal);
        }

        return $this;
    }

    public function HtmlHead() : void {
        if ($this->selectedpage == "felhasznalo" || $this->selectedpage == "profil") {
            $this->title .= " - " . $this->felhasznalo->nev . " profilja";
            $this->sitedesc = "Felhasználói profiloldal";
        } elseif ($this->selectedpage == "felhasznalok") {
            $this->title .= ' - Felhasználók';
            $this->sitedesc = "Felhasználók listája";
        }
        $this->ablakcim = $this->title;
        include(ROOT_DIR . "/template/assets/htmlheader.php");
    }

    protected function Form(Felhasznalo $felhasznalo) : FormBuilder {
        $form = (new FormBuilder())
            ->Hidden('felhasznalo_id')
            ->Text('usernev', 'Felhasználónév', true)
            ->Email('email', 'Email cím', true)
            ->Text('nev', 'Név')
            ->SetValue('felhasznalo_id', $felhasznalo->uid)
            ->SetValue('usernev', $felhasznalo->usernev)
            ->SetValue('email', $felhasznalo->email)
            ->SetValue('nev', $felhasznalo->nev);
        if(FELHASZNALO->godadmin) {
            $form->Checkbox('godadmin', 'Root admin')
                ->SetValue('godadmin', $felhasznalo->godadmin);
        }

        return $form;
    }

    protected function Szerkesztes() : bool {
        $this->redirtarget = ROOT_PATH . "/felhasznalo";
        $pkepid = null;
        if(!isset($_POST['felhasznalo_id'])) {
            $this->mixintext = "Nem lett kiválasztva módosítani kívánt felhasználó!";
            return false;
        }

        if(isset($_POST['szint']) && !Settings::$foadmin) {
            $this->mixintext = "A felhasználói szint megváltoztatására kizárólag a főadminnak van joga!";
            return false;
        }

        if(isset($_POST['allapot']) && !Settings::$admin) {
            $this->mixintext = "Fiókot engedélyezni, vagy letiltani kizárólag adminok tudnak!";
            return false;
        }

        if(!Settings::$admin && $_POST['felhasznalo_id'] != FELHASZNALO->uid) {
            $this->mixintext = "Felhasználók csak a saját fiókjukat szerkeszthetik!";
            return false;
        }

        foreach ($_POST as $key => $value) {
            if($value !== null)
                $_POST[$key] = htmlspecialchars($value);
        }

        if(isset($_FILES['profilkep'])) {
            $profilkep = new FeltoltesHandler($this->mediatypes, 'profilkepek', date('Y'), 'profilkep', FELHASZNALO->uid);
            $profilkep = $profilkep->Feltoltes($_FILES['profilkep']);
            print_r($profilkep);
            if($profilkep['eredmeny']) {
                $pkepid = $profilkep['uploadids'][0];
            }
        }

        $eredmeny = FelhasznaloHandlerDB::FelhasznaloSzerkeszt($_POST, $pkepid);
        if($eredmeny)
            $this->mixintext = "A fiók módosítása sikerült!";
        else
            $this->mixintext = "A fiók módosítása sikertelen!";

        return $eredmeny;
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
            //@$szervezet = szervezetValaszto($ldapresults[0]['company'][0]);
            @$telefon = $ldapresults[0]['telephonenumber'][0];
            @$beosztas = $ldapresults[0]['title'][0];
            @$profilkep = $ldapresults[0]['thumbnailphoto'][0];
            $felhasznalo = new Felhasznalo($samaccountname, $nev, $email, $osztaly, $telefon, $beosztas, $leiras, $profilkep);
            ldap_unbind($ldapconnection);
            return $felhasznalo;
        }
        return null;
    }

    public static function CreateUser(Felhasznalo $felhasznalo, string $hashedpassword) : bool {
        return false;
    }

    public static function UpdateUser(Felhasznalo $felhasznalo, string $hashedpassword) : bool {
        return FelhasznaloHandlerDB::UpdateUser($felhasznalo, $hashedpassword);
    }

    public static function GetUser(int|string $azonosito) : ?Felhasznalo {
        $getuser = FelhasznaloHandlerDB::GetUser($azonosito);
        $felhasznalo = new Felhasznalo($getuser['usernev'], $getuser['nev'], $getuser['email'], $getuser['osztaly'], $getuser['telefon'], $getuser['beosztas'], $getuser['leiras'], $getuser['profilkep']);
        $felhasznalo->uid = $getuser['uid'];
        $felhasznalo->godadmin = ($getuser['godadmin'] == 1);

        $felhasznalo->SetCsoportTagsagok($getuser['csoporttagsagok'], $getuser['csoportnevek'], $getuser['irasjogok']);
        $felhasznalo->SetSajatSzervezet($getuser['sajatszervezet']);

        return $felhasznalo;
    }
}