<?php
if(@$irhat)
{
    ?><div class="contentcenter">
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

                for($i = 1; $i <= $valaszlehetosegszam; $i++)
                {
                    ?><div>
                        <label for="valasz-<?=$i?>">Válasz <?=$i?>:<br></label>
                        <textarea name="valasz[]" id="valasz-<?=$i?>"><?=(count($valaszlehetosegek) > 0) ? $valaszlehetosegek[$i-1]['valaszszoveg'] : "" ?></textarea>
                        <input type="checkbox"
                                name="helyes[<?=$i?>]"
                                id="helyes-<?=$i?>"
                                value="<?=(count($valaszlehetosegek) > 0) ? $valaszlehetosegek[$i-1]['valaszid'] : $i ?>"
                                <?=(count($valaszlehetosegek) > 0 && $valaszlehetosegek[$i-1]['helyes']) ? "checked" : "" ?>><?php
                                if(count($valaszlehetosegek) > 0)
                                {
                                    ?><input type ="hidden" id="vid-<?=$i?>" name="vid[<?=$i?>]" value=<?=$valaszlehetosegek[$i-1]['valaszid']?> /><?php
                                }
                      ?></div><?php
                }
                for($divid = 1; $divid < 5; $divid++)
                {
                    ?><div id="pluszkerdes-<?=$divid?>"></div><?php
                }
                
                ?><div class="submit"><input type="submit" value="<?=$button?>"></div>
                <div class="submit"><button id="button-valasz" type="button" onclick="addUjValasz(); return false;">Újabb válaszlehetőség hozzáadása</button></div>
                <?php cancelForm(); ?>
            </form>
        </div>
    </div>
        
    <div id="kerdestoadd" style="display: none">
        <label id="label-<?=$i?>" for="valasz-<?=$i?>">Válasz <?=$i?>:<br></label>
        <textarea name="valasz[]" id="valasz-<?=$i?>"></textarea>
        <input type="checkbox"
                name="helyes[<?=$i?>]"
                id="helyes-<?=$i?>"
                value="<?=$i?>" />
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

            var origselect = document.querySelector('#kerdestoadd');
            var clone = origselect.cloneNode(true);
            //var label = clone.getElementById('ujvalaszlabel');
            
            //console.log(clone);
            var elem = document.getElementById('pluszkerdes-' + kovetkezopluszkerdes);
            elem.innerHTML = clone.innerHTML;

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