<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/helyisegdb?action=generate" method="post" onsubmit="beKuld.disabled = true; return true;">
        <input type ="hidden" id="epulet" name="epulet" value=<?=$id?>>
        
        <div>
            <label for="emelet">Emelet:</label><br>
            <select id="emelet" name="emelet">
                <option value=""></option>
                <option value="0">Földszint</option>
                <option value="1">Első emelet</option>
                <option value="2">Második emelet</option>
                <option value="3">Harmadik emelet</option>
                <option value="4">Negyedik emelet</option>
            </select>
        </div>

        <div>
            <label for="kezdohelyisegszam">Kezdő helyiség szám<small> (csak a száma)</small>:</label><br>
            <input type="text" accept-charset="utf-8" name="kezdohelyisegszam" id="kezdohelyisegszam"></input>
        </div>

        <div>
            <label for="zarohelyisegszam">Záró helyiség szám<small> (csak a száma)</small>:</label><br>
            <input type="text" accept-charset="utf-8" name="zarohelyisegszam" id="zarohelyisegszam"></input>
        </div>

        <div>
            <label for="szamjegyszam">Számjegyek száma<small> (pl.: 002-es helyiség = 3)</small>:</label><br>
            <input type="text" accept-charset="utf-8" name="szamjegyszam" id="szamjegyszam"></input>
        </div>

        <div class="submit"><input type="submit" name="beKuld" value="Helyiségek generálása"></div>
        </form>
    </div><?php
}