function elemNyit(id) {
    var elem = document.getElementById('feladat-' + id);
    if(elem.style.height != 'unset')
    {
        document.getElementById('kommentek-' + id).style.display = '';
        document.getElementById('fajlok-' + id).style.display = '';
        document.getElementById('leiras-' + id).style.display = '';
        elem.style.height = 'unset';
    }
    else
    {
        document.getElementById('fajlok-' + id).style.display = 'none';
        document.getElementById('kommentek-' + id).style.display = 'none';
        document.getElementById('leiras-' + id).style.display = 'none';
        elem.style.height = '';
    }
}