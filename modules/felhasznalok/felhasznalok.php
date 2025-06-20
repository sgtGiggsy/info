<?php
if(!$csoportolvas)
{
    getPermissionError();
}
else
{
    $adattabla = "felhasznalok";
    $oldalnev = "felhasznalok";
    $oldalcim = "Felhasználók listája";
    $table = "modules/felhasznalok/includes/felhasznalotable";
    $javascriptfiles[] = "modules/felhasznalok/includes/passwordgen.js";

    $where = $csoportwhere = $keres = null;
    $enablekeres = true;

    if(isset($_GET['kereses']))
    {
        $keres = $_GET['kereses'];
        $where = "WHERE felhasznalok.nev LIKE '%$keres%' OR felhasznalonev LIKE '%$keres%' OR osztaly LIKE '%$keres%'";
        $keres = "?kereses=" . $keres;
    }

    if(isset($_GET['telefontoad']))
    {
        $table = "modules/felhasznalok/includes/telefontoad";
        $keres = "?telefontoad";
        if($_GET['telefontoad'] == 'notnull')
            $keres .= '=notnull';
    }

    if(!$where)
    {
        $where = "WHERE felhasznalok.aktiv = 1";
    }

    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => "szervezet",                        // A szűrés típusa, null = mindkettő, szervezet = szervezet, telephely = telephely
            'and' => false,                          // Kerüljön-e AND a parancs elejére
            'szervezetelo' => "felhasznalok",                  // A tábla neve, ahonnan az szervezet neve jön
            'telephelyelo' => null,           // A tábla neve, ahonnan a telephely neve jön
            'szervezetnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az szervezetszűréshez
            'telephelynull' => true,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'szervezetmegnevezes' => "szervezet"    // Az szervezetot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
        $csoportwhere = "AND felhasznalok.aktiv = 1 AND $csoportwhere";
    }

    if($csoportir) 
    {
        ?><div class="szerkgombsor">
            <button type="button" onclick="location.href='<?=$RootPath?>/felhasznalo?action=addnew'">Új felhasználó</button>
            <button type="button" onclick="location.href='<?=$RootPath?>/felhasznalo?action=sync'">Meglévő felhasználók AD-val szinkronizálása</button>
            <button type="button" onclick="location.href='<?=$RootPath?>/felhasznalo?action=syncou'">Kiválasztott OU szinkronizálása</button>
            <button type="button" onclick="location.href='<?=$RootPath?>/felhasznalok?telefontoad'">Telefonkönyv összevetése AD-val</button>
            <button type="button" onclick="location.href='<?=$RootPath?>/felhasznalok?telefontoad=notnull'">Telefonkönyv összevetése AD-val (csak megtaláltak)</button>
            <button type="button" onclick="generatePassword()">Jelszó generálása</button>
        </div><?php
    }

    include('././templates/lapozas.tpl.php');

}
?>