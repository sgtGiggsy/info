<?php
$pages = array(
// Alap oldalak //
    '/' => array('szoveg' => 'Főoldal', 'fajl' => 'fooldal'),
    'fooldal' => array('szoveg' => 'Főoldal', 'fajl' => 'fooldal'),
    'belepes' => array('szoveg' => 'Belépés', 'fajl' => 'belepes'),

// Gyüjtő oldalak //
    'vizsgalista' => array('szoveg' => 'Kitöltött vizsgák', 'fajl' => 'vizsgalista'),
    'megkezdett' => array('szoveg' => 'Megkezdett vizsgák', 'fajl' => 'megkezdett'),
    'hibajelzesek' => array('szoveg' => 'Hibajelzések', 'fajl' => 'hibajelzesek'),
    'felhasznalok' => array('szoveg' => 'Felhasználók listája', 'fajl' => 'felhasznalok'),
    'bejelentkezesek' => array('szoveg' => 'Bejelentkezések', 'fajl' => 'bejelentkezesek'),

// Egyes oldalak //
    'vizsga' => array('szoveg' => 'Vizsga', 'fajl' => 'vizsga'),
    'kerdes' => array('szoveg' => 'Kérdés', 'fajl' => 'kerdes'),
    'hiba' => array('szoveg' => 'Hibajelzés küldése', 'fajl' => 'hiba'),
    'felhasznalo' => array('szoveg' => 'Személyes oldal', 'fajl' => 'felhasznalo'),
    'vizsgaigazolas' => array('szoveg' => 'Vizsgaigazolás', 'fajl' => 'vizsgaigazolas'),
    'vizsgareszletezo' => array('szoveg' => 'Vizsgaigarészletező', 'fajl' => 'vizsgareszletezo'),
    'bejelentkezesihibak' => array('szoveg' => 'Bejelentkezési hibák', 'fajl' => 'bejelentkezesihibak'),

// Admin oldalak //
    'kerdeslista' => array('szoveg' => 'Kérdések listája', 'fajl' => 'kerdeslista'), 
    'kerdesszerkeszt' => array('szoveg' => 'Kérdés szerkesztése', 'fajl' => 'kerdesszerkeszt'),
    'beallitasok' => array('szoveg' => 'Beállítások szerkesztése', 'fajl' => 'beallitasok'),
    'hibaszerk' => array('szoveg' => 'Hiba vizsgálata', 'fajl' => 'hibaszerk'),
    'felhasznaloszerkeszt' => array('szoveg' => 'Felhasználó szerkesztése', 'fajl' => 'felhasznaloszerkeszt'),

// Adatbázis funkciók //
    'kerdesdb' => array('szoveg' => 'Kérdésszerk', 'fajl' => 'db/kerdesdb'),
    'beallitasdb' => array('szoveg' => 'Főoldalszerk', 'fajl' => 'db/beallitasdb'),
    'vizsgadb' => array('szoveg' => 'Vizsgaszerk', 'fajl' => 'db/vizsgadb'),
    'valaszdb' => array('szoveg' => 'Valaszszerk', 'fajl' => 'db/valaszdb'),
    'hibadb' => array('szoveg' => 'Hibaszerk', 'fajl' => 'db/hibadb'),
    'felhasznalodb' => array('szoveg' => 'Felhasznaloszerk', 'fajl' => 'db/felhasznalodb'),

// Egyéb includes-ok //
    'exportexcel' => array('szoveg' => 'Exportálás XLSX-be', 'fajl' => 'exportexcel'),
    'login' => array('szoveg' => 'Belépés', 'fajl' => 'includes/login')
);