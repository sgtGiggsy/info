# Info nyilvántartó

## Ismertető
A rendszer elsődleges célja megfelelő, központilag menedzselt adminisztrációs felületet biztosítani egy szervezet híradó és informatikai részlege számára. Ez a 

## Jelenleg meglévő funkciók
* Közlemények modul
* Hibajegy rendszer
* Munkalapok az elvégzett munkákról
* Egyszerű ToDo modul
* Részletes felhasználókezelés
  * Active Directory-n keresztül történő bejelentkezés
* Eszközök nyilvántartása típusokra lebontva, részletes adatokkal
  * Központilag menedzselhető hálózati eszközök
    * Típus
    * Sorozatszám
    * Telepítés helye
    * IP cím
    * Csatlakoztatott bővítők
    * Portok kapcsolatai (strukturált végpontokkal, vagy direktben más eszközökkel)
    * Állapotelőzmények
    * Szerkesztési előzmények
  * SOHO hálózati eszközök
  * Médiakonverterek
  * Bővítőmodulok hálózati eszközökhöz
  * Nyomtatók
    * Típus
    * Funkciók
* Statikusan kiosztott IP címek nyilvántartása
* Létesítmények nyilvántartása
  * Telephelyek
  * Épületek
  * Helyiségek
  * Strukturált végpontok
  * Rackszekrények
* Raktárkészlet
* Telefonszámok HICOM telefonközpontból történő importálása
* Telefonkönyv
  * Részletes változáskövetés
* Vizsga modul
* Értesítési rendszer

## Rövidtávú tervek
* API írása, ugyanis jelenleg a switchek állapotát ellenőrző segéd program direktben ír az adatbázisba
* Az alapvető MySQL lekérdezésekhez használt függvény osztályba helyezése, és biztonságosabbá tétele

