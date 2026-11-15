<?php
function HtmlHead(&$keywords, &$canonical, &$title, &$ogtype, &$publishtime, &$shareimage, &$robots, &$ablakcim, &$sitedesc, &$cimke) : void
{
    $title .= " - Impresszum";
    $ablakcim = $title;
    $sitedesc = "A KalóriaFaló impresszuma";
    $cimke = "impresszum";
}

function Render(?string $egyedihiba = null) : void {
    ?><div class="normalcontent">
        <h1>Impresszum</h1>

        <div>
            <strong>Szolgáltató neve:</strong> KalóriaFaló<br>
            <strong>Weboldal:</strong> https://kaloriafalo.hu
        </div>

        <div>
            <h2>Cégadatok</h2>
            <strong>Székhely:</strong> 6000 Kecskemét, Minta utca 1.<br>
            <strong>Levelezési cím:</strong> 6000 Kecskemét, Minta utca 1.<br>
            <strong>E-mail:</strong> info@kaloriafalo.hu<br>
            <strong>Telefon:</strong> +36 20 123 4567<br><br>

            <strong>Cégjegyzékszám:</strong> 00-00-000000<br>
            <strong>Adószám:</strong> 00000000-0-00<br>
            <strong>Nyilvántartó hatóság:</strong> Minta Megyei Bíróság, mint Cégbíróság
        </div>

        <div>
            <h2>Vezetőség és munkatársak</h2>
            <strong>Cégtulajdonos:</strong> Kovács István<br>
            <strong>Ügyvezető:</strong> Nagy Péter<br>
            <strong>Pénzügyi vezető:</strong> Kiss János<br>
        </div>

        <div>
            <h2>Kapcsolattartók</h2>
            <strong>Média kapcsolattartó:</strong> Tóth Anna<br>
            <strong>E-mail:</strong> media@kaloriafalo.hu<br>
            <strong>Telefon:</strong> +36 30 234 5678<br><br>

            <strong>Ügyfélszolgálat:</strong> Szabó Gábor<br>
            <strong>E-mail:</strong> ugyfelszolgalat@kaloriafalo.hu<br>
            <strong>Telefon:</strong> +36 70 345 6789<br>
        </div>

        <div>
            <h2>Tárhelyszolgáltató</h2>
            <strong>Név:</strong> Példa Hosting Kft.<br>
            <strong>Székhely:</strong> 1111 Budapest, Teszt utca 2.<br>
            <strong>Weboldal:</strong> https://pelda-hosting.hu<br>
            <strong>E-mail:</strong> support@pelda-hosting.hu<br>
            <strong>Telefon:</strong> +36 1 234 5678
        </div>

        <div>
            <h2>Fejlesztés és design</h2>
            A weboldal teljes körű tervezését és fejlesztését (frontend és backend):<br>
            <strong>Király Béla</strong> végezte.
        </div>
    </div><?php
}