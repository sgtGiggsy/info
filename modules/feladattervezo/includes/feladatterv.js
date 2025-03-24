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

function szerkesztNyit(id, btnid) {
    let szerk = document.getElementById(id);
    let addbtn = document.getElementById('addbutton-' + btnid);
    let removebtn = document.getElementById('removebutton-' + btnid);
    if(szerk.style.display == 'none') {
        szerk.style.display = '';
        addbtn.style.display = 'none';
        removebtn.style.display = '';
    }
    else {
        szerk.style.display = 'none';
        addbtn.style.display = '';
        removebtn.style.display = 'none';
    }
}