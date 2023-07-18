<?php
if(@$irhat)
{
    ?><form action="<?=$vizsgabeallitasurl?>" enctype="multipart/form-data" method="post">
        <div class="ketharmad">
            <input type ="hidden" id="vizsgaid" name="vizsgaid" value=<?=$vizsgaid?>>
            <div>
                <div>
                    <label for="udvozloszoveg">Üdvözlőszöveg:
                    <textarea name="udvozloszoveg" id="udvozloszoveg"><?=$udvozloszoveg?></textarea></label>
                </div>
                <div>
                    <label for="vendegudvozlo">Üdvözlőszöveg be nem jelentkezett felhasználók részére:
                    <textarea name="vendegudvozlo" id="vendegudvozlo"><?=$vendegudvozlo?></textarea></label>
                </div>
                <div>
                    <label for="leiras">Infó rész a láblécben:
                    <textarea name="leiras" id="leiras"><?=$leiras?></textarea></label>
                </div>
            </div>
            <div><?php
                if($fejleckep)
                {
                    ?><div>
                        <img src="<?=$RootPath?>/uploads/<?=$fejleckep?>" width="500px">
                        <label for="keptorol">Feltöltött kép eltávolítása</label>
                        <input type="checkbox" name="keptorol" />
                    </div><?php
                }
                ?><div>
                    <label for="fejleckep">A vizsga fejléceként használt kép:</label><br>
                    <input type="file" name="fejleckep" accept="image/jpeg, image/png, image/bmp">
                </div><?php
                if($mindir)
                {
                    ?><div>
                        <label for="url">A vizsga címsorban használt neve:
                        <input type="text" accept-charset="utf-8" name="url" id="url" value="<?=$url?>"></input></label>
                    </div><?php
                }
                ?><div>
                    <label for="nev">A vizsga neve:
                    <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input></label>
                </div>
                
                <div>
                    <label for="vizsgahossz">A vizsga kérdésszáma:
                    <input type="text" accept-charset="utf-8" name="kerdesszam" id="kerdesszam" value="<?=$kerdesszam?>"></input></label>
                </div>
                <div>
                    <label for="minimumhelyes">Minimális helyes válaszok száma:
                    <input type="text" accept-charset="utf-8" name="minimumhelyes" id="minimumhelyes" value="<?=$minimumhelyes?>"></input></label>
                </div>
                <div>
                    <label for="vizsgaido">A vizsgára adott idő:
                    <input type="text" accept-charset="utf-8" name="vizsgaido" id="vizsgaido" value="<?=$vizsgaido?>"></input></label>
                </div>
                <div>
                    <label for="ismetelheto">A vizsga ismételhető:</label>
                    <select id="ismetelheto" name="ismetelheto">
                        <option value="1" <?=($ismetelheto == 1) ? "selected" : "" ?>>Igen</option>
                        <option value="0" <?=($ismetelheto != 1) ? "selected" : "" ?>>Nem</option>
                    </select>
                </div>
                <div>
                    <label for="maxismetles">Újrapróbálkozások maximális száma:
                    <input type="text" accept-charset="utf-8" name="maxismetles" id="maxismetles" value="<?=$maxismetles?>"></input></label>
                </div>
                <div class="submit"><input type="submit" value="<?=$button?>"></div><?php
                cancelForm();
                if(isset($contextmenujogok))
                {
                    if($contextmenujogok['ujkornyitas'])
                    {
                        ?><div class="submit"><input type="submit" name="ujornyitas" value="Jelen vizsgakör lezárása, új kör indítása" onclick="return confirm('Biztos vagy benne, hogy lezárnád a jelenleg futó vizsgakört, és újat indítanál?');"></div><?php
                        if(isset($_GET['torlomod']))
                        {
                            ?><div><a href="?vizsgareset" style="color: red" onclick="return confirm('Biztos vagy benne, hogy törölni akarod az ÖSSZES VISZGÁT?')">Korábbi vizsgák törlése</a></div><?php
                        }
                        elseif(!isset($_GET['torlomod']))
                        {
                            ?><div class="submit"><button onclick="return confirm('Biztos vagy benne, hogy bekapcsolod a törlő módot?'); return false;" style="background: #990000; border: #660000;">Törlőmód bekapcsolása</button></div><?php
                        }
                    }
                    
                }
            ?></div>
        </div>
    </form>
    <script type="text/javascript"><?php
        if($szemelyes['szinsema'] == "dark")
        {
            ?>
            tinymce.init({
                selector: '#udvozloszoveg',
                plugins : 'advlist autolink link image lists charmap print preview emoticons code',
                skin: "tinymce-5-dark",
                content_css: "tinymce-5-dark"
            });

            tinymce.init({
                selector: '#vendegudvozlo',
                plugins : 'advlist autolink link image lists charmap print preview emoticons code',
                skin: "tinymce-5-dark",
                content_css: "tinymce-5-dark"
            });

            tinymce.init({
                selector: '#leiras',
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
                selector: '#udvozloszoveg',
                plugins : 'advlist autolink link image lists charmap print preview emoticons code'
            });

            tinymce.init({
                selector: '#vendegudvozlo',
                plugins : 'advlist autolink link image lists charmap print preview emoticons code'
            });

            tinymce.init({
                selector: '#leiras',
                plugins : 'advlist autolink link image lists charmap print preview emoticons code'
            });
            <?php
        }
    ?></script><?php
}