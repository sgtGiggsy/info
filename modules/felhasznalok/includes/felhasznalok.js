document.getElementById('telephely').onchange = function() {
    select = document.getElementById('szervezet');
    select.value = '';
};

document.getElementById('szervezet').onchange = function() {
    select = document.getElementById('telephely');
    select.value = '';
};

function switchNightMode()
{
    var nightmode = document.getElementById('szinsema').checked;

    // Create new link Element
    let link = document.createElement('link');
    
    // set the attributes for link element
    link.rel = 'stylesheet';
    link.type = 'text/css';
    link.href = 'dark.css';
    link.id = 'nightmode';

    if(nightmode)
    {
        document.getElementById('szinsema').checked = false;
        let element = document.getElementById('nightmode');
        element.parentNode.removeChild(element);
    }
    else
    {
        document.getElementById('szinsema').checked = true;
        document.getElementsByTagName('HEAD')[0].appendChild(link);
    }
}