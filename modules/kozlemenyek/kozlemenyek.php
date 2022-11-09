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
	?><button type="button" onclick="location.href='<?=$RootPath?>/kozlemeny?action=addnew'">Új közlemény közzététele</button><?php
}
?><div class="oldalcim">Közlemények</div><?php
if(mysqli_num_rows($result) > 0)
{
	?><div class="ketharmad"><?php
		$fo = 2;
		$darab = $result->num_rows;
		$i = 1;
		foreach ($result as $x)
		{
			if($i < $fo + 1)
			{
				if($i == 1)
				{
					?><div class='kozlemeny'><?php
				}
					?><div class='kozlemenyfej'>
						<h2><a href='<?=$RootPath?>/kozlemeny/<?=$x['link']?>'><?=$x['cim']?></a></h2>
					</div>
					<div class='szovegtartalom'>
						<small><b>Szerző: <?=$x['nev']?></b><br><i><?=$x['ido']?></i></small><br><br>
						<?=$x['bevezetes']?>
						<?=$x['szovegtorzs']?><?php
						if($csoportir || ($sajatir && $x['szerzo'] == $felhasznaloid))
						{
							?><a href='<?=$RootPath?>/kozlemeny/<?=$x['id']?>?action=edit'>Szerkesztés</a><?php
						}
					?></div><?php
				if ($i != $fo)
				{
					?><div class='elvalaszto'></div><?php
				}
				if($i == $fo)
				{
					?></div><?php
				}
			}
			else
			{
				if($i == $fo + 1)
				{
					?><div class="kozlemenyside"><?php
				}
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
								?><a class="right" href='<?=$RootPath?>/kozlemeny/<?=$x['id']?>?action=edit'>Szerkesztés</a><?php
							}
						?></div>
					</div><?php
				if ($i != $darab)
				{
					?><div class='elvalaszto'></div><?php
				}
				if($i == $darab)
				{
					?></div><?php	
				}
			}
			
			$i++;
		}
	?></div><?php
}