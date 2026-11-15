<?php
function HtmlHead(&$keywords, &$canonical, &$title, &$ogtype, &$publishtime, &$shareimage, &$robots, &$ablakcim, &$sitedesc, &$cimke) : void
{
    $title .= " - Média ajánlat";
    $ogtype = "website";
    $ablakcim = $title;
    $sitedesc = "A KalóriaFaló média ajánlata";
    $cimke = "media-ajanlat";
}

function Render(?string $egyedihiba = null) : void {
    ?><div class="normalcontent">
        <h1>Média ajánlat</h1>

        <div>
            <strong>Weboldal neve:</strong> KalóriaFaló<br>
            <strong>Weboldal címe:</strong> https://kaloriafalo.hu<br>
            <strong>Téma:</strong> Egészség, táplálkozás, kalóriaszámolás<br>
        </div>

        <div>
            <h2>Bemutatkozás</h2>
            A KalóriaFaló egy dinamikusan fejlődő online platform, amely a tudatos táplálkozás,
            életmód és kalóriakövetés témájában nyújt hasznos információkat. Célunk, hogy
            felhasználóink számára könnyen érthető és gyakorlati segítséget adjunk a mindennapi
            egészséges életvitelhez.
        </div>

        <div>
            <h2>Látogatottsági adatok</h2>
            <ul>
                <li>Havi egyedi látogatók: ~50 000 fő</li>
                <li>Oldalmegtekintések száma: ~200 000 / hó</li>
                <li>Átlagos oldalon töltött idő: 3 perc 20 másodperc</li>
                <li>Visszatérő látogatók aránya: 65%</li>
            </ul>
        </div>

        <div>
            <h2>Célcsoport</h2>
            <ul>
                <li>18–45 év közötti nők és férfiak</li>
                <li>Egészségtudatos életmód iránt érdeklődők</li>
                <li>Fogyókúrázók és sportolók</li>
                <li>Online szolgáltatásokat aktívan használó felhasználók</li>
            </ul>
        </div>

        <div>
            <h2>Hirdetési lehetőségek</h2>
            <table>
                <tr>
                    <th>Felület</th>
                    <th>Méret</th>
                    <th>Elhelyezés</th>
                    <th>Ár (nettó)</th>
                </tr>
                <tr>
                    <td>Banner</td>
                    <td>728x90</td>
                    <td>Fejléc alatt</td>
                    <td>100 000 Ft / hó</td>
                </tr>
                <tr>
                    <td>Sidebar banner</td>
                    <td>300x250</td>
                    <td>Oldalsáv</td>
                    <td>80 000 Ft / hó</td>
                </tr>
                <tr>
                    <td>Natív cikk</td>
                    <td>-</td>
                    <td>Blog szekció</td>
                    <td>150 000 Ft / cikk</td>
                </tr>
                <tr>
                    <td>Kiemelt megjelenés</td>
                    <td>-</td>
                    <td>Főoldal</td>
                    <td>200 000 Ft / hét</td>
                </tr>
            </table>
        </div>

        <div>
            <h2>Egyedi megoldások</h2>
            Egyedi kampányok, szponzorációk, nyereményjátékok és integrált megjelenések
            megvalósítására is van lehetőség. Kérjük, vegye fel velünk a kapcsolatot egyedi
            ajánlatért.
        </div>

        <div>
            <h2>Kapcsolat</h2>
            <strong>Média kapcsolattartó:</strong> Tóth Anna<br>
            <strong>E-mail:</strong> media@kaloriafalo.hu<br>
            <strong>Telefon:</strong> +36 30 234 5678
        </div>

        <div>
            <h2>Megjegyzés</h2>
            Az árak tájékoztató jellegűek, nem minősülnek hivatalos ajánlattételnek.
            A feltüntetett adatok és statisztikák minta jellegűek.
        </div>
    </div><?php
}