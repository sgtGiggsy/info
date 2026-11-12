<?php

namespace Infoinventory\Classes;

class Helpers
{
    public static function TimeStampToDate(int $timestamp) {
        if($timestamp)
        {
            return date('Y M j.', strtotime($timestamp));
        }
        else
        {
            return null;
        }
    }

    public static function TimeStampToDateTimeLocal(int $timestamp) {
        if($timestamp)
        {
            return str_replace(" ", "T", $timestamp);
        }
        else
        {
            return null;
        }
    }

    public static function DateTimeLocalToTimeStamp($datetimelocal) {
        if($datetimelocal)
        {
            return str_replace("T", " ", $datetimelocal);
        }
        else
        {
            return null;
        }
    }

    public static function ThisDate() {
        return date('Y-m-d');
    }

    public static function TimeStampForSQL(?int $timestamp = null) {
        return date('Y-m-d H:i:s', $timestamp);
    }

    public static function SQLTimeStampToDate(?string $timestamp = null, bool $nice = false) {
        if(!$nice)
            return date('Y-m-d', strtotime($timestamp));
        else
            return date('Y m d.', strtotime($timestamp));
    }

    public static function ArrayKeyLetezik(array $array, string ...$keys): bool {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                return false;
            }
        }
        return true;
    }

    public static function StrContainsAny(string $haystack, string ...$needles): bool {
        return array_reduce($needles, fn($a, $n) => $a || str_contains($haystack, $n), false);
    }

    public static function ArrayNaturalSort(array $returnarr, string $sortcriteria) {
        usort($returnarr, function($a, $b) use ($sortcriteria) {
            if($a[$sortcriteria] == null)
            {
                $a[$sortcriteria] = "zzzzz";
            }

            if($b[$sortcriteria] == null)
            {
                $b[$sortcriteria] = "zzzzz";
            }

            return strnatcmp($a[$sortcriteria], $b[$sortcriteria]);
        });

        return $returnarr;
    }

    public static function SlugGenerator(string $basestring) : ?string {
        if(!$basestring)
            return null;

        setlocale(LC_CTYPE, 'hu_HU');
        $charstoreplace = array(":", ",", ".", "\"", "'");
        return @strtolower(str_replace($charstoreplace, "", str_replace(" ", "-", iconv('utf-8', 'ascii//TRANSLIT', $basestring))));
    }

    public static function SlugVerifier(string $slug, $function) : string {
        $ujslug = $slug;
        $ind = 1;
        while(true) {
            if(call_user_func($function, $ujslug) !== null) {
                $ujslug = $slug . '-' . $ind;
                $ind++;
            }
            else
                break;
        }
        return $ujslug;
    }

    public static function RenderArrayAsTable(array $array, ?string $link = null, ?string $linkid = null, ?string $tableclass = null) : void {
        if(count($array) == 0)
            return;
        if($tableclass)
            $tableclass = 'class="' . $tableclass . '"';
        ?><table <?=$tableclass?>>
            <thead>
            <tr><?php
                foreach(array_keys($array[array_key_first($array)]) as $key)
                {
                    if(!$linkid || $key != $linkid) {
                        ?><th><?=ucfirst($key)?></th><?php
                    }
                }
                ?></tr>
            </thead>
            <tbody><?php
            foreach($array as $sor)
            {
                ?><tr><?php
                foreach($sor as $key => $val)
                {
                    ?><td><?php
                        if(!$linkid || $key != $linkid) {
                            if($link !== null) {
                                ?><a href="<?=ROOT_PATH . '/' . $link . '/' . $sor[$linkid] ?>"><?php
                            }
                            echo $val;
                            if($link !== null) {
                                ?></a><?php
                            }
                        }
                    ?></td><?php
                }
                ?></tr><?php
            }
            ?></tbody>
        </table><?php
    }

    public static function levHasonlosag(string $needle, string $haystack) : int {
        $needle = mb_strtolower($needle);
        $haystack = mb_strtolower($haystack);

        $score = levenshtein($needle, $haystack);

        if ($needle === $haystack)
            $score -= 100;
        if (str_starts_with($haystack, $needle))
            $score -= 20;
        if (str_contains($haystack, $needle))
            $score -= 5;

        return $score;
    }

    public static function FuzzySearch(array $dbresult, string $needle, string $oszlopnev) : array {
        foreach ($dbresult as &$row) {
            $row['_score'] = self::levHasonlosag($needle, $row[$oszlopnev]);
        }

        usort($dbresult, fn($a, $b) => $a['_score'] <=> $b['_score']);

        return $dbresult;
    }

    public static function FuzzySearchStringGen (string $basestring) : array {
        $needle = '%'; $needle2 = [];
        $chars = preg_split('//u', $basestring, -1, PREG_SPLIT_NO_EMPTY);
        $tmp = '';
        $limit = min(count($chars), 4);
        for($i=0; $i < $limit; $i++) {
            $needle .= $chars[$i] . '_';
        }
        for ($i = 0; $i < $limit - 1; $i++) {
            $tmp = $chars;
            [$tmp[$i], $tmp[$i + 1]] = [$tmp[$i + 1], $tmp[$i]];
            $needle2[] = '%' . implode('', $tmp) . '%';
        }

        return [$basestring . "%", "%" . $basestring . "%" , $needle, ...$needle2];
    }

    public static function NeveloHatarozo(?string $szo) {
        if(!$szo)
            return null;

        $nevelo = "a";
        $elsokarakter = preg_split('//u', $szo, -1, PREG_SPLIT_NO_EMPTY)[0];
        $massalhangzok = 'bcdfghjklmnpqrstvwxz';
        if(!str_contains($massalhangzok, $elsokarakter))
            $nevelo .= 'z';
        return $nevelo . ' ' . $szo;
    }

    public static function CellaLink($link, ...$szovegek)
    {
        foreach($szovegek as $szoveg)
        {
            ?><td><a href='<?=$link?>'><?=$szoveg?></a></td><?php
        }
    }

    public function MultiSelectDropdown(array $elements, array $selected, string $selectnev, string $label, ?int $selectid = null) {
        //TODO: Ha a szülőelemen van overflow:hidden, úgy a legördülő menü nem tud "megszökni" a szülőelem határain kívülre
        //!: A CSP jelenleg tiltja a működését! Átírni EventListenerre, ha szükség lenne rá!!!
        ?><div>
            <label><?=$label?></label>
            <div class="msdropdownparent">
                <div onclick="dropdownMutat('<?=$selectid?>')"><input type="text" readonly value="<?=$label?> listája"></input></div>
                <div <?=($selectid) ? "id=" . $selectid : "" ?> class="msdropdown" onmouseleave="dropdownRejt('<?=$selectid?>')"><?php
                foreach($elements as $element)
                {
                    ?><label class="customcb">
                        <input type="checkbox" name="<?=$selectnev?>[]" value="<?=$element['id']?>" <?=(in_array($element['id'], $selected)) ? "checked" : ""?>>
                            <span class="msddlabel"><?=$element['nev']?></span>
                        <span class="customcbjelolo"></span>
                        </input>
                    </label><?php
                }
                ?></div>
            </div>
        </div><?php
    }

    public static function ConcatToAssocArray(array $fields, ...$concats)
    {
        if(!$concats[0])
        {
            return array();
        }

        $oszlopszam = count($fields);
        $sorokszama = count(explode(';', $concats[0]));
        $assoc = array();

        if($oszlopszam != count($concats))
        {
            echo "A mezőnevek és értékmezők száma nem egyezik!";
            return false;
        }
        
        $sorids = explode(';', $concats[0]);
        for($oszlop = 0; $oszlop < $oszlopszam; $oszlop++) {
            $temparr = explode(';', $concats[$oszlop]);
            for($sor = 0; $sor < $sorokszama; $sor++) {
                $assoc[trim($sorids[$sor])][$fields[$oszlop]] = trim($temparr[$sor]);
            }
        }

        return $assoc;
    }
}