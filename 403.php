<?php
function HtmlHead(&$keywords, &$canonical, &$title, &$ogtype, &$publishtime, &$shareimage, &$robots, &$ablakcim, &$sitedesc, &$cimke)
{
    http_response_code(403);
    $canonical = ROOT_PATH;
    $title .= " - Hozzáférés megtagadva";
    $publishtime = null;
    $ablakcim = $title;
    $robots = '<meta name="robots" content="noindex, nofollow">';
    $sitedesc = "Ehhez az oldalhoz nincs jogosultságod.";
    $cimke = "403";
}

function Render(?string $egyedihiba = null) : void
{
    ?><h1>403 - Hozzáférés megtagadva</h1>
    <p><?=($egyedihiba) ? $egyedihiba : "Ehhez az oldalhoz nincs jogosultságod." ?></p><?php
}