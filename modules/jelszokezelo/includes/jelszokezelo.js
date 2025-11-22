var ido = 300 - unlockedtime;
const visszaszamlalas = document.getElementById('countd');
const visszaszulo = visszaszamlalas.parentElement;
const Alert = Swal.mixin({
  theme: 'material-ui-dark',
});

pop = JSON.parse(popup);
if(pop.type != null) {
    Alert.fire({
        text:   pop.message,
        icon:   pop.type
    })
}

const link = document.createElement('link');
link.rel = 'stylesheet';
link.href = RootPath + '/external/sweetalert/themes/material-ui.css';
document.head.appendChild(link);

if(unlockedtime < 300) {
    visszaszamlalas.innerText = "";
    visszaszulo.className = "topmenuitem-nohover";
    document.addEventListener("DOMContentLoaded", visszaSzamlalo);
}

function visszaSzamlalo()
{
    let perc = Math.floor(ido / 60);
    let masodperc = ido % 60;
    let idoString = String(perc).padStart(2, 0) + ":" + String(masodperc).padStart(2, 0);
    
    visszaszamlalas.innerText = idoString;
    if(ido > 0) {
        ido--;
        setTimeout("visszaSzamlalo()", 1000);
    }
    else {
        visszaszamlalas.innerText = "Feloldás";
        visszaszulo.className = "topmenuitem";
    }
}

function setRequired() {
    let newpass = document.getElementById("newpass");
    let newpassrepeat = document.getElementById("newpassrepeat");

    if(newpass.value || newpassrepeat.value) {
        newpass.required = true;
        newpassrepeat.required = true;
    }
    else {
        newpass.required = false;
        newpassrepeat.required = false;
    }
}

function showPass(passid) {
    if(!unlocked){
        Alert.fire({
            text:   "A jelszókezelő zárolva van! Add meg a mesterjelszót a jelszó megjelenítéséhez!",
            icon:   "warning"
        });
    }
    else
    {
        let passcell = document.getElementById('jelszo-' + passid);

        if(passcell.getAttribute("data-shown")) {
            navigator.clipboard.writeText(passcell.innerText);
            showToaster("Jelszó a vágólapra lett másolva");
        }
        else {
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4) {
                    let res = JSON.parse(this.response);
                    if(this.status == 200)
                    {
                        hidePasswords(passid);
                        passcell.innerText = res.eredmeny;
                        passcell.setAttribute("data-shown", true);
                    }
                    else {
                        console.log(this.response);
                        let res = JSON.parse(this.response);
                        Alert.fire({
                            text:   res.eredmeny,
                            icon:   "error"
                        });
                    }
                }
            };
            xhttp.open("GET", RootPath + "/api/unsafe?modulnev=jelszokezelo&jelid=" + passid, true);
            xhttp.send();
        }
    }
}

function hidePasswords(passid) {
    const cells = document.querySelectorAll('[id^="jelszo-"]');
    let current = document.getElementById('jelszo-' + passid);

    cells.forEach(jelszo => {
        jelszo.innerText = "********";
        jelszo.removeAttribute("data-shown");
    });

    setTimeout(function() {
        current.innerText = "********";
        current.removeAttribute("data-shown");
    }, 10000);
}

function enterMasterPass() {
    // Ha zárolt, és nincs kizárásra utaló ötperces időzítő, csak akkor jelenítjük meg a belépési dialógust.
    // Szerver oldalon is tiltva van, tehát ha valaki vissza is hozza magának DOM-ból, belépni akkor sem tud a tiltás lejártáig.
    if(!unlocked && ido < 0) {
        document.getElementById("masterpass-dialog").style.display = "block";
        document.getElementById("masterpass").focus();
    }
}