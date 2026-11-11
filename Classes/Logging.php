<?php

namespace Infoinventory\Classes;

class Logging
{
    public static function LogLogin(int $uid, int $session_id) : bool {
        $login = new MySQLHandler("INSERT INTO core_bejelentkezesek (felhasznalo_id, ip_cim, session_id) VALUES (?, ?, ?);", $uid, $_SERVER['REMOTE_ADDR'], $session_id);
        return $login->siker;
    }

    public static function LogFailedLogin(int $session_id) : bool {
        $failed = new MySQLHandler();
        $failed->Prepare('INSERT INTO core_bejelentkezesi_hibak (felhasznalo_nev, ip_cim, session_id) VALUES (?, ?, ?)');
        $failed->Run($_POST['usernev'], $_SERVER['REMOTE_ADDR'], $session_id);

        return $failed->siker;
    }

    public static function LogApiCall() : bool {
        $postc = [];
        if($_SERVER['REQUEST_METHOD'] == "POST")
            $postc = $_POST;
        $postc = json_encode($postc, JSON_UNESCAPED_UNICODE);

        $api = new MySQLHandler("INSERT INTO core_api_hivasok (request_method, request_uri, sessid, status_code, post_content)
            VALUES (?, ?, ?, ?, ?);",
            $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], Personalvars::$session_id, http_response_code(), $postc);
        return $api->siker;
    }

    public static function SessionDB(?int $uid = null) : ?int {
        $sessidstr = session_id();
        $letezik = new MySQLHandler("SELECT session_id FROM core_sessionok WHERE sessidstr = ?;", $sessidstr);
        if($letezik->sorokszama != 0)
            $session_id = $letezik->Fetch()['session_id'];
        else {
            $sess = new MySQLHandler("INSERT INTO core_sessionok (sessidstr, felhasznalo_id, user_agent) VALUES (?, ?, ?)", $sessidstr, $uid, $_SERVER['HTTP_USER_AGENT']);
            $session_id = $sess->last_insert_id;
        }

        return $session_id;
    }

    public static function SessionSeen(int $session_id) : void {
        $ts = Helpers::TimeStampForSQL();
	    new MySQLHandler("UPDATE core_sessionok SET last_seen = ? WHERE session_id = ?", $ts, $session_id);
    }

    public static function Latogatas(float $responseTime, ?int $botscore, ?array $bot_ok, int $session_id, int $dbcallcount) : void {
        $bot_ok = json_encode($bot_ok);
        $bot = 0;
        $slug = null;

        if($botscore > 5)
            $bot = 1;

        new MySQLHandler("INSERT INTO core_oldal_megtekintesek
                    (uri, felhasznalo_id, oldalgeneralas_ideje, session_id, ip_cim, hivatkozas, slug, db_call)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                $_SERVER['REQUEST_URI'], FELHASZNALO->uid, $responseTime, $session_id, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_REFERER'] ?? null, Settings::$slug, $dbcallcount
            );
        
        new MySQLHandler("UPDATE core_sessionok SET bot = ?, bot_pontszam = ?, bot_ok = ? WHERE session_id =?;",
            $bot, $botscore, $bot_ok, $session_id);
    }

    public static function PageFault(int $session_id, float $loadtime) : void {
        //TODO Az adatbázisban van egy flag mező fenntartva ami a gyanús felhasználói tevékenységek megjelölésére szolgálna.
        //TODO Jelenleg ez a mező csak dísznek van, meg kell hozzá írni az elemző függvényt
        $postc = json_encode($_POST, JSON_UNESCAPED_UNICODE);
        $getc = json_encode($_GET, JSON_UNESCAPED_UNICODE);
        $respnse_code = http_response_code();

        $hiba = new MySQLHandler("INSERT INTO core_oldal_hibak
                (felhasznalo_id, uri, post_content, get_content, session_id, betoltes_ideje, response_code, ip_cim, referrer)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);",
            Personalvars::$uid, $_SERVER['REQUEST_URI'], $postc, $getc, $session_id, $loadtime, $respnse_code, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_REFERER'] ?? null);
    }

    public static function BotDetection(?int &$botscore, ?array &$bot_ok) : void {
        $now = time();
        if (!isset($_SESSION['last_request_time'])) {
            $_SESSION['last_request_time'] = $now;
            $_SESSION['requests'] = [];
            $_SESSION['visited_pages'] = [];
            $_SESSION['response_codes'] = [];
        }
        else {
            // Az utolsó két kérés közti idő alapján elemzés

            $last = $_SESSION['last_request_time'];
            $delta = $now - $last;

            if ($delta < 2) {
                $bot_ok[] = "Gyors kattintás";
                $botscore += 2;
            }

            $_SESSION['last_request_time'] = $now;

            // Az utolsó 10 másodperc során felkeresett oldalak alapján történő elemzés

            $_SESSION['requests'][] = $now;

            $_SESSION['requests'] = array_filter(
                $_SESSION['requests'],
                fn($t) => $t > $now - 10
            );

            if (count($_SESSION['requests']) > 5) {
                $bot_ok[] = "Sok kérés 10 másodpercen belül";
                $botscore += 4;
            }

            // Oldal minta alapján történő elemzés. Ha nagyon különböznek az elérési utak, gyanús

            $_SESSION['visited_pages'][] = $_SERVER['REQUEST_URI'];
            $_SESSION['visited_pages'] = array_slice($_SESSION['visited_pages'], -20);

            $unique = count(array_unique($_SESSION['visited_pages']));
            $total  = count($_SESSION['visited_pages']);

            $ratio = $unique / max($total, 1);

            if ($ratio > 0.95) {
                $bot_ok[] = "Random oldalak";
                $botscore += 2;
            }

            // Ha nincs referrer, nagyon enyhe bot gyanú

            if (empty($_SERVER['HTTP_REFERER'])) {
                $bot_ok[] = "Nincs referrer";
                $botscore += 1;
            }

            // Ha a user agent gyanúsan bot-ra utal, flag
            // Bár Nexus és G900P lehetne valós eszköz is, de gyakorlati tapasztalatok alapján
            // ma már csak bot-ok user agent-jében jelennek meg

            if (preg_match('/curl|python|wget|bot|scan|palo|nexus|G900P|disqus|sitemaps/i', $_SERVER['HTTP_USER_AGENT'] ?? '')) {
                $bot_ok[] = "Gyanús UA";
                $botscore += 5;
            }

            // Ha 5% felett van az oldalhibák száma, gyanús, hogy automatikus scan

            $pagevisits = count($_SESSION['response_codes']);
            $hibak = count(array_filter($_SESSION['response_codes'], fn($x) => $x !== 200));
            if ($pagevisits && $pagevisits > 0 && $hibak / $pagevisits > 0.05) { // Első betöltésnél a $pagevisits értéke mindig 0;
                $bot_ok[] = "Sok oldalhiba";
                $botscore += 5;
            }
        }
    }
}