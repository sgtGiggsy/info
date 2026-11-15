<?php
function HtmlHead(&$keywords, &$canonical, &$title, &$ogtype, &$publishtime, &$shareimage, &$robots, &$ablakcim, &$sitedesc, &$cimke) : void
{
    $canonical = ROOT_PATH;
    $title .= " - Főoldal";
    $ablakcim = $title;
    $sitedesc = "A keresett tartalom nem létezik vagy eltávolították.";
    $cimke = "főoldal";
}

function Render(?string $egyedihiba = null) : void
{
    ?><h1>Főoldal</h1>
    <p>Itt valami nagyszerű dolog készül!</p><?php
}