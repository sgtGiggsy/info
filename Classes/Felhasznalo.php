<?php

namespace Infoinventory\Classes;

class Felhasznalo
{
    public ?int $uid = null;
    public ?string $usernev;
    public ?string $jelszo;
    public ?string $nev;
    public ?string $email;
    public ?string $elsobelepes;
    public ?string $osztaly;
    public ?string $telefon;
    public ?int $szervezet = null;
    public ?string $szervezetnev;
    public ?string $beosztas;
    public ?string $leiras;
    public bool $aktiv;
    public ?string $profilkep;
    public ?int $session_id;
    public bool $godadmin;
    public array $csoporttagsagok;
    private array $moduljogosultsagok;

    public function __construct(?string $usernev = null, ?string $nev = null, ?string $email = null, ?string $elsobelepes = null, ?string $osztaly = null, ?string $telefon = null, ?string $beosztas = null, ?string $leiras = null, ?string $profilkep = null, ?int $aktiv = null)
    {
        $this->usernev = $usernev;
        //$this->jelszo = $jelszo;
        $this->nev = $nev;
        $this->email = $email;
        $this->elsobelepes = $elsobelepes;
        $this->osztaly = $osztaly;
        $this->telefon = $telefon;
        $this->beosztas = $beosztas;
        $this->leiras = $leiras;
        $this->profilkep = $profilkep;
        $this->aktiv = ($aktiv == 1);
    }

    public function SetCsoportTagsagok(?string $csoporttagsagok, ?string $csoportnevek, ?string $irasjogok) : void {
        if($csoporttagsagok == null || $csoportnevek == null || $irasjogok == null) {
            $this->csoporttagsagok = array();
            return;
        }
        $csoportok = Helpers::ConcatToAssocArray(['csoport_id', 'csoport_nev', 'irasjog'], $csoporttagsagok, $csoportnevek, $irasjogok);
        foreach($csoportok as &$csoport) {
            $csoport['irasjog'] = ($csoport['irasjog'] == 1);
        }
        $this->csoporttagsagok = $csoportok;
    }

    public function SetSajatSzervezet(int $csoportid, ?string $csoportnev = null) : void {
        $this->szervezet = $csoportid;
        if($csoportnev === null && isset($this->csoporttagsagok)) {
            $csoportnev = $this->csoporttagsagok[$csoportid]['csoport_nev'] ?? '';
        }
        $this->szervezetnev = $csoportnev;
    }

    public function GetCsoportIDs() : array {
        return array_keys($this->csoporttagsagok);
    }

    public function GetIrhatoCsoportIDs() : array {
        return array_keys(
            array_filter(
                $this->csoporttagsagok,
                fn($tagsag) => $tagsag['irasjog'] == true
            )
        );
    }

    public function GetModulJogosultsagok() : array {
        if(!isset($this->moduljogosultsagok)) {
            $this->moduljogosultsagok = Modulkezelo::GetAuthorizedModules($this->uid);
        }
        return $this->moduljogosultsagok;
    }
}