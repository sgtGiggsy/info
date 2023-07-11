<?php
if(@$irhat)
{
    ?><form action="?page=beallitasdb" method="post">
        <div class="ketharmad">
            <div>
                <div>
                    <label for="udvozloszoveg">Üdvözlőszöveg:
                    <textarea name="udvozloszoveg" id="udvozloszoveg"><?=$vizsgaadatok['udvozloszoveg']?></textarea></label>
                </div>
                <div>
                    <label for="leiras">Infó rész a láblécben:
                    <textarea name="leiras" id="leiras"><?=$vizsgaadatok['leiras']?></textarea></label>
                </div>
            </div>
            <div>
                <div>
                    <label for="vizsgahossz">A vizsga kérdésszáma:
                    <input type="text" accept-charset="utf-8" name="kerdesszam" id="kerdesszam" value="<?=$vizsgaadatok['kerdesszam']?>"></input></label>
                </div>
                <div>
                    <label for="minimumhelyes">Minimális helyes válaszok száma:
                    <input type="text" accept-charset="utf-8" name="minimumhelyes" id="minimumhelyes" value="<?=$vizsgaadatok['minimumhelyes']?>"></input></label>
                </div>
                <div>
                    <label for="vizsgaido">A vizsgára adott idő:
                    <input type="text" accept-charset="utf-8" name="vizsgaido" id="vizsgaido" value="<?=$vizsgaadatok['vizsgaido']?>"></input></label>
                </div>
                <div>
                    <label for="ismetelheto">A vizsga ismételhető:</label>
                    <select id="ismetelheto" name="ismetelheto">
                        <option value="1" <?=($vizsgaadatok['ismetelheto'] == 1) ? "selected" : "" ?>>Igen</option>
                        <option value="0" <?=($vizsgaadatok['ismetelheto'] != 1) ? "selected" : "" ?>>Nem</option>
                    </select>
                </div>
                <div>
                    <label for="maxismetles">Újrapróbálkozások maximális száma:
                    <input type="text" accept-charset="utf-8" name="maxismetles" id="maxismetles" value="<?=$vizsgaadatok['maxismetles']?>"></input></label>
                </div>
                <div class="submit"><input type="submit" value="<?=$button?>"></div><?php
                cancelForm();
                if($contextmenujogok['ujkornyitas'] && isset($_GET['torlomod']))
                {
                    ?><div><a href="?vizsgareset" style="color: red" onclick="return confirm('Biztos vagy benne, hogy törölni akarod az ÖSSZES VISZGÁT?')">Korábbi vizsgák törlése</a></div><?php
                }
                elseif($contextmenujogok['ujkornyitas'] && !isset($_GET['torlomod']))
                {
                    ?><div class="submit"><button href="?torlomod" style="background: #990000; border: #660000;" onclick="return confirm('Biztos vagy benne, hogy bekapcsolod a törlő módot?')">Törlőmód bekapcsolása</button></div><?php
                }
            ?></div>
        </div>
    </form>
    <script type="text/javascript">
        tinymce.init({
            selector: '#udvozloszoveg',
            plugins : 'advlist autolink link image lists charmap print preview code'
        });

        tinymce.init({
            selector: '#leiras',
            plugins : 'advlist autolink link image lists charmap print preview code'
        })
    </script><?php
}