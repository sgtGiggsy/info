<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <small>Ez a menüpont a portok genrálását végzi el. A túlbonyolítás elkerülése végett csak az utolsó két tag generálása végezhető el egyszere. Tehát ha négy tagból áll a port,
        és van 1-1- valamint 1-2- kezdetű porttartomány is, akkor azokat külön kell legenerálni.</small>
        <form action="<?=$RootPath?>/portdb?action=generate&tipus=telefonkozpont<?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;">
            <input type ="hidden" id="eszkoz" name="eszkoz" value=<?=$id?>>
            
            <div>
                <label for="portpre">Port előtag<small> (pl 1-1-)</small>:</label><br>
                <input type="text" accept-charset="utf-8" name="portpre" id="portpre"></input>
            </div>

            <div>
                <label for="kezdoharmadik">Kezdő harmadik tag port<small> (csak a száma)</small>:</label><br>
                <input type="text" accept-charset="utf-8" name="kezdoharmadik" id="kezdoharmadik"></input>
            </div>

            <div>
                <label for="zaroharmadik">Záró harmadik tag port<small> (csak a száma)</small>:</label><br>
                <input type="text" accept-charset="utf-8" name="zaroharmadik" id="zaroharmadik"></input>
            </div>

            <div>
                <label for="kezdonegyedik">Kezdő negyedik tag port<small> (csak a száma)</small>:</label><br>
                <input type="text" accept-charset="utf-8" name="kezdonegyedik" id="kezdonegyedik" value="0"></input>
            </div>

            <div>
                <label for="zaronegyedik">Záró negyedik tag port<small> (csak a száma)</small>:</label><br>
                <input type="text" accept-charset="utf-8" name="zaronegyedik" id="zaronegyedik" value="23"></input>
            </div>

            <div class="submit"><input type="submit" name="beKuld" value="Portok generálása"></div>
        </form>
    </div><?php
}