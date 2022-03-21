<?php

if(!@$mindir)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./db/menudb.php");
    }

    $szulo = array();
    foreach($menu as $x)
    {
        $szulo[] = array("id" => $x['id'], "menupont" => $x['menupont']);
    }
    ?><div class="oldalcim">Menük adminisztrációja</div>
    <table>
        <thead>
            <tr>
                <th>Sorszám</th>
                <th>Menüpont</th>
                <th>Szülő</th>
                <th>URL</th>
                <th>Oldal</th>
                <th>Címszöveg</th>
                <th>Megjelenik</th>
                <th>Menüterület</th>
                <th>Sorrend</th>
            </tr>
        </thead>
        <tbody><?php
            foreach($menu as $menup)
            {
                ?><tr>
                    <form action="<?=$RootPath?>/menuszerkeszt&action=update" method="post">
                        <td><input type ="hidden" id="id" name="id" value=<?=$menup['id']?>><h3><?=$menup['id']?></h3></td>
                        <td><input type="text" name="menupont" value="<?=$menup['menupont']?>"></td>
                        <td><select name="szulo">
                                <option value="">Nincs</option><?php
                                foreach($szulo as $x)
                                {
                                    ?><option value="<?=$x['id']?>" <?=($x['id'] == $menup['szulo']) ? "selected" : "" ?>><?=$x['menupont']?></option><?php
                                }
                            ?></select>
                        </td>
                        <td><input type="text" name="url" value="<?=$menup['url']?>"></td>
                        <td><input type="text" name="oldal" value="<?=$menup['oldal']?>"></td>
                        <td><input type="text" name="cimszoveg" value="<?=$menup['cimszoveg']?>"></td>
                        <td><select name="aktiv">
                                <option value="" <?=(!$menup['aktiv']) ? "selected" : "" ?>>Senkinek</option>
                                <option value="1" <?=($menup['aktiv'] == "1") ? "selected" : "" ?>>Jogosultaknak</option>
                                <option value="2" <?=($menup['aktiv'] == "2") ? "selected" : "" ?>>Bejelentkezetteknek</option>
                                <option value="3" <?=($menup['aktiv'] == "3") ? "selected" : "" ?>>Mindenkinek</option>
                                <option value="4" <?=($menup['aktiv'] == "4") ? "selected" : "" ?>>Csak látogatóknak</option>
                            </select>
                        </td>
                        <td><input type="text" name="menuterulet" value="<?=$menup['menuterulet']?>"></td>
                        <td><input type="text" name="sorrend" value="<?=$menup['sorrend']?>"></td>
                        <td><input type="submit" value="Módosítás"></td>
                    </form>
                </tr><?php
            }
            ?><tr>
                <form action="<?=$RootPath?>/menuszerkeszt&action=new" method="post">
                    <td><h3>Új menü</h3></td>
                    <td><input type="text" name="menupont"></td>
                    <td><select name="szulo">
                            <option value="">Nincs</option><?php
                            foreach($szulo as $x)
                            {
                                ?><option value="<?=$x['id']?>"><?=$x['menupont']?></option><?php
                            }
                        ?></select>
                    </td>
                    <td><input type="text" name="url"></td>
                    <td><input type="text" name="oldal"></td>
                    <td><input type="text" name="cimszoveg"></td>
                    <td><select name="aktiv">
                            <option value="">Senkinek</option>
                            <option value="1">Jogosultaknak</option>
                            <option value="2">Bejelentkezetteknek</option>
                            <option value="3">Mindenkinek</option>
                            <option value="4">Csak látogatóknak</option>
                        </select>
                    </td>
                    <td><input type="text" name="menuterulet"></td>
                    <td><input type="text" name="sorrend"></td>
                    <td><input type="submit" value="Új"></td>
                    </form>
                </tr>
        </tbody>
    </table><?php
}