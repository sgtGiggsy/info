<!DOCTYPE HTML>
<html xml:lang="hu" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=0.85">
	<meta name="description" content="<?=$ablakcim?>">
	<script src="<?=$RootPath?>/includes/jquery.min.js"></script>
	<script src="<?=$RootPath?>/includes/tinymce/tinymce.min.js"></script>
	<link rel="shortcut icon" href="<?=$RootPath?>/favicon.ico"><?php
	if(isset($szemelyes['szinsema']) && $szemelyes['szinsema'])
	{
		?><link rel="stylesheet" href="<?=$RootPath?>/<?=$szemelyes['szinsema']?>.css" type="text/css"><?php
	}
	?><link rel="stylesheet" href="<?=$RootPath?>/style.css" type="text/css">
	<title><?=$ablakcim . " - " . $currentpage['gyujtocimszoveg']?></title>
</head>

<body>
	<div class="wrapper">
		<!-- Tartalom -->
		<div class="content">
			<div class="right"><a href="<?=$RootPath?>/bugreport?oldal=<?=$current?>">Hiba jelzése</a></div><?php
			include('./includes/contentpage.inc.php')
		?></div>

		<!-- Fejléc -->
		<div class="topmenubase" id="topmenuelement"><?php include('./templates/header.tpl.php'); ?></div>

		<!-- Menürész -->
		<?php $menuterulet = 1; include('./includes/menu.inc.php'); ?>		

		<!-- Lábléc -->
		<div class="bottom-line"><p><?=($mindir) ? "Adatbázis hívások száma: " . $dbcallcount . " " : "" ?><a href="mailto:kiraly.bela@mil.hu">© Király Béla ftőrm <script>document.write(new Date().getFullYear())</script></a></p><span id="constatus"></span></div>

		<!-- Betöltés során látható területen kívül eső, illetve nem létező tartalmak -->
		<div id="snackbar"></div>
		<div id="formkuldatfedes" class="formkuldatfedes"><div class="formkuldmessage">Művelet folyamatban, kérlek várj...<br><div class="loader"></div></div></div>
		<img src="<?=$RootPath?>/images/back.png" alt= "Vissza" id="backtotop" onclick="scrollToTop()"/>
	</div>
</body>
<script>

	$(".kattinthatotr").click(function() {
				window.document.location = $(this).data("href");
				console.log("ktr");
		});

	function scrollToTop() {
		document.getElementById('topmenuelement').scrollIntoView({
			behavior: 'smooth'
		});
	};

	window.addEventListener("scroll", (event) => {
		let scroll = this.scrollY;
		var x = document.getElementById("backtotop");

		if(scroll > 1000)
		{
        	x.className = "show";
		}
		if(scroll < 1000 && x.className == "show")
		{
        	x.className = "hide";
		}
	});

	window.addEventListener('load', () => {
		const status = navigator.onLine;
		//console.log(status);
	})

	window.addEventListener('offline', (e) => {
		console.log('offline');
	});

	window.addEventListener('online', (e) => {
		console.log('online');
	});

	<?php
	if(isset($csoportfilter))
	{
		?>
		document.getElementById("<?=$csoportfilter?>").addEventListener("search", function(event) {
			filterCsoport('<?=$csoportfilter?>', '<?=$tipus?>');
		});
		<?php
	}
	?>

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

	function filterTable(szures, tablazat, oszlop, szuloszur = false)
	{
		var input, filter, table, tr, td, i, txtValue, szuloelem;
		input = document.getElementById(szures);
		filter = input.value.toUpperCase();
		table = document.getElementById(tablazat);
		tr = table.getElementsByTagName("tr");

		for (i = 0; i < tr.length; i++)
		{
			td = tr[i].getElementsByTagName("td")[oszlop];
			tdfirst = tr[i].getElementsByTagName("td")[0];
			if(tdfirst && tdfirst.innerHTML && szuloszur)
			{
				szuloelem = tr[i];
				if (szuloelem.title == oszlop || !szuloelem.title)
				{
					szuloelem.style.display = "none";
					szuloelem.title = oszlop;
				}
			}
			if(td)
			{
				txtValue = td.textContent;
				if (txtValue.toUpperCase().indexOf(filter) > -1)
				{
					if(tr[i].title == oszlop || !tr[i].title)
					{
						if(szuloszur && szuloelem.title == oszlop)
						{
							szuloelem.style.display = "";
						}
						tr[i].style.display = "";
					}
				}
				else
				{
					tr[i].style.display = "none";
					tr[i].title = oszlop;
				}
			}

			//console.log(tdfirst.colSpan);
			
		}
	}

	function filterCsoport(szures, tablazat)
	{
		var input, filter, table, tr, i, txtValue;
		input = document.getElementById(szures);
		filter = input.value.toUpperCase();
		table = document.getElementById(tablazat);
		tr = table.getElementsByTagName("tr");

		//console.log(tr.length);
		for (i = 1; i < tr.length; i++)
		{
			txtValue = table.rows[i].className;
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

	function showHideCsoport(csoport, tablazat)
	{
		var filter, table, tr, td, i, txtValue;
		var voltmar = false;
		filter = csoport.toUpperCase();
		table = document.getElementById(tablazat);
		tr = table.getElementsByTagName("tr");

		for (i = 0; i < tr.length; i++)
		{
			txtValue = table.rows[i].id;
			csoportcim = csoport+"0";
			if (txtValue.toUpperCase().indexOf(filter) > -1 && txtValue != csoportcim)
			{
				voltmar = true;
				if(tr[i].style.display == "none")
				{
					tr[i].style.display = "";
				}
				else
				{
					tr[i].style.display = "none";
				}
			}
			else if(voltmar)
			{
				break;
			}
		}
	}

	function verifyExist(datalist, field, tooltipid)
	{
		list = document.getElementById(datalist);
		listlength = list.options.length

		input = document.getElementById(field);
		filter = input.value.toUpperCase();

		for (i = 0; i < listlength; i++)
		{
			if(filter == list.options[i].value)
			{
				var popup = document.getElementById(tooltipid);
				popup.classList.toggle("show");
				setTimeout(function(){ popup.className = popup.className.replace("show", ""); }, 6000);
			}
		}
	}

	<?php
	if(isset($_GET['ertesites']))
	{
		?>
		$(document).ready(function($) {
			seenNotif(<?=$_GET['ertesites']?>);
			var notifcount = document.getElementById("notifcount").textContent;
			notifcount = notifcount - 1;
			document.getElementById("notifcount").textContent = notifcount;
			document.getElementById("notif-<?=$_GET['ertesites']?>").className.replace("notifitem", "notifitem-latta");
		});
		<?php
	}
	?>

	function rejtMutat(id) {
		if(document.getElementById(id).style.display == "block")
		{
			document.getElementById(id).style.display = "none";
		}
		else
		{
			document.getElementById(id).style.display = "block";
		}
	};

	function ipHistory(id) {
		if(document.getElementById(id).style.display == "none")
		{
			document.getElementById(id).style.display = "";
		}
		else
		{
			document.getElementById(id).style.display = "none";
		}
	};

	function showOnlyOne(elotag, id, closecurrent = false) {
		for(var i = 1; i <= 10; i++)
		{
			var x = document.getElementById(elotag + i);

			// Ha létezik, és jelenleg látszik: elrejt
			if((x && x.style.display == "block") && (i != id || (i == id && closecurrent)))
			{
				if(!(i == id && closecurrent))
				{
					x.style.display = "none";
					if(elotag == "beallitas-")
					{
						var mp = document.getElementById("szerkcard-" + i);
						mp.style.backgroundColor = "";
					}
				}
			}
			// Ha létezik, nem látszik, és ez a megjeleníteni kívánt elem, megjelenítjük
			else if(i == id)
			{
				x.style.display = "block";
				if(elotag == "beallitas-")
				{
					var mp = document.getElementById("szerkcard-" + i);
					mp.style.backgroundColor = "var(--infoboxtitle)";
				}
			};
		}
	}

	function upDownConversion(id) {
		var elem = document.getElementById(id);
		if(elem.textContent != "⮝") {
			elem.textContent = "⮝";
		}
		else {
			elem.textContent = "⮟";
		};
	}

	function changeTitle(id, szoveg) {
		var elem = document.getElementById(id);
		elem.textContent = szoveg;
	}

	function showToaster(message) {
		$("#snackbar").html(message)
        var x = document.getElementById("snackbar");
        x.className = "show";
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    }

	function showPopup(id) {
        var x = document.getElementById(id);

		if(x.className == "show")
		{
			x.className = x.className.replace("show", "");
		}
		else
		{
			x.className = "show";
		};
    }

	function hidePopup(id) {
        var x = document.getElementById(id);

		setTimeout(
				function(){
					if($("#" + id + ":hover").length == 0)
					{
						x.className = x.className.replace("show", "");
					}
				}, 700
			);
    }

	function openToDo(id) {
		var jelenlegi;

		// Jelenleg statikusan maximum 4 beúszó elemet tud kezelni a függvény
		for(var i = 1; i <= 100; i++)
		{
			var x = document.getElementById("tennivaloopenclose-" + i);
			var y = document.getElementById("tennivalobody-" + i);
			var z = document.getElementById("prioritas-" + i);

			// Ha létezik, és jelenleg látszik: elrejt
			if(x && x.className == "open")
			{
				x.className = x.className.replace("open", "close");
				y.className = y.className.replace("open", "close");
				z.className = z.className.replace("open", "close");
			}
			// Ha létezik, nem látszik, és ez a megjeleníteni kívánt elem, megjelenítjük
			else if(i == id)
			{
				jelenlegi = x;
				x.className = "open";
				y.className = "open";
				z.className = "open";
			}
			else if(!x)
			{
				break;
			}
		}
	}

	function showSlideIn(id = null, irany = null) {
		var jelenlegi;
		if(irany)
		{
			var animnev = irany;
		}
		else
		{
			var animnev = "slidein-";
		}

		// Jelenleg statikusan maximum 4 beúszó elemet tud kezelni a függvény
		for(var i = 1; i <= 6; i++)
		{
			var x = document.getElementById(animnev + i);

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
					if($("#" + animnev + id + ":hover").length == 0)
					{
						jelenlegi.className = jelenlegi.className.replace("show", "hide");
					}
				}, 3000
			);
		}
	}

	function hideSlideIn(id) {
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

	function enlargeImage(id) {
		var x = document.getElementById(id);

		if(x.className == "enlarge")
		{
			x.className = x.className.replace("enlarge", "shrink");
		}
		else
		{
			x.className = "enlarge";
		};
	}

	function updateNotif() {
		$.ajax({
        type: "POST",
        url: "<?=$RootPath?>/ertesites?action=checkednotif",
	});
	}

	function seenAllNotif() {
		$.ajax({
        	type: "POST",
        	url: "<?=$RootPath?>/ertesites?action=seenallnotif",
		});

		document.getElementById("notifcount").style.display = "none"
	}

	function seenNotif(notifid) {
		$.ajax({
        	type: "POST",
        	url: "<?=$RootPath?>/ertesites?action=seennotif&notifid=" + notifid,	
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

	function showProgressOverlay() {
		var formkuldatfedes = document.getElementById("formkuldatfedes");
		formkuldatfedes.style.display = "block";
	}

	function hideProgressOverlay() {
		var formkuldatfedes = document.getElementById("formkuldatfedes");
		formkuldatfedes.style.display = "none";
	}

	function checkAll(sor, ertek)
	{
		var tr = document.getElementById(sor);
		var ele = tr.querySelectorAll('select');
		for(var i = 0; i < ele.length; i++){
			if(ele[i].type == 'select-one')
			{
				if(ele[i].value == ertek)
				{
					ele[i].value = 0;
				}
				else
				{
					ele[i].value = ertek;
				}
			}
		}
	}

	<?php
	if(isset($_GET['page']) && $_GET['page'] != "aktiveszkoz" && $_GET['page'] != "sohoeszkoz" && $_GET['page'] != "mediakonverter" || ($_GET['page'] == "aktiveszkoz" && isset($_GET['action'])))
	{
		?>
		$("form").on("submit", function (e) {
				hideSlideIn();
				showProgressOverlay();
			});
		<?php
	}

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

	if(@$nyithelp)
	{
		?>rejtMutat('magyarazat');<?php
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

	if(isset($cselect) && $cselect)
	{
		?>
		var x, i, j, l, ll, selElmnt, a, b, c;
		/*look for any elements with the class "custom-select":*/
		x = document.getElementsByClassName("custom-select");
		l = x.length;
		for (i = 0; i < l; i++) {
		selElmnt = x[i].getElementsByTagName("select")[0];
		selElmnt.options[selElmnt.selectedIndex];
		ll = selElmnt.length;
		/*for each element, create a new DIV that will act as the selected item:*/
		a = document.createElement("DIV");
		a.setAttribute("class", "select-selected<?=(@$_GET['page'] == 'aktiveszkoz') ? ' transpinput' : '' ?>");
		a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
		x[i].appendChild(a);
		/*for each element, create a new DIV that will contain the option list:*/
		b = document.createElement("DIV");
		b.setAttribute("class", "select-items select-hide");
		for (j = 1; j < ll; j++) {
			/*for each option in the original select element,
			create a new DIV that will act as an option item:*/
			c = document.createElement("DIV");
			c.innerHTML = selElmnt.options[j].innerHTML;
			if(selElmnt.selectedIndex == j) {
				c.setAttribute("class", "same-as-selected");
			}
			c.addEventListener("click", function(e) {
				/*when an item is clicked, update the original select box,
				and the selected item:*/
				var y, i, k, s, h, sl, yl;
				s = this.parentNode.parentNode.getElementsByTagName("select")[0];
				sl = s.length;
				h = this.parentNode.previousSibling;
				for (i = 0; i < sl; i++) {
				if (s.options[i].innerHTML == this.innerHTML) {
					s.selectedIndex = i;
					h.innerHTML = this.innerHTML;
					y = this.parentNode.getElementsByClassName("same-as-selected");
					yl = y.length;
					for (k = 0; k < yl; k++) {
					y[k].removeAttribute("class");
					}
					this.setAttribute("class", "same-as-selected");
					break;
				}
				}
				h.click();
			});
			b.appendChild(c);
		}
		x[i].appendChild(b);
		a.addEventListener("click", function(e) {
			/*when the select box is clicked, close any other select boxes,
			and open/close the current select box:*/
			e.stopPropagation();
			closeAllSelect(this);
			this.nextSibling.classList.toggle("select-hide");
			this.classList.toggle("select-arrow-active");
			});
		}
		function closeAllSelect(elmnt) {
		/*a function that will close all select boxes in the document,
		except the current select box:*/
		var x, y, i, xl, yl, arrNo = [];
		x = document.getElementsByClassName("select-items");
		y = document.getElementsByClassName("select-selected");
		xl = x.length;
		yl = y.length;
		for (i = 0; i < yl; i++) {
			if (elmnt == y[i]) {
			arrNo.push(i)
			} else {
			y[i].classList.remove("select-arrow-active");
			}
		}
		for (i = 0; i < xl; i++) {
			if (arrNo.indexOf(i)) {
			x[i].classList.add("select-hide");
			}
		}
		}
		/*if the user clicks anywhere outside the select box,
		then close all select boxes:*/
		document.addEventListener("click", closeAllSelect);
		<?php
	}
?></script>
</html>