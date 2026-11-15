<?php
function HtmlHead(&$keywords, &$canonical, &$title, &$ogtype, &$publishtime, &$shareimage, &$robots, &$ablakcim, &$sitedesc, &$cimke) : void
{
    $title .= " - Kapcsolat";
    $ablakcim = $title;
    $sitedesc = "A Kalóriafaló kapcsolati adatlapja";
    $cimke = "kapcsolat";
}

function Render(?string $egyedihiba = null) : void {
    ?><div class="normalcontent szovegtartalom">
        <h1>Kapcsolat</h1>
        <div>
            <p><strong>E-mail:</strong> kapcsolat@kaloriafalo.hu<br>
            <strong>Cím:</strong> 6000 Kecskemét, Minta utca 1.<br>
            <strong>Telefonszám:</strong> +36 20 123 4567 (munkanapokon, 9-17 óra között elérhető)</p>
        </div>
    </div><?php
}