document.getElementById('telephely').onchange = function() {
    select = document.getElementById('alakulat');
    select.value = '';
};

document.getElementById('alakulat').onchange = function() {
    select = document.getElementById('telephely');
    select.value = '';
};

function switchNightMode()
{
    var nightmode = document.getElementById('szinsema').checked;
    if(nightmode)
    {
        document.getElementById('szinsema').checked = false;
    }
    else
    {
        document.getElementById('szinsema').checked = true;
    }
}