<?php
function HtmlHead(&$keywords, &$canonical, &$title, &$ogtype, &$publishtime, &$shareimage, &$robots, &$ablakcim, &$sitedesc, &$cimke)
{
    http_response_code(401);
    $canonical = ROOT_PATH;
    $title .= " - Bejelentkezés szükséges!";
    $publishtime = null;
    $ablakcim = $title;
    $robots = '<meta name="robots" content="noindex, nofollow">';
    $sitedesc = "Ennek az oldalnak a megtekintéséhez be kell jelentkezned!";
    $cimke = "401";
}

function Render(?string $egyedihiba = null) : void
{
    ?><h1>Bejelentkezéshez kötött tartalom</h1>
    <p><?=($egyedihiba) ? $egyedihiba : "Az oldal megtekintése bejelentkezéshez kötött!" ?></p><?php
}