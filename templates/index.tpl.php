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
	<link rel="stylesheet" href="<?=$RootPath?>/style.css" type="text/css">
	<title><?=$ablakcim . " - " . $currentpage['cimszoveg']?></title>
</head>

<body>
<div class="wrapper">	
<!-- Fejléc -->
	<div class="header">
		<?php include('./templates/header.tpl.php'); ?>
	</div>
<!-- Menürész -->
	<div class="menubar"></div>
		<div class="menu">
			<?php $menuterulet = 1; include('./includes/menu.inc.php'); ?>
		</div>
	
<!-- Oldaltörzs -->	
<!-- Tartalom -->
    <div class="content">
		<?php include("./{$currentpage['url']}.php");	?>
	</div>

<!-- lábléc -->
    <div class ="footer">
		<?php
			include("./templates/footer.tpl.php");
		?>
	</div>
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
						if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
						shouldSwitch = true;
						break;
						}
					} else if (dir == "desc") {
						if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
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

	$(document).ready(function($) {
		$(".kattinthatotr").click(function() {
		window.document.location = $(this).data("href");
			});
	});

	function rejtMutat(id) {
		if(document.getElementById(id).style.display == "grid")
		{
			document.getElementById(id).style.display = "none"
		}
		else
		{
			document.getElementById(id).style.display = "grid";
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
</script>
</html>