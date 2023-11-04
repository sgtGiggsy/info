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
            popup.classList.add("show");
            setTimeout(function(){ popup.className = popup.className.replace("show", ""); }, 6000);
        }
    }
}

function rejtMutat(id) {
    if(document.getElementById(id).style.display == "")
    {
        document.getElementById(id).style.display = "none";
    }
    else
    {
        document.getElementById(id).style.display = "";
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
        url: RootPath + "/ertesites?action=checkednotif",
    });
}

function seenAllNotif() {
    $.ajax({
        type: "POST",
        url: RootPath + "/ertesites?action=seenallnotif",
    });

    document.getElementById("notifcount").style.display = "none"
}

function seenNotif(notifid) {
    $.ajax({
        type: "POST",
        url: RootPath + "/ertesites?action=seennotif&notifid=" + notifid,	
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