<?php
if(@$irhat)
{
    ?><script type="text/javascript"><?php
        if($szemelyes['szinsema'] == "dark")
        {
            ?>
            tinymce.init({
                selector: '#bevezetes',
                plugins : 'advlist autolink link image lists charmap print preview emoticons code',
                skin: "tinymce-5-dark",
                content_css: "tinymce-5-dark"
            });

            tinymce.init({
                selector: '#szovegtorzs',
                plugins : 'advlist autolink link image lists charmap print preview emoticons code',
                skin: "tinymce-5-dark",
                content_css: "tinymce-5-dark"
            });
            <?php
        }
        else
        {
            ?>
            tinymce.init({
                selector: '#bevezetes',
                plugins : 'advlist autolink link image lists charmap print preview emoticons code'
            });

            tinymce.init({
                selector: '#szovegtorzs',
                plugins : 'advlist autolink link image lists charmap print preview emoticons code'
            });
            <?php
        }

    ?></script>
    <form action="<?=$RootPath?>/kozlemeny&action=<?=(isset($_GET['id'])) ? 'update' : 'new' ?><?=$kuldooldal?>" method="post">
        <div class="ketharmad"><?php
            if(isset($_GET['id']))
            {
                ?><input type ="hidden" id="id" name="id" value=<?=$_GET['id']?>><?php
            }
            ?><div>
                <div>
                    <label for="bevezetes">Bevezetés:</label>
                    <textarea id="bevezetes" name="bevezetes"><?=$bevezetes?></textarea><br>
                </div>

                <?php $magyarazat .= "<strong>Bevezetés</strong><p>A közlemény bevezető része.
                    A főoldali első közleményt, és a teljes közlemény megnyitását leszámítva
                    csak ez jelenik meg.</p>" ?>

                <div>
                    <label for="szovegtorzs">Közlemény törzs:</label>
                    <textarea id="szovegtorzs" name="szovegtorzs"><?=$szovegtorzs?></textarea><br>
                </div>

                <?php $magyarazat .= "<strong>Cikk törzs</strong><p>A közlemény törzsszövege.
                    Bármi, ami a közelmény tartalmi magyarázatán túlnyúlik, ide kell kerüljön.</p>" ?>

            </div>

            <div>
                <div>
                    <label for="cim">Cím:</label><br>
                    <input type="text" accept-charset="utf-8" name="cim" id="cim" value="<?=$cim?>"></input><br>
                </div><?php

                $magyarazat .= "<strong>Cím</strong><p>A közlemény címe.</p>";

                if(isset($_GET['id']))
                {
                    ?><div>
                        <label for="link">Link:</label><br>
                        <input type="text" accept-charset="utf-8" name="link" id="link" value="<?=$link?>"></input><br>
                    </div><?php

                    $magyarazat .= "<strong>Link</strong><p>A közlemény felhasználóbarát linkje.
                        Csak szerkesztés során jelenik meg. Új hír létrehozásakor a rendszer
                        generálja le. Az ide beírt szöveg lesz a közlemény linkje.
                        Például: szervernev/kozlemeny/kozlemeny-cime az enélkül használatos:
                        szervernev/kozlemeny/1 helyett. Ha a mező tartalma törlésre kerül,
                        a rendszer újra legenerálja a linket a cikk címe alapján,
                        amúgy azt fogja linkként használni, ami ide be van írva.</p>";
                }

                ?><div>
                    <label for="Cimkek">Cimkék:</label><br><?php
                    foreach ($cimkek as $x)
                    {
                        ?><label style="font-weight: normal"><input type="checkbox" name="cimkek[]" value="<?=$x["id"]?>"
                        <?php
                        if(isset($cimkelista))
                        {
                            foreach($cimkelista as $cimke)
                            {
                                if($x['id'] == $cimke)
                                {
                                    echo "checked"; break;
                                }
                            }
                        }
                        ?>/><?=$x["nev"]?></label><br><?php
                    }

                    $magyarazat .= "<strong>Címkék</strong><p>A közlemények rendszerezéséhez,
                        és könnyebb átláthatóságához használható címkék.</p>";

                ?></div>

                <div>
                    <label for="publikalt">Publikált:</label><br>
                    <label class="kapcsolo">
                        <input type="hidden" name="publikalt" id="publikalthidden" value="">
                        <input type="checkbox" name="publikalt" id="publikalt" value="1" <?=($publikalt) ? "checked" : "" ?>>
                        <span class="slider"></span>
                    </label>
                </div><br>

                <?php $magyarazat .= "<strong>Publikált</strong><p>A közlemény láthatósági állapota.
                    Kizárólag 'Publikált' állapotú közlemények jelennek meg az oldalon.</p>" ?>

                <div><input type="submit" value='<?=$button?>'></div>
                <?php cancelForm(); ?>
                <br>
                <div class="submit">
                    <button type='button' onclick='showSlideIn("<?=$slideup?>", "slideup-"); return false'>Korábbi közlemények</button>
                </div>
            </div>
        </div>
    </form><?php
}