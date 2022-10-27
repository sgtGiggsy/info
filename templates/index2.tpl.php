<!DOCTYPE HTML>
<html xml:lang="hu" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=0.85">
	<meta name="description" content="<?=$ablakcim?>">
	<script src="<?=$RootPath?>/includes/jquery.min.js"></script>
	<script src="<?=$RootPath?>/includes/tinymce/tinymce.min.js"></script>
	<link rel="shortcut icon" href="<?=$RootPath?>/favicon.ico">
	<link rel="stylesheet" href="<?=$RootPath?>/style2.css" type="text/css">
	<title><?=$ablakcim . " - " . $currentpage['cimszoveg']?></title>
</head>

<body>
	<div class="wrapper">
		<!-- Tartalom -->
		<div class="content">
			<div class="right"><a href="<?=$RootPath?>/bugreport?oldal=<?=$current?>">Hiba jelzése</a></div><?php
			// Alkalomadtán szépíteni rajta, mert így most elég hányaveti
				if($currentpage['oldal'] == "fooldal")
				{
					include("./{$currentpage['url']}.php");
				}
				elseif($_GET['page'] == $currentpage['oldal'])
				{
					include("./{$currentpage['url']}.php");
				}
				elseif($_GET['page'] == $currentpage['gyujtooldal'])
				{
					include("./{$currentpage['gyujtourl']}.php");
				}
				elseif($_GET['page'] == $currentpage['dboldal'])
				{
					include("./{$currentpage['dburl']}.php");
				}
				elseif($_GET['page'] == $currentpage['szerkoldal'])
				{
					include("./{$currentpage['szerkoldal']}.php");
				}
		?></div>

		<!-- Fejléc -->
		<div class="topmenubase"></div><?php include('./templates/header2.tpl.php'); ?>

		<!-- Menürész -->
		<?php $menuterulet = 1; include('./includes/menu2.inc.php'); ?>		

		<!-- Lábléc -->
		<div class="bottom-line"><p><a href="mailto:kiraly.bela@mil.hu">© Király Béla ftőrm <script>document.write(new Date().getFullYear())</script></a></p></div>
		<div id="snackbar"></div>
	</div>
</body>
<script>
	function sortTable(n, t, tname) {
		var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
		table = document.getElementById(tname);
		switching = true;
		if(t == "s") { dir = "asc"; } else { dir = "desc"; }
		while (switching) {
			switching = false;
			rows = table.rows;
			rowcount = rows.length - 1;
			if(rows[rowcount].getElementsByTagName("TH")[1])
			{
				rowcount = rows.length - 2;
			}
			for (i = 1; i < rowcount; i++) {
				shouldSwitch = false;
				if(!rows[i].getElementsByTagName("TD")[n])
				{
					i++;
				}
				x = rows[i].getElementsByTagName("TD")[n];
				y = rows[i + 1].getElementsByTagName("TD")[n];
				if(t == "s")
				{
					if (dir == "asc") {
						if((x.innerHTML.toLowerCase().localeCompare(y.innerHTML.toLowerCase(), navigator.languages[0] || navigator.language, {numeric: true, ignorePunctuation: true})) > 0)
						{
							shouldSwitch = true;
							break;
						}
					} else if (dir == "desc") {
						if((x.innerHTML.toLowerCase().localeCompare(y.innerHTML.toLowerCase(), navigator.languages[0] || navigator.language, {numeric: true, ignorePunctuation: true})) < 0)
						{
							shouldSwitch = true;
							break;
						}
					}
				}
				else if(t == "i")
				{
					if (dir == "asc") {
						if (Number(x.innerHTML) < Number(y.innerHTML)) {
						shouldSwitch = true;
						break;
						}
					} else if (dir == "desc") {
						if (Number(x.innerHTML) > Number(y.innerHTML)) {
						shouldSwitch = true;
						break;
						}
					}
				}
			}
			if (shouldSwitch) {
			rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
			switching = true;
			switchcount ++;
			} else {
			if(t == "s") {
					if (switchcount == 0 && dir == "asc") {
					dir = "desc";
					switching = true;
					}
			}
			else
				{
					if (switchcount == 0 && dir == "desc") {
					dir = "asc";
					switching = true;
					}
				}  
			}
		}
	};

	function filterTable(szures, tablazat, oszlop)
	{
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById(szures);
		filter = input.value.toUpperCase();
		table = document.getElementById(tablazat);
		tr = table.getElementsByTagName("tr");

		for (i = 0; i < tr.length; i++)
		{
			td = tr[i].getElementsByTagName("td")[oszlop];
			if (td)
			{
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1)
				{
					if(tr[i].style.color == filter)
					{
						tr[i].style.display = "";
						tr[i].style.color = "";
					}
				}
				else
				{
					tr[i].style.display = "none";
					tr[i].style.color = filter;
				}
			}       
		}
	}

	$(document).ready(function($) {
		$(".kattinthatotr").click(function() {
		window.document.location = $(this).data("href");
			});
	});

	$(document).ready(function($) {
		$(".kattinthatotr-1").click(function() {
		window.document.location = $(this).data("href");
			});
	});

	$(document).ready(function($) {
		$(".kattinthatotr-2").click(function() {
		window.document.location = $(this).data("href");
			});
	});

	function rejtMutat(id) {
		if(document.getElementById(id).style.display == "block")
		{
			document.getElementById(id).style.display = "none"
		}
		else
		{
			document.getElementById(id).style.display = "block";
		}
	};

	function showToaster(message) {
		$("#snackbar").html(message)
        // Get the snackbar DIV
        var x = document.getElementById("snackbar");

        // Add the "show" class to DIV
        x.className = "show";

        // After 3 seconds, remove the show class from DIV
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    }

	function showPopup(id) {
        // Get the snackbar DIV
        var x = document.getElementById(id);

        // Add the "show" class to DIV
		if(x.className == "show")
		{
			x.className = x.className.replace("show", "");
		}
		else
		{
			x.className = "show";
		};

        // After 3 seconds, remove the show class from DIV
        //setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    }

	function hidePopup(id) {
        var x = document.getElementById(id);
        x.className = x.className.replace("show", "");
    }

	function showSlideIn(id = null) {
		var jelenlegi;

		// Jelenleg statikusan maximum 4 beúszó elemet tud kezelni a függvény
		for(var i = 1; i <= 4; i++)
		{
			var x = document.getElementById("slidein-" + i);

			// Ha létezik, és jelenleg látszik: elrejt
			if(x && x.className == "show")
			{
				x.className = x.className.replace("show", "hide");
			}
			// Ha létezik, nem látszik, és ez a megjeleníteni kívánt elem, megjelenítjük
			else if(i == id)
			{
				jelenlegi = x;
				x.className = "show";
			};
		}

		// Ha az egér nem megy a megjelenített menü fölé 3 másodpercen belül: elrejt
		if(jelenlegi && jelenlegi.className == "show")
		{
			setTimeout(
				function(){
					if($("#slidein-" + id + ":hover").length == 0)
					{
						jelenlegi.className = jelenlegi.className.replace("show", "hide");
					}
				}, 3000
			);
		}
	}

	function hideSlideIn(id)
	{
		var jelenlegi = document.getElementById("slidein-" + id);
		setTimeout(
				function(){
					if($("#slidein-" + id + ":hover").length == 0)
					{
						jelenlegi.className = jelenlegi.className.replace("show", "hide");
					}
				}, 2000
			);
	}

	function updateNotif() {
		$.ajax({
        type: "POST",
        url: "<?=$RootPath?>/notifseendb?action=checkednotif",
	});
	}

	function seenAllNotif() {
		$.ajax({
        type: "POST",
        url: "<?=$RootPath?>/notifseendb?action=seenallnotif",
	});
	}

	function seenNotif(notifid) {
		$.ajax({
        	type: "POST",
        	url: "<?=$RootPath?>/notifseendb?action=seennotif&notifid=" + notifid,	
		});
	}

	function reloadPageDelay(delaytime) {
		setTimeout(() => { location.reload(); }, delaytime);
	}

	function getMost(dateselect)
    {
        var most = new Date();
        var dd = String(most.getDate()).padStart(2, '0');
        var mm = String(most.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = most.getFullYear();
        var hour = String(most.getHours()).padStart(2, '0');
        var minute = String(most.getMinutes()).padStart(2, '0');

        most = yyyy + '-' + mm + '-' + dd + ' ' + hour + ':' + minute;
        document.getElementById(dateselect).value = most;
    }

	function getMa(dateselect)
    {
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();

        today = yyyy + '-' + mm + '-' + dd;
        document.getElementById(dateselect).value = today;
    }

	<?php
	if(isset($ujoldalcim))
	{
		?>
		document.title = '<?=$ujoldalcim?>'
		<?php
	}

	if(@$succesmessage)
	{
		?>showToaster("<?=$succesmessage?>");<?php
	}

	if(@$sorozatszamok)
	{
		?>
		function checkSorozatszam()
		{
			let sorozatszamok = new Array(
				<?php
					$elso = true;
					foreach($sorozatszamok as $x)
					{
						if($elso)
						{
							echo '"';
							$elso = false;
						}
						else
						{
							echo ', "';
						}
						echo $x['sorozatszam'] . '"';
					}
				?>);
			let sorozatszam = document.getElementById("sorozatszam");

			if (sorozatszamok.includes(sorozatszam.value))
			{
				alert("A megadott sorozatszám már létezik az adatbázisban");
			}
		}
		
		<?php
	}
	
?></script>
</html>