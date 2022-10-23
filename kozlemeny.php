<?php

$result = mySQLConnect("SELECT kozlemenyek.id, link, szerzo, nev, cim, bevezetes, szovegtorzs, ido, cimke
	FROM kozlemenyek
	LEFT JOIN felhasznalok ON kozlemenyek.szerzo = felhasznalok.id
	WHERE link = '$id'");

$kozlemeny = mysqli_fetch_assoc($result);
$ujoldalcim = $ablakcim . " - " . $kozlemeny['cim'];

?><div class="oldalcim"><?=$kozlemeny['cim']?></div>
<div class="kozlemenyek">
	<div class='szovegtartalom'>
		<div class='kozlemenyszoveg'>
			<small><b>Szerző: <?=$kozlemeny['nev']?></b><br><i><?=$kozlemeny['ido']?></i></small><br><br>
			<?=$kozlemeny['bevezetes']?>
			<br><?=$kozlemeny['szovegtorzs']?>
		</div>
	</div>
</div>