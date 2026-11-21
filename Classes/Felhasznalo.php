<?php

/**
 * 
 * A Felhasznalo objektumot, és a hozzá tartozó attribútumokat, valamint a felhasználón a futásidőben használható metódosukat tartalmazó osztály
 * 
 * 
*/
namespace Infoinventory\Classes;

class Felhasznalo
{
    // {{{ properties
    /**
     * A felhasználó adatbázis azonosítója. Csak nem autentikált felhasználó esetén lehet null
     * @var ?int $uid
     */
    public ?int $uid = null;
    /**
     * A felhasználó felhasználóneve. Csak nem autentikált felhasználó esetén lehet null
     * @var ?string $usernev
     */
    public ?string $usernev;
    /**
     * A felhasználó jelszava. Kizárólag autentikációs céloknál kap értéket. Sosem plain text!!!
     * @var ?string $usernev
     */
    private ?string $jelszo;
    /**
     * A felhasználó anyakönyvszerinti neve.
     * @var ?string $jelszo
     */
    public ?string $nev;
    /**
     * A felhasználó e-mailcíme. Megegyezik az AD mail attribútumával
     * @var ?string $email
     */
    public ?string $email;
    /**
     * A felhasználó első bejelentkezésénék ideje. NEM egyenlő a fiók létrehozásával, mivel a fiók létrejöhet a felhasználó aktív közreműködése nélkül is
     * @var ?string $elsobelepes
     */
    public ?string $elsobelepes;
    /**
     * Az osztály, amelyen a felhasználó dolgozik. Megegyezik az AD Department attribútumával
     * @var ?string $osztaly
     */
    public ?string $osztaly;
    /**
     * A felhasználó telefonszáma. Megegyezik az AD telephoneNumber attribútumával
     * @var ?string $osztaly
     */
    public ?string $telefon;
    /**
     * A felhasználó szervezetének adatbázis ID-ja. A szervezet MINDEN esetben egy speciális célú csoport, amelyet az adatbázisban a sajatszervezet mező true-ra állításával lehet kiválasztani. Minden felhasználó csak egy szervezet célú csoportnak lehet belső tagja
     * @var ?int $szervezet
     */
    public ?int $szervezet = null;
    /**
     * A felhasználó szervezetének a neve. Csak olyankor kap értéket, ha a szervezet nevének van jelentősége
     * @var ?string $szervezetnev
     */
    public ?string $szervezetnev;
    /**
     * A felhasználó beosztásának megnevezése. Megegyezik az AD title mezőjével
     * @var ?string $beosztas
     */
    public ?string $beosztas;
    /**
     * A felhasználóhoz fűzött leírás. Megegyezik az AD description mezőjével
     * @var ?string $leiras
     */
    public ?string $leiras;
    /**
     * A felhasználóhoz fiókjának engedélyezettségi állapota.
     * @var bool $aktiv
     */
    public bool $aktiv;
    /**
     * A felhasználó profilképe. Egyelőre a rendszer képes értelmezni az AD-ból vett BLOB képet is, valamint a profilképek közé feltöltöttet is
     * @var ?string $profilkep
     */
    public ?string $profilkep;
    /**
     * A felhasználóhoz jelenlegi PHP session-jének az adatbázis azonosítója.
     * @var ?int $session_id
     */
    public ?int $session_id;
    /**
     * A felhasználó globáladmin jogát jelző flag. True esetén a felhasználó bármi korlátozás nélkül hozzáfér mindenhez, ezért csak a legvégső esetben kaphatja meg valaki.
     * Minden oldalbetöltéskor ellenőrzésre kerül, és nem módosítható a script-en belül már sehol
     * @var bool $godadmin
     */
    public readonly bool $godadmin;
    /**
     * A felhasználó csoporttagságait tartalmazó tömb. A tömb kulcsai az adott csoportok adatbázis azonosítói
     * @var array $csoporttagsagok
     */
    public array $csoporttagsagok;
    /**
     * A felhasználó írási szintű csoporttagságait tartalmazó tömb. A tömb kulcsai az adott csoportok adatbázis azonosítói
     * @var array $irhatocsoportok
     */
    private array $irhatocsoportok;
    /**
     * A felhasználó moduljogosultságait tartalmazó tömb. A tömb kulcsai az modulok slug-ja, az attribútumok közül pedig az iras és olvasas aminek leginkább van jelentősége
     * @var array $moduljogosultsagok
     */
    public array $moduljogosultsagok;
    // }}}

    public function __construct(?string $usernev = null, ?string $nev = null, ?string $email = null, ?string $elsobelepes = null, ?string $osztaly = null, ?string $telefon = null, ?string $beosztas = null, ?string $leiras = null, ?string $profilkep = null, ?int $aktiv = null)
    {
        $this->usernev = $usernev;
        $this->nev = $nev;
        $this->email = $email;
        $this->elsobelepes = $elsobelepes;
        $this->osztaly = $osztaly;
        $this->telefon = $telefon;
        $this->beosztas = $beosztas;
        $this->leiras = $leiras;
        $this->profilkep = $profilkep;
        $this->aktiv = ($aktiv == 1);
        $this->csoporttagsagok = [0 => ['csoport_id' => 0, 'csoport_nev' => 'Nincs csoporttagság', 'irasjog' => false, 'slug' => 'nincs-csoporttagsag']];
        $this->irhatocsoportok = [0];
    }

    public function SetGodAdmin(?int $dbertek = null) : void {
        $this->godadmin = ($dbertek == 1);
    }

    public function SetCsoportTagsagok(?string $csoporttagsagok, ?string $csoportnevek, ?string $irasjogok, ?string $slugok) : void {
        if($csoporttagsagok == null || $csoportnevek == null || $irasjogok == null) {
            $this->csoporttagsagok = array();
            return;
        }
        $csoportok = Helpers::ConcatToAssocArray(['csoport_id', 'csoport_nev', 'irasjog', 'slug'], $csoporttagsagok, $csoportnevek, $irasjogok, $slugok);
        foreach($csoportok as &$csoport) {
            $csoport['irasjog'] = ($csoport['irasjog'] == 1);
        }
        $this->csoporttagsagok = $csoportok;
    }

    public function SetSajatSzervezet(?int $csoportid, ?string $csoportnev = null) : void {
        $this->szervezet = $csoportid;
        if($csoportnev === null && isset($this->csoporttagsagok) && count($this->csoporttagsagok) > 0) {
            $csoportnev = $this->csoporttagsagok[$csoportid]['csoport_nev'] ?? '';
        }
        $this->szervezetnev = $csoportnev;
    }

    public function GetCsoportIDs() : array {
        return array_keys($this->csoporttagsagok);
    }

    public function GetIrhatoCsoportIDs() : array {
        $this->irhatocsoportok = array_keys(
            array_filter(
                $this->csoporttagsagok,
                fn($tagsag) => $tagsag['irasjog'] == true
            )
        );
        return $this->irhatocsoportok;
    }

    public function GetModulJogLista() : array {
        if(!isset($this->moduljogosultsagok) || count($this->moduljogosultsagok) == 0)
            $this->moduljogosultsagok = ModuleLoader::GetModuleJogosultsagok($this->uid);

        return $this->moduljogosultsagok;
    }
}