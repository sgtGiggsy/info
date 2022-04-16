<!DOCTYPE HTML>
<html xml:lang="hu" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=0.85">
	<meta name="description" content="<?=$ablakcim?>">
	<script src="<?=$RootPath?>/includes/jquery.min.js"></script>
	<script src="<?=$RootPath?>/includes/greedynav/greedynav.js"></script>
	<script src="<?=$RootPath?>/includes/tinymce/tinymce.min.js"></script>
	<link rel="shortcut icon" href="<?=$RootPath?>/favicon.ico">
	<link rel="stylesheet" href="<?=$RootPath?>/style2.css" type="text/css">
	<title><?=$ablakcim . " - " . $currentpage['cimszoveg']?></title>
</head>

<body>
<div class="wrapper">	
<!-- Fejléc -->
<div class="content">
		<div class="right"><a href="<?=$RootPath?>/bugreport?oldal=<?=$current?>">Hiba jelzése</a></div>
		<?php include("./{$currentpage['url']}.php"); ?>
	</div>

	<div class="topmenubase"></div><?php include('./templates/header2.tpl.php'); ?>
	<!--<div class="header">
		
	</div>-->
<!-- Menürész -->
	<!--<div class="menubar"></div>-->
	<?php $menuterulet = 1; include('./includes/menu2.inc.php'); ?>
		<!--<div class="menu">
			
		</div>-->
	
<!-- Oldaltörzs -->	
<!-- Tartalom -->
    

<!-- lábléc -->
    <div class ="footer">
		<?php
			//include("./templates/footer.tpl.php");
		?>
	</div>
	<div class="bottom-line"><p><a href="mailto:kiraly.bela@mil.hu">© Király Béla ftőrm <script>document.write(new Date().getFullYear())</script></a></p></div>
</div>
<div id="snackbar"></div>
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

	function showProfile() {
        // Get the snackbar DIV
        var x = document.getElementById("profilpopup");

        // Add the "show" class to DIV
        x.className = "show";

        // After 3 seconds, remove the show class from DIV
        //setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    }

	function hideProfile() {
        // Get the snackbar DIV
        var x = document.getElementById("profilpopup");

        // After 3 seconds, remove the show class from DIV
        x.className = x.className.replace("show", "");
    }
</script>
</html>