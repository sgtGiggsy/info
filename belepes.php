<?php
$magyarazat = "<strong>A bejelentkezés menete</strong>
    <p>Az oldalra bárki bármikor bejelentkezhet a tartományi felhasználóneve és jelszava megadásával.</p>";
$kuldooldal = str_replace("&", "?", $kuldooldal);
$kuldooldal = str_replace("?kuldooldalid", "&kuldooldalid", $kuldooldal);

?><div class="szerkcard">
    <div class="szerkcardtitle">Bejelentkezés<a class="help" onclick="rejtMutat('magyarazat')">?</a></div>
    <div class="szerkcardbody">
        <div class="szerkeszt">
            <div class="contentcenter">
                <div>
                    <form action="<?=$RootPath?>/index.php<?=$kuldooldal?>" method="post">

                        <div>
                            <label for="felhasznalonev">Felhasználónév:
                            <input type="text" accept-charset="utf-8" name="felhasznalonev" placeholder="Felhasználónév" id="felhasznalonev" required></input></label>
                        </div>
                        
                        <div>
                            <label for="jelszo">Jelszó:
                            <input type="password" name="jelszo" placeholder="Jelszó" id="jelszo" required></label>
                        </div>

                        <div class="submit"><input type="submit" value="Bejelentkezés"></div>
                    </form>
                </div>
            </div>

            <div id="magyarazat">
                <h2 style="text-align: center">Súgó</h2>
                <?=$magyarazat?>
            </div>

        </div>
    </div>
</div>
