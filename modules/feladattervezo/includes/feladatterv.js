function elemNyit(id) {
    var elem = document.getElementById('leiras-' + id);
    if(elem.style.display == 'none')
    {
        document.getElementById('fajlok-' + id).style.display = '';
        document.getElementById('kommentek-' + id).style.display = '';
        document.getElementById('feladatnev-' + id).style.paddingBottom = '0';
        elem.style.display = '';
    }
    else
    {
        document.getElementById('fajlok-' + id).style.display = 'none';
        document.getElementById('kommentek-' + id).style.display = 'none';
        document.getElementById('feladatnev-' + id).style.paddingBottom = '';
        elem.style.display = 'none';
    }
}

function elemFelkeres(id) {
    window.location.href = RootPath + "/feladatterv/" + id;
}

function szerkesztNyit(id) {
    document.getElementById(id).style.display = "";
}