function addNewFelelos()
{
    szamlalo++;
    var origselect = document.querySelector('#selecttoadd');
    var clone = origselect.cloneNode(true);
    var elem = document.getElementById('felelos-' + szamlalo);
    elem.style.display = 'block';
    elem.innerHTML += clone.innerHTML;
    var ujselect = document.getElementById('felelosnew');
    ujselect.id = 'felelos_sel-' + szamlalo;

    document.getElementById('felelos_sel-' + szamlalo).onchange = function() {
        var ujelem = document.getElementById('felelos_sel-' + szamlalo);
        if(ujelem.value) {
            addNewFelelos();
        }
    };
}

function nullFelelosok()
{
    var felelosok = document.getElementsByClassName('hjegyfelelosok');
    l = felelosok.length;
    for(i = 1; i < l; i++) {
        felelosok[i].value = "";
    }
}

var szamlalo = 1;

if(document.getElementById('felelos_sel-' + szamlalo))
{
    document.getElementById('felelos_sel-' + szamlalo).onchange = function() {
        var ujelem = document.getElementById('felelos_sel-' + szamlalo);
        if(ujelem.value) {
            addNewFelelos();
        }
    }
}

if(document.getElementById('allapottipus'))
{
    document.getElementById('allapottipus').onchange = function() {
        nullFelelosok();
        if(this.value == '26') {
            document.getElementById('felelos').style.display = 'none';
            document.getElementById('halasztas').style.display = 'none';
            document.getElementById('hatarido').style.display = 'none';
            document.getElementById('fajlok').style.display = 'block';
        } else if(this.value == '27') {
            document.getElementById('felelos').style.display = 'none';
            document.getElementById('halasztas').style.display = 'none';
            document.getElementById('fajlok').style.display = 'none';
            document.getElementById('hatarido').style.display = 'block';
        } else if (this.value == '28') {
            document.getElementById('felelos').style.display = 'none';
            document.getElementById('hatarido').style.display = 'none';
            document.getElementById('fajlok').style.display = 'none';
            document.getElementById('halasztas').style.display = 'block';
        } else if (this.value == '29') {
            document.getElementById('hatarido').style.display = 'none';
            document.getElementById('fajlok').style.display = 'none';
            document.getElementById('halasztas').style.display = 'none';
            document.getElementById('felelos').style.display = 'block';
        } else {
            document.getElementById('felelos').style.display = 'none';
            document.getElementById('hatarido').style.display = 'none';
            document.getElementById('halasztas').style.display = 'none';
            document.getElementById('fajlok').style.display = 'none';
        }
    }
}

if(document.getElementById("rovid"))
{
    document.getElementById("rovid").onkeyup = function () {
        if(80 - this.value.length > -1) {
            document.getElementById('szamlalo').innerHTML = "Felhasználható karakterek száma: " + (80 - this.value.length);
        }
        else {
            document.getElementById('szamlalo').innerHTML = "<span class='warning'>A megengedett karakterlimit fölött jár: " + (80 - this.value.length) + "</span>";
        }
    };
}