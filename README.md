# Info nyilvántartó

## Ismertető
A rendszer elsődleges célja megfelelő, központilag menedzselt adminisztrációs felületet biztosítani egy szervezet híradó és informatikai részlege számára. Az idő előrehaladtával egyéb, üzemeltetéshez nem kapcsolódó funkciók is hozzáadásra kerültek.

## Jelenleg meglévő funkciók
* Jogosultságok elkülönítése
  * Vertikálisan (Szabályozható, hogy ki melyik menüponthoz fér hozzá)
  * Horizontálisan (Szabályozható, hogy melyik menüpontban ki mit írhasson, olvashasson)
* Közlemények modul
* Hibajegy rendszer
  * Állapotkövetés
  * Prioritások (munkavégzők, felettesek jelölik ki, a bejelentő nem látja)
  * Feladatosztás (felettes kijelölheti, hogy ki foglalkozzon vele)
* Munkalapok az elvégzett feladatokról
  * Ki volt a kérő
  * Ki végezte el
  * Mikor volt elvégezve
  * Milyen munka volt elvégezve
  * Milyen eszközöket érintett
  * Munkavégzés helye
* Egyszerű ToDo modul
* Részletes felhasználókezelés
  * Active Directory-n keresztül történő bejelentkezés
  * Active Directory kiszolgáló hiányában adatbázisból történő bejelentkezés
* Eszközök nyilvántartása típusokra lebontva, részletes adatokkal
  * Alapadatok minden eszközről
    * Típus
    * Sorozatszám
    * Telepítés helye (helyiség, raktár, vagy másik eszköz)
    * Beépítési előzmények (hol volt korábban beépítve)
    * Szerkesztési előzmények (mikor ki változtatta, és mit)
    * Megjegyzések
    * Eszköz "egészsége" (működésképtelen, részlegesen hibás, működőképes)
  * Központilag menedzselhető hálózati eszközök
    * IP cím
    * Csatlakoztatott bővítők
    * Portok kapcsolatai (strukturált végpontokkal, vagy direktben más eszközökkel)
    * Állapotelőzmények (mikor volt utoljára elérhető, mikor volt utoljára hiba, stb.)
  * SOHO hálózati eszközök
  * Médiakonverterek
  * Bővítőmodulok hálózati eszközökhöz
  * Nyomtatók
    * Funkciók (nyomtatás, szkennelés, faxolás, stb.)
    * Nyomtatási méret
    * Színes, vagy fekete-fehér
* Statikusan kiosztott IP címek nyilvántartása
  * Mely IP címek vannak használatban, és melyik eszközön
  * Mely IP címek vannak szabadon
  * IP címek előzményei (melyik eszközön voltak korábban)
* Létesítmények nyilvántartása
  * Telephelyek
  * Épületek
  * Helyiségek
  * Strukturált végpontok
  * Rackszekrények
* Raktárkészlet nyilvántartás raktárakra bontva
* Telefonszámok HICOM telefonközpontból történő importálása
  * Nyilvántartás központokra, és központokon belül kártyákra bontva
  * Telefonszámok strukturált végpontokhoz rendelése
  * Telefonszám címkék nyilvántartása
  * Telefonszámhoz rendelt jogosultságok nyilvántartása
  * Telefonszámokhoz rendelt készülékek nyilvántartása
* Telefonkönyv
  * Részletes változáskövetés
  * Részlegekre bontott felelősök a telefonszámok kezelésére
  * Excel export
* Vizsga modul
  * Vizsgán belüli adminisztrátori szintek (admin, felügyelő, vizsgáztató)
  * Vizsgaeredmények nyilvántartása
  * Elkülönített vizsgaperiódusok az időszakonként megismételni szükséges vizsgákhoz
  * Kérdések sikerességének követése
  * Eredmények excel fájlba történő exportálása
* A különböző modellek webes felületen keresztül történő szerkesztése
* Bugreport, hogy a felhasználók jelenteni tudják az oldal működésével kapcsolatos hibákat
* Értesítési rendszer
* Súgó a szerkesztési funkciókhoz
* Felhasználónként változtatható színséma

## Rövidtávú tervek
* API írása, ugyanis jelenleg a switchek állapotát ellenőrző segéd program direktben ír az adatbázisba
* Az alapvető MySQL lekérdezésekhez használt függvény osztályba helyezése, és biztonságosabbá tétele