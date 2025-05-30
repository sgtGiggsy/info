$(".kattinthatotr").click(function() {
    window.document.location = $(this).data("href");
    //console.log("ktr");
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

function verifyExist(datalist, field, tooltipid)
{
    list = document.getElementById(datalist);
    listlength = list.options.length

    input = document.getElementById(field);
    filter = input.value.toUpperCase();

    for (i = 0; i < listlength; i++)
    {
        var popup = document.getElementById(tooltipid);
        if(filter == list.options[i].value && filter != "")
        {
            popup.classList.add("show");
            //setTimeout(function(){ popup.className = popup.className.replace("show", ""); }, 6000);
            break;
        }
        else
        {
            popup.className = popup.className.replace("show", "");
        }
    }
}

function rejtMutat(id) {
    if(document.getElementById(id).style.display == "")
        document.getElementById(id).style.display = "none";
    else
        document.getElementById(id).style.display = "";
}

function subRejtMutat(id) {
    let submenuk = document.getElementsByClassName("leftmenu-sub");
    let submenuszam = submenuk.length;

    for(let i = 0; i < submenuszam; i++)
    {
        if(submenuk[i].id == id && submenuk[i].style.display == "none")
            submenuk[i].style.display = "";
        else
            submenuk[i].style.display = "none";
    }
}

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

function showToaster(message, success = true) {
    $("#snackbar").html(message)
    let snackbar = document.getElementById("snackbar");
    if(!success)
        snackbar.style.backgroundColor = "var(--offline)";
    snackbar.className = "show";
    setTimeout(function(){ snackbar.className = snackbar.className.replace("show", ""); }, 3000);
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

function copyTableToClipboard(idtocopy) {
    const table = document.getElementById(idtocopy);

    // Klónozzuk a táblát, hogy manipulálhassuk
    const clonedTable = table.cloneNode(true);
    let css = "";

    // Hozzáfűzzük a stílusokat egy <style> blokkban
    const htmlWithStyles = `
      <html>
        <head>
          <style>${css}</style>
        </head>
        <body>${clonedTable.outerHTML}</body>
      </html>
    `;
  
    // A Clipboard API-hoz HTML formátumban
    navigator.clipboard.write([
      new ClipboardItem({
        "text/html": new Blob([htmlWithStyles], { type: "text/html" })
      })
    ]).then(() => {
        showToaster("A táblázat sikeresen vágólapra másolva!");
    }).catch(err => {
      showToaster("Hiba történt a vágólapra másolás során:", err);
    });
  }

function copyToClipboard(idtocopy) {
    var copyText = document.getElementById(idtocopy);
    copyText.select();
    copyText.setSelectionRange(0, 99999); // For mobile devices
    if(window.isSecureContext)
    {
        navigator.clipboard.writeText(copyText.value);
        showToaster("Szöveg a vágólapra másolva!");
    }
    else
    {
        showToaster("A vágólapra másolás csak HTTPS-sel betöltött oldalon működik!");
    }
}

function showNewsflash()
{
    let newsflash = document.getElementById("newsflash");
    newsflash.className = "show";
}

function hideNewsflash()
{
    let newsflash = document.getElementById("newsflash");
    newsflash.className = newsflash.className.replace("show", "");
}

function confirmSend(text, link)
{
    let conf = confirm(text);
    if(conf)
    {
        window.document.location = link;
    }
}

function userDeviceParams(loginid) {
    $.post(RootPath + "/bejelentkezesdb",
    {
        loginid: loginid,
        gepnev: window.location.hostname,
        felbontas: window.screen.width + "x" + window.screen.height
    });
}

function dropdownRejt(selectid) {
    let x = document.getElementById(selectid);
    x.classList.remove("visible");
    x.classList.add("hidden");
}

function dropdownMutat(selectid) {
    let x = document.getElementById(selectid);
    if(!x.classList.contains("visible")){
        x.classList.add("visible");
        if(x.classList.contains("hidden"))
        {
            x.classList.remove("hidden");
        }
    }
    else
    {
        x.classList.remove("visible");
        x.classList.add("hidden");
    }
}

function vertButton(id, buttonid) {
    let elem = document.getElementById(id);
    let button = document.getElementById(buttonid);
    if(elem.style.width == '0px')
    {
        elem.style.width = 'unset';
        elem.style.visibility = 'visible';
        button.innerHTML = button.getAttribute('data-open');
    }
    else
    {
        elem.style.width = '0px';
        elem.style.visibility = 'hidden';
        button.innerHTML = button.getAttribute('data-closed');
    }
}
