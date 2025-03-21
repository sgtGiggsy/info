function confirmFinalize()
{
    var x = confirm("Biztosan rögzíteni akarod a módosításokat?\nA rögzítést követően nem lehet már a változásjelentési exportot megcsinálni!");
    if (x)
    {
        window.location.href = RootPath + "/telefonkonyv/valtozasok?action=confirmchanges"
    }
    else
    {
        return false;
    }
}

function openKorabbi()
{
    korabbi = document.getElementById('korabbikorok');
    korabbi.style.display = "block";
}

function setAllapotPartial()
{
    allapotvaltozas = document.getElementById("allapot");
    allapotvaltozas.value = 2;
    admincomment = document.getElementById("adminmegjegyzes");
    admincomment.required = true;
    admincommentlabel = document.getElementById("admincommentlabel");
    admincommentlabel.innerHTML = "Adminisztrátori megjegyzés*";
}

function confirmDiscard(discardid)
{
    admincomment = document.getElementById("adminmegjegyzes");
    if(!admincomment.value)
    {
        window.alert("Nem adtál magyarázatot az elvetés okáról!");
        return false;
    }
    else
    {
        var x = confirm("Biztosan el akarod vetni a móodításokat?");
        if (x)
            window.location.href= RootPath + "/telefonkonyv/felulvizsgalat&action=discard&discardid=" + discardid + "&adminmegjegyzes="+admincomment.value
        else
            return false;
    }
}

function refreshList() {
    let xhttp = new XMLHttpRequest();
    let csopid, eredeti;
    csopid = document.getElementById("csoport").value;
    eredetielem = document.getElementById("eredetisor");
    eredeti = eredetielem.value;
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("sorrend").innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", RootPath + "/api/unsafe?modulnev=telefonkonyv&tipus=beosztaslist&csoport=" + csopid + "&eredeti=" + eredeti + "&novaltozatlan", true);
    xhttp.send();
}

function restoreOriginal(inputid, origvalue)
{
    input = document.getElementById(inputid);
    input.value = origvalue;
}

function switchBeosztas() {
    let i, reszletes, egyszeru;

    reszletes = document.getElementsByClassName("reszletes");
    egyszeru  = document.getElementsByClassName("egyszeru");

    if(document.getElementById("beosztasreszletes").style.display == "none")
    {
        document.getElementById("beosztasalap").style.display = "none";
        document.getElementById("beosztasreszletes").style.display = "";
        document.getElementById("szerkbeo").value = "1";

        for(i = 0; i < reszletes.length; i++)
        {
            reszletes[i].style.display = "";
        }

        for(i = 0; i < egyszeru.length; i++)
        {
            egyszeru[i].style.display = "none";
        }
    }
    else
    {
        document.getElementById("beosztasalap").style.display = "";
        document.getElementById("beosztasreszletes").style.display = "none";
        document.getElementById("szerkbeo").value = "";

        for(i = 0; i < reszletes.length; i++)
        {
            reszletes[i].style.display = "none";
        }

        for(i = 0; i < egyszeru.length; i++)
        {
            egyszeru[i].style.display = "";
        }
    }
}

function checkIfNew(felhasznaloid)
{
    if(document.getElementById('ujbeo').selected)
    {
        requireModositasOka();
        
        beosztasnev = document.getElementById('beosztasnev');
        beosztasnev.value = null;

        beosztasnevcimke = document.getElementById('beosztasnevcimke');
        beosztasnevcimke.textContent = "Beosztás megnevezése*";

        csoport = document.getElementById('csoport');
        csoport.value = null;

        sorrend = document.getElementById('sorrend');
        sorrend.value = null;

        switchBeosztas();
    }
    else
    {
        let xhttp = new XMLHttpRequest();
        let beosztas = document.getElementById('beosztas');
        elem = beosztas.selectedIndex;
        beoid = beosztas.value;
        beosztasnev = document.getElementById('beosztasnev');
        beosztasnev.value = beosztas[elem].textContent;
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("csoport").innerHTML = this.responseText;
                }
        };
        xhttp.open("GET", RootPath + "/api/unsafe?modulnev=telefonkonyv&tipus=csoportlist&felhid=" + felhasznaloid + "&beoid=" + beoid, true);
        xhttp.send();
        
        setTimeout(() => { refreshList(); }, 500);
    }
}

function checkIfAvailable() {
    let select = document.getElementById("beosztas");
    let index = select.selectedIndex;
    if(select[index].title == "Foglalt")
    {
        let conf = confirm("A nyilvántartás szerint az adott beosztás foglalt, vagy az adminisztrátor még nem fogadta el a módosítást, ami szabaddá teszi.\nBiztos vagy benne, hogy erre a beosztásra szeretnéd a dolgozót rögzíteni?")
        if(conf)
            legutobbi = index;
        else
            select.selectedIndex = legutobbi;
    }
    else
    {
        legutobbi = index;
    }
}
function onlyNumberKey(evt) {
    var ASCIICode = (evt.which) ? evt.which : evt.keyCode
    if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
        return false;
    return true;
}

function addDash(szam) {
    telszam = szam.value;
    console.log(telszam.length);
    if(telszam.length == 3)
        szam.value = telszam + "-";
}

function delUser() {
    requireModositasOka();

    nevmezo = document.getElementById('nev');
    nevmezo.value = "";
    nevmezo.required = false;

    nevcimke = document.getElementById('nevlabel');
    nevcimke.textContent = "Név";

    elotag = document.getElementById('elotag');
    elotag.value = null;

    titulus = document.getElementById('titulus');
    titulus.required = false;
    titulus.value = null;

    tituluscimke = document.getElementById('tituluscimke');
    tituluscimke.textContent = "Titulus";

    rendfokozat = document.getElementById('rendfokozat');
    rendfokozat.required = false;
    rendfokozat.value = null;

    rendfokozatcimke = document.getElementById('rendfokozatcimke');
    rendfokozatcimke.textContent = "Rendfokozat";

    mobil = document.getElementById('mobil');
    mobil.value = null;
}

function delBeosztas(){
    var x = confirm("Biztosan törölni akarod a beosztást?");
        if (x)
        {
            let remove = document.getElementById("removebeo");
            let button = document.getElementById("beodelbutton")
            remove.value = "1";
            button.textContent = "!!! BEOSZTÁS TÖRLÉSRE KIJELÖLVE !!!";
            let notrequired =  [
                document.getElementById('nev'),
                document.getElementById('titulus'),
                document.getElementById('rendfokozat'),
                document.getElementById('belsoszam')
            ];
            for(let i = 0; i < notrequired.length; i++)
            {
                notrequired[i].required = false;
            }
            requireModositasOka();
        }
        else
            return false;
}

function requireModositasOka() {
    modositasoka = document.getElementById('modositasoka');
    modositasoka.required = true;

    modositascimke = document.getElementById('modositasokcimke');
    modositascimke.textContent = "Módosítás oka*";
}

function refreshSelections() {
    let xhttp = new XMLHttpRequest();
    let beoid, eredeti;
    beoid = document.getElementById("beosztas").value;
    if(beoid != 0)
    {
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var jsonObj = JSON.parse(this.responseText);
                //console.log(jsonObj[0].sorrend);
                document.getElementById("sorrend").value = jsonObj[0].sorrend;
                document.getElementById("belsoszam").value = jsonObj[0].belsoszam;
                document.getElementById("belsoszam2").value = jsonObj[0].belsoszam2;
                document.getElementById("fax").value = jsonObj[0].fax;
                document.getElementById("kozcelu").value = jsonObj[0].kozcelu;
                document.getElementById("kozcelufax").value = jsonObj[0].kozcelufax;
            }
        };
        xhttp.open("GET", RootPath + "/api/unsafe?modulnev=telefonkonyv&tipus=refreshselections&beoid=" + beoid, true);
        xhttp.send();
    }
}

function valtozasokSzurese() {
    let listaszur = document.getElementById("valtozasszures")
    window.location.href = RootPath + "/telefonkonyv/valtozasok?valtozasszures=" + listaszur.value;
}

function gyorsJovahagyas(id, action) {
    let allapotinp = document.getElementById('allapot-' + id);
    let priority = document.getElementById('prioritylevel-' + id);
    let beonev = document.getElementById('beosztas-' + id).value;
    let uzenet;
    let csoportdata = document.getElementById("csoport-" + id).value;
    allapotinp.value = action;
    rejtMutat('valaszto-' + id);
    if(action == 1)
    {
        uzenet = "Változás sikeresen jóváhagyva"
        priority.classList.remove('kritikus');
        priority.classList.add('halaszthato');
    }
    else
    {
        uzenet = "Változás sikeresen elutasítva"
        priority.classList.remove('halaszthato');
        priority.classList.add('kritikus');
    }
    
    $.post(RootPath + "/telefonkonyv/valtozas?action=quickapprove",
        {
            id: id,
            csoport: csoportdata,
            allapot: action,
            beosztasnev: beonev
        },
        function(data, status){
            if(status == "success")
                showToaster(uzenet);
            else
                showToaster("!!! A beküldött változás engedélyezése sikertelen !!!", false);
                //alert("Data: " + data + "\nStatus: " + status);
        }
    );    
}

if(typeof jelenbeo !== 'undefined')
{
    let legutobbi = jelenbeo;
}

window.addEventListener("load", (event) => {
    let sorrend = document.getElementById('sorrend');
    // A sorrend.title csak a változás felülvizsgálatnál létezik, ahol NEM szabad betöltéskor listát frissíteni
    if(sorrend != null && sorrend.value != 999 && sorrend.title != "Sorrend")
    {
        refreshList();
    }

    if(typeof beosztaskapcs === 'undefined')
    {
        if(document.getElementById("beosztasreszletes") != null)
            switchBeosztas();
    }
});

if(typeof onloadfelugro !== 'undefined')
{
    window.addEventListener("load", (event) => {
        var x = confirm(onloadfelugro);
        if(!x)
            window.location.href = RootPath + "/telefonkonyv/valtozasok";
    })
}