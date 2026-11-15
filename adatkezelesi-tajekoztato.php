<?php
function HtmlHead(&$keywords, &$canonical, &$title, &$ogtype, &$publishtime, &$shareimage, &$robots, &$ablakcim, &$sitedesc, &$cimke) : void
{
    $title .= " - Adatkezelési tájékoztató";
    $ablakcim = $title;
    $sitedesc = "A Kalóriafaló adatkezelési tájékoztatója";
    $cimke = "adatkezelesi-tajekoztato";
}

function Render(?string $egyedihiba = null) : void {
    ?><div class="normalcontent szovegtartalom">
        <h1>Kalóriafaló – Adatkezelési tájékoztató</h1>
        <h2>1. Az adatkezelő adatai</h2>
        <p>A weboldal üzemeltetője:</p>
        <strong>Kalóriafaló</strong>
        <p>Kapcsolattartás:</p>
        <p>info@kaloriafalo.hu</p>
        <p>---</p>

        <h2>2. Az adatkezelés célja</h2>
        <p>A Kalóriafaló weboldal a felhasználói adatok kezelését az alábbi célokból végzi:</p>
        <ul>
            <li>felhasználói fiók létrehozása és kezelése</li>
            <li>szolgáltatások biztosítása (pl. kalória számítás, profil funkciók)</li>
            <li>felhasználói azonosítás</li>
            <li>technikai működés biztosítása</li>
            <li>esetleges hibák kezelése és naplózása</li>
        </ul>
        <p>---</p>

        <h2>3. A kezelt adatok köre</h2>
        <p>A rendszer az alábbi adatokat kezelheti:</p>
        <li>felhasználónév</li>
        <li>e-mail cím</li>
        <li>jelszó (titkosítva / hash-elve tárolva)</li>
        <li>IP-cím (biztonsági és naplózási célból)</li>
        <li>opcionális felhasználói adatok (pl. profiladatok)</li>
        <p>---</p>

        <h2> 4. Az adatkezelés jogalapja</h2>
        <p>Az adatkezelés jogalapja:</p>
        <li> az érintett hozzájárulása (GDPR 6. cikk (1) a) bekezdés)</li>
        <li> a szerződés teljesítése (szolgáltatás biztosítása)</li>
        <p>---</p>

        <h2>5. Az adatok tárolásának időtartama</h2>
        <p>A személyes adatokat:</p>
        <li>a felhasználói fiók fennállásáig, vagy</li>
        <li>a felhasználó törlési kérelméig</li>
        <p>tároljuk, kivéve a jogszabály által előírt kötelező megőrzési eseteket.</p>
        <p>---</p>

        <h2>6. Adattovábbítás</h2>
        <p>A Kalóriafaló az adatokat harmadik félnek alapértelmezetten <strong>nem adja át</strong>, kivéve:</p>
        <li>jogi kötelezettség esetén</li>
        <li>hatósági megkeresésre</li>
        <p>---</p>

        <h2> 7. Adatbiztonság</h2>
        <p>Az adatok védelme érdekében:</p>
        <li>jelszavak hash-elve kerülnek tárolásra</li>
        <li>a rendszer hozzáférése korlátozott</li>
        <li>technikai és szervezési védelmi intézkedések vannak érvényben</li>
        <p>---</p>

        <h2>8. A felhasználó jogai</h2>
        <p>A felhasználó jogosult:</p>
        <li>tájékoztatást kérni az adatairól</li>
        <li>adatainak helyesbítésére</li>
        <li>adatainak törlésére (“elfeledtetés joga”)</li>
        <li>adatkezelés korlátozására</li>
        <li>hozzájárulás visszavonására</li>
        <p>---</p>

        <h2>9. Kapcsolat</h2>
        <p>Adatkezeléssel kapcsolatos kérdések esetén:</p>
        <p>adatkezeles@kaloriafalo.hu</p>
    </div><?php
}