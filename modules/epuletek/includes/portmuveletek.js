function addSelect(azonosito, sorszam) {
    if(sorszam < 5) {
        var origselect = document.querySelector('#selecttoadd');
        var clone = origselect.cloneNode(true);
        var elem = document.getElementById('telefondiv-' + azonosito + '-' + sorszam);
        elem.style = "display: block;";
        elem.innerHTML += '<select name="telefonszam-' + azonosito + '-' + sorszam + '">' + clone.innerHTML;
        
        var button = document.getElementById('button-' + azonosito);
        var sorsz = sorszam + 1;
        var funct = 'addSelect(' + azonosito + ", " + sorsz + ")";
        button.setAttribute('onClick', funct);
    }
}

function atHurkolas(port) {
    // Nullázás, törölni kell minden korábbi társítást mielőtt az újat felvesszük
    var torlendo = document.getElementsByClassName("hurkok");
    var select = document.getElementById("hurok-" + port);
    var value = select.value;
    
    l = torlendo.length;
    for (i = 0; i < l; i++) {
        var selElmnt = torlendo[i];
        // Töröljük a jelenleg társítani próbált port társításait, illetve az összes portról
        // töröljük a jelenleg társítani próbált portot
        if(selElmnt.value == port || (select != selElmnt && selElmnt.value == value)) {
            selElmnt.value = "";
        }
    }

    var tulold = document.getElementById("hurok-" + value);
    if(tulold) {
        // Ha VAN a kiválasztott porton másik, a felhasználó figyelmeztetése, hogy az a hurok törlésre kerül
        if(tulold.value)
        {
            alert("Port hurkolás eltávolítva a(z) " + tulold.options[tulold.selectedIndex].text + " portról");
        }
        tulold.value = port;
    }

    // Mivel a port önmagával való hurkolásának nincs értelme, így ha ilyesmivel próbálkoznánk,
    // a rendszer azt törli
    if(value == port)
    {
        select.value = "";
    }
}