<?php
if(isset($_GET['kategoria']))
{
	$kategoria = $_GET['kategoria'];
	$where = "AND cimke = $kategoria";
}
else
{
	$where = null;
}

$result = mySQLConnect("SELECT kozlemenyek.id, link, szerzo, nev, cim, bevezetes, szovegtorzs, ido, cimke
	FROM kozlemenyek
	LEFT JOIN felhasznalok ON kozlemenyek.szerzo = felhasznalok.id
	WHERE publikalt = 1 $where
	ORDER BY kozlemenyek.id DESC
	LIMIT 4");

$legfrissebb = mysqli_fetch_assoc($result);
if($mindir) 
{
	?><button type="button" onclick="location.href='<?=$RootPath?>/kozlemenyszerkeszt'">Új közlemény közzététele</button><?php
}
?><div class="oldalcim">Közlemények</div><?php
if(mysqli_num_rows($result) > 0)
{
	?><div class="ketharmad">
		<div class='kozlemeny'>
			<div class='kozlemenyfej'>
				<h2><a href='<?=$RootPath?>/kozlemeny/<?=$legfrissebb['link']?>'><?=$legfrissebb['cim']?></a></h2>
			</div>
			<div class='szovegtartalom'>
				<small><b>Szerző: <?=$legfrissebb['nev']?></b><br><i><?=$legfrissebb['ido']?></i></small><br><br>
				<?=$legfrissebb['bevezetes']?>
				<?=$legfrissebb['szovegtorzs']?><?php
				if($csoportir || ($sajatir && $legfrissebb['szerzo'] == $felhasznaloid))
				{
					?><a href='<?=$RootPath?>/kozlemenyszerkeszt/<?=$legfrissebb['id']?>'>Szerkesztés</a><?php
				}
			?></div>
		</div>
		<div class="kozlemenyside"><?php
			$darab = $result->num_rows;
			$i = 1;
			foreach ($result as $x)
			{
				if($i != 1)
				{
					?><div class='kozlemeny'>
						<div class='kozlemenyfej'>
							<h2><a href='<?=$RootPath?>/kozlemeny/<?=$x['link']?>'><?=$x['cim']?></a></h2>
						</div>
						<div class='szovegtartalom'>
							<?=$x['bevezetes']?>
							<?php
							if ($x['szovegtorzs'])
							{
								?><a class="left" href='<?=$RootPath?>/kozlemeny/<?=$x['link']?>'>Tovább...</a><?php
							}
							if($csoportir || ($sajatir && $x['szerzo'] == $felhasznaloid))
							{
								?><a class="right" href='<?=$RootPath?>/kozlemenyszerkeszt/<?=$x['id']?>'>Szerkesztés</a><?php
							}
						?></div><?php
						if ($i != $darab)
						{
							echo "<div class='elvalaszto'></div>"; 
						}
						$i++
					?></div><?php
				}
			}
		?></div>
	</div><?php
}