<?php
function HtmlHead(&$keywords, &$canonical, &$title, &$ogtype, &$publishtime, &$shareimage, &$robots, &$ablakcim, &$sitedesc, &$cimke)
{
    $title .= " - Biztonsági irányelvek (Security Policy)";
    $ablakcim = $title;
    $sitedesc = "A Kalóriafaló biztonsági irányelvei";
    $cimke = "security";
}

function Render(?string $egyedihiba = null) {
    ?><div class="normalcontent szovegtartalom">
        <small>A magyarnyelvű irányelvekért görgess lejjebb</small>

        <h1>Security Policy</h1>
        <p>We take the security of our systems seriously. If you discover a vulnerability, we appreciate your help in responsibly disclosing it.</p>
        <h2>Reporting a Vulnerability</h2>
        <p>Please report security issues to:</p>
        <p>security@kaloriafalo.hu</p>
        <p>Include the following information:</p>
        <li>Description of the issue</li>
        <li>Steps to reproduce (PoC)</li>
        <li>Affected URLs or systems</li>
        <li>Potential impact</li>
        <p>Reports without sufficient technical details may not be processed.</p>

        <h2>Scope</h2>
        <p>This policy applies to:</p>
        <li>All services under the kaloriafalo.hu domain</li>

        <h2>Out of scope:</h2>
        <li>Issues requiring physical access</li>
        <li>Social engineering attacks</li>
        <li>Denial of Service (DoS/DDoS)</li>
        <li>Reports based on outdated software versions without a working exploit</li>

        <h2>Guidelines</h2>

        <h3>You agree to:</h3>
        <li>Avoid privacy violations, data destruction, or service disruption</li>
        <li>Not access or modify data that does not belong to you</li>
        <li>Perform testing only to the extent necessary to confirm a vulnerability</li>
        <h3>What You Can Expect</h3>
        <li>We will acknowledge your report within a reasonable timeframe</li>
        <li>We will investigate legitimate reports</li>
        <li>We may contact you for additional details</li>

        <h2>No Bounty Program</h2>
        <p>We currently do not offer monetary rewards for vulnerability reports.</p>

        <h2>Legal</h2>
        <p>We will not pursue legal action against researchers who:</p>
        <li>Act in good faith</li>
        <li>Follow this policy</li>
        <li>Do not exploit vulnerabilities beyond what is necessary for proof</li>

        <p>-o-o-o-o-o-o-o-o-o-o-o-o-o-o-o-</p>

        <h1>Biztonsági irányelvek (Security Policy)</h1>
        <p>Komolyan vesszük rendszereink biztonságát. Ha sebezhetőséget találsz, köszönjük, ha azt felelősen jelented.</p>

        <h2>Sebezhetőség jelentése</h2>
        <p>Kérjük, a biztonsági problémákat az alábbi címen jelezd:</p>
        <p>security@kaloriafalo.hu</p>
        <p>A gyors feldolgozás érdekében a bejelentés tartalmazza:</p>
        <li>A hiba leírását</li>
        <li>A reprodukálás lépéseit (PoC)</li>
        <li>Az érintett URL-eket vagy rendszereket</li>
        <li>A várható hatás rövid leírását</li>
        <p>A nem kellően részletes bejelentéseket nem áll módunkban kivizsgálni.</p>

        <h2>Hatókör</h2>
        <p>Az irányelvek az alábbiakra vonatkoznak:</p>
        <li>A kaloriafalo.hu domain alá tartozó szolgáltatások</li>

        <h2>Nem tartozik a hatókörbe:</h2>
        <li>Fizikai hozzáférést igénylő támadások</li>
        <li>Social engineering (pl. adathalászat, megtévesztés)</li>
        <li>Szolgáltatásmegtagadásos támadások (DoS/DDoS)</li>
        <li>Elavult szoftververziókra épülő, működő exploit nélküli jelentések</li>

        <h2>Irányelvek</h2>
        <p>A bejelentéssel vállalod, hogy:</p>
        <li>Nem sérted mások adatait vagy magánszféráját</li>
        <li>Nem módosítasz, törölsz vagy teszel közzé adatokat</li>
        <li>Nem okozol szolgáltatás-kiesést vagy teljesítményromlást</li>
        <li>A tesztelést a szükséges minimális mértékre korlátozod</li>

        <h2>Mit várhatsz tőlünk?</h2>
        <li>A bejelentést ésszerű időn belül visszaigazoljuk</li>
        <li>A valós problémákat kivizsgáljuk</li>
        <li>Szükség esetén további információt kérünk</li>

        <h2>Jutalmazás</h2>
        <p>Jelenleg nem kínálunk pénzbeli jutalmat a sebezhetőségek bejelentéséért.</p>

        <h2>Jogi nyilatkozat</h2>

        <p>Nem indítunk jogi eljárást azok ellen, akik:</p>
        <li>jóhiszeműen járnak el</li>
        <li>betartják ezen irányelveket</li>
        <li>nem használják ki a hibát a szükséges mértéken túl</li>
    </div><?php
}