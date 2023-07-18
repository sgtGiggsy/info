<?php
if(@$irhat)
{
    ?><div class="contentcenter contentcenterpadding">
        <div>
            <form action="<?=$RootPath?>/vizsga/<?=$vizsgaadatok['url']?>/kerdesszerkeszt&action=<?=(isset($_GET['id'])) ? "update" : "addnew" ?>" method="post" enctype="multipart/form-data" onsubmit="return checkKivalasztas();"><?php
                if(isset($_GET['id']))
                {
                    ?><input type ="hidden" id="id" name="id" value=<?=$id?> /><?php
                }
                ?><div>
                    <label for="kerdes">Kérdés szövege:</label><br>
                    <textarea name="kerdes" id="kerdes"><?=$kerdesszoveg?></textarea>
                </div>

                <div>
                    <label for="kerdeskep">Kép csatolása a kérdéshez:</label><br>
                    <input type="file" name="kerdeskep" id="kerdeskep" accept="image/jpeg, image/png, image/bmp">
                </div><?php

                if(isset($_GET['id']) && $kerdes['kep'])
                {
                    ?><div>
                        <img src="<?=$kep?>" width="500px">
                        <label for="keptorol">Feltöltött kép eltávolítása</label>
                        <input type="checkbox" name="keptorol" />
                    </div><?php
                }

                $valaszlehetosegszam = 4;
                if(count($valaszlehetosegek) > 0)
                {
                    $valaszlehetosegszam = count($valaszlehetosegek);
                }

                ?><div class="kerdesthreecol">
                    <div><h2>Válasz szövege</h2></div>
                    <div><h2>Helyes válasz</h2></div>
                    <div><h2>Válasz törlése</h2></div><?php

                    for($i = 1; $i <= $valaszlehetosegszam; $i++)
                    {
                        ?><div>
                            <label for="valasz-<?=$i?>">Válasz <?=$i?>:<br></label>
                            <textarea name="valasz[]" id="valasz-<?=$i?>"><?=(count($valaszlehetosegek) > 0) ? $valaszlehetosegek[$i-1]['valaszszoveg'] : "" ?></textarea>
                        </div>
                        <div>
                            <label class="customcb cbcenter">
                                <input type="checkbox"
                                        name="helyes[<?=$i?>]"
                                        id="helyes-<?=$i?>"
                                        value="<?=(count($valaszlehetosegek) > 0) ? $valaszlehetosegek[$i-1]['valaszid'] : $i ?>"
                                        <?=(count($valaszlehetosegek) > 0 && $valaszlehetosegek[$i-1]['helyes']) ? "checked" : "" ?>><?php
                                        if(count($valaszlehetosegek) > 0)
                                        {
                                            ?><input type ="hidden" id="vid-<?=$i?>" name="vid[<?=$i?>]" value=<?=$valaszlehetosegek[$i-1]['valaszid']?> /><?php
                                        }
                                ?><span class="customcbjelolo"></span>
                            </label>
                        </div>
                        <div>
                            <label class="customcb cbcenter">
                                <input type="checkbox"
                                        name="torol[<?=$i?>]"
                                        id="torol-<?=$i?>"
                                        value="<?=(count($valaszlehetosegek) > 0) ? $valaszlehetosegek[$i-1]['valaszid'] : $i ?>">
                                <span class="customcbjelolo"></span>
                            </label>
                        </div><?php
                    }
                for($divid = 1; $divid < 5; $divid++)
                {
                    ?><div id="pluszkerdes-<?=$divid?>" style="display: none;"></div>
                    <div id="pluszhelyes-<?=$divid?>" style="display: none;"></div>
                    <div id="plusztorles-<?=$divid?>" style="display: none;"></div><?php
                }
                ?></div><?php
                ?><div class="submit"><input type="submit" value="<?=$button?>"></div>
                <div class="submit"><button id="button-valasz" type="button" onclick="addUjValasz(); return false;">Újabb válaszlehetőség hozzáadása</button></div>
                <?php cancelForm(); ?>
            </form>
        </div>
    </div>
        
    <div id="kerdestoadd" style="display: none">
        <div id='pluszlabel'>
            <label id="label-<?=$i?>" for="valasz-<?=$i?>">Válasz <?=$i?>:<br></label>
            <textarea name="valasz[]" id="valasz-<?=$i?>"></textarea>
        </div>
        <div id='pluszcheckbox'>
            <label class="customcb cbcenter">
                <input type="checkbox"
                        name="helyes[<?=$i?>]"
                        id="helyes-<?=$i?>"
                        value="<?=$i?>" />
                <span class="customcbjelolo"></span>
            </label>
        </div>
        <div id='pluszcbdel'></div>
    </div>

    <script type="text/javascript"><?php
        if($szemelyes['szinsema'] == "dark")
        {
            ?>
            tinymce.init({
                selector: '#kerdes',
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
                selector: '#kerdes',
                plugins : 'advlist autolink link image lists charmap print preview emoticons code'
            });
            <?php
        }
        ?>

        var kovetkezokerdes = <?=$i?>;
        var kovetkezopluszkerdes = 1;

        function addUjValasz() {
            var label = document.getElementById('label-' + kovetkezokerdes);
            var textarea = document.getElementById('valasz-' + kovetkezokerdes);
            var checkbox = document.getElementById('helyes-' + kovetkezokerdes);

            var origlabel = document.querySelector('#pluszlabel');
            var clonelabel = origlabel.cloneNode(true);
            var origcheckbox = document.querySelector('#pluszcheckbox');
            var clonecheckbox = origcheckbox.cloneNode(true);
            var origcbdel = document.querySelector('#pluszcbdel');
            var clonecbdel = origcbdel.cloneNode(true);
            //var label = clone.getElementById('ujvalaszlabel');
            
            //console.log(clone);
            var elem = document.getElementById('pluszkerdes-' + kovetkezopluszkerdes);
            elem.innerHTML = clonelabel.innerHTML;
            elem.style.display = 'unset';
            var elem2 = document.getElementById('pluszhelyes-' + kovetkezopluszkerdes);
            elem2.innerHTML = clonecheckbox.innerHTML;
            elem2.style.display = 'unset';
            var elem3 = document.getElementById('plusztorles-' + kovetkezopluszkerdes);
            elem3.innerHTML = clonecbdel.innerHTML;
            elem3.style.display = 'unset';

            kovetkezokerdes++;
            kovetkezopluszkerdes++;

            label.id = 'label-' + kovetkezokerdes;
            label.for = 'valasz-' + kovetkezokerdes;
            label.textContent = 'Válasz ' + kovetkezokerdes + ':';
            textarea.id = 'valasz-' + kovetkezokerdes;
            checkbox.id = 'helyes-' + kovetkezokerdes;
            checkbox.value = kovetkezokerdes;
            checkbox.name = kovetkezokerdes;

            if(kovetkezopluszkerdes == 5)
            {
                var button = document.getElementById('button-valasz');
                button.disabled = true;
                button.style.display = 'none';
            }
        }

        function checkKivalasztas()
        {
            var cboxcount = 1;
            let voltjeloles = false;

            while (document.getElementById('helyes-' + cboxcount))
            {
                checkbox = document.getElementById('helyes-' + cboxcount);
                if(checkbox.checked == true)
                {
                    voltjeloles = true;
                }
                cboxcount++;
            }
            
            if(!voltjeloles)
            {
                hideOverlay();
                return confirm('Nem jelölt meg egyetlen válaszlehetőséget sem helyesként.\nBiztosan így szeretné elmenteni a kérdést?');
            }
            else
            {
                return true;
            };
        }

        function hideOverlay()
        {
            setTimeout(
				function(){
					hideProgressOverlay();
				}, 1000
			);
        }

    </script><?php
}