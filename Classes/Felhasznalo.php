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
    public ?int $szervezet;
    public ?string $beosztas;
    public ?string $leiras;
    public bool $aktiv;
    public ?string $profilkep;

    public function __construct(?string $usernev = null, ?string $nev = null, ?string $email = null, ?string $osztaly = null, ?string $telefon = null, ?string $beosztas = null, ?string $leiras = null, ?string $profilkep = null, int $aktiv = 1)
    {
        $this->usernev = $usernev;
        //$this->jelszo = $jelszo;
        $this->nev = $nev;
        $this->email = $email;
        //$this->elsobelepes = $elsobelepes;
        $this->osztaly = $osztaly;
        $this->telefon = $telefon;
        $this->beosztas = $beosztas;
        $this->leiras = $leiras;
        $this->profilkep = $profilkep;
        $this->aktiv = ($aktiv == 1);
    }

    public function CsoportTagsagok() : ?array {
        return null;
    }
}