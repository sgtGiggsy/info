<?php
if(isset($_GET['id']) && is_numeric($_GET['id']))
{
    $kozlemenyget = mySQLConnect("SELECT * FROM kozlemenyek WHERE id = $id");
    $kozlemeny = mysqli_fetch_assoc($kozlemenyget);
    $szerzo = $kozlemeny['szerzo'];
    $cimkelista = getCimkek($kozlemeny['cimke']);
}

if (isset($_GET['action']) && !$mindir && !(isset($szerzo) && $szerzo == $felhasznaloid))
{
    getPermissionError();
}
elseif(isset($_GET['action']))
{
	$magyarazat = null;
	// Amíg nem tudjuk, hogy a folyamat jár-e tényleges írással, a változót false-ra állítjuk
    $dbir = false;

    // Amíg nem tudjuk, hogy a felhasználó valós műveletet akar végezni, a változót false-ra állítjuk
    $irhat = false;

    // Ha a kért művelet nem a szerkesztő oldal betöltése, az adatbázis változót true-ra állítjuk
    if($_GET['action'] == "new" || $_GET['action'] == "update" || $_GET['action'] == "delete")
    {
        $irhat = true;
        $dbir = true;
    }

    // Ha a kért művelet a szerkesztő oldal betöltése, az írás változót true-ra állítjuk
    if($_GET['action'] == "addnew" || $_GET['action'] == "edit")
    {
        $irhat = true;
    }

    // Ha a felhasználó valótlan műveletet akart folytatni, letilt
    if(!$irhat && !$dbir)
    {
        getPermissionError();
    }
    // Ha a kért művelet jár adatbázisművelettel, az adatbázis műveletekért felelős oldal meghívása
    elseif($irhat && $dbir && count($_POST) > 0)
    {
        include("./modules/kozlemenyek/db/kozlemenydb.php");

        // A kiinduló oldalra visszairányító függvény meghívása.
        afterDBRedirect($con);
    }
    else
    {
		$cim = $bevezetes = $szovegtorzs = $link = $publikalt = null;
		
		$kozlemenyek = mySQLConnect("SELECT id, cim FROM kozlemenyek ORDER BY id DESC");
		$cimkek = mySQLConnect("SELECT id, nev FROM kozlemenykategoriak");

		$slideup = 1;
		$button = "Új közlemény";
        $oldalcim = "Új közlemény kiadása";
        $form = "modules/kozlemenyek/forms/kozlemenyform";

		if(isset($_GET['id']))
		{
			$cim = $kozlemeny['cim'];
			$bevezetes = $kozlemeny['bevezetes'];
			$szovegtorzs = $kozlemeny['szovegtorzs'];
			$link = $kozlemeny['link'];
			$publikalt = $kozlemeny['publikalt'];

			$button = "Közlemény szerkesztése";
			$oldalcim = "Közlemény szerkesztése";
		}

		include('./templates/edit.tpl.php');

		if(mysqli_num_rows($kozlemenyek) > 0)
        {
			?><div id="slideup-<?=$slideup?>" onmouseleave='showSlideIn("<?=$slideup?>", "slideup-")'>
                <div class="tablecard">
                    <div class="tablecardtitle">Meglévő közlemények</div>
                    <div class="tablecardbody" style="max-height: calc(260px - 3em)">
						<table>
							<thead>
								<tr>
									<th>Sorszám</th>
									<th>Közlemény címe</th>
									<th>Szerkeszt</th>
								</tr>
							</thead>
							<tbody><?php
							foreach($kozlemenyek as $x)
							{
								?><tr class='kattinthatotr' data-href='<?=$RootPath?>/kozlemeny/<?=$x['id']?>?action=edit'>
									<td><?=$x['id']?></td>
									<td><?=$x['cim']?></td>
									<td><?=$icons['editnews']?></td>
								</tr><?php
							}
							?></tbody>
						</table>
					</div>
                </div>
            </div><?php
        }
	}
}
else
{
	$where = "WHERE link = '" . $id . "'";
	if(is_numeric($_GET['id']))
	{
		$where = "WHERE kozlemenyek.id = " . $id;
	}
	$result = mySQLConnect("SELECT kozlemenyek.id AS kozlemenyid, link, szerzo, nev, cim, bevezetes, szovegtorzs, ido, cimke
		FROM kozlemenyek
			LEFT JOIN felhasznalok ON kozlemenyek.szerzo = felhasznalok.id
		$where");

	$kozlemeny = mysqli_fetch_assoc($result);
	$ujoldalcim = $ablakcim . " - " . $kozlemeny['cim'];

	?><div class="oldalcim"><?=$kozlemeny['cim']?><?php
		if($mindir)
		{
			?><a class="help" href="<?=$RootPath?>/kozlemeny/<?=$kozlemeny['kozlemenyid']?>?action=edit"><?=$icons['editnews']?></a><?php
		}
	?></div>
	<div class="kozlemenyek">
		<div class='szovegtartalom'>
			<div class='kozlemenyszoveg'>
				<small><b>Szerző: <?=$kozlemeny['nev']?></b><br><i><?=$kozlemeny['ido']?></i></small><br><br>
				<?=$kozlemeny['bevezetes']?>
				<br><?=$kozlemeny['szovegtorzs']?>
			</div>
		</div>
	</div><?php
}
