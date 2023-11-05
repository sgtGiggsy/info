var kovetkezokerdes = kovetkezokerdesszama;
var kovetkezopluszkerdes = 1;

function addUjValasz() {
    var label = document.getElementById('label-' + kovetkezokerdes);
    var textarea = document.getElementById('valasz-' + kovetkezokerdes);
    var checkbox = document.getElementById('helyes-' + kovetkezokerdes);

    var origlabel = document.querySelector('#pluszlabel');
    var clonelabel = origlabel.cloneNode(true);
    var origcheckbox = document.querySelector('#pluszcheckbox');
    var clonecheckbox = origcheckbox.cloneNode(true);
    var origcbdel = document.querySelector('#pluszcbdel');
    var clonecbdel = origcbdel.cloneNode(true);
    //var label = clone.getElementById('ujvalaszlabel');
    
    //console.log(clone);
    var elem = document.getElementById('pluszkerdes-' + kovetkezopluszkerdes);
    elem.innerHTML = clonelabel.innerHTML;
    elem.style.display = 'unset';
    var elem2 = document.getElementById('pluszhelyes-' + kovetkezopluszkerdes);
    elem2.innerHTML = clonecheckbox.innerHTML;
    elem2.style.display = 'unset';
    var elem3 = document.getElementById('plusztorles-' + kovetkezopluszkerdes);
    elem3.innerHTML = clonecbdel.innerHTML;
    elem3.style.display = 'unset';

    kovetkezokerdes++;
    kovetkezopluszkerdes++;

    label.id = 'label-' + kovetkezokerdes;
    label.for = 'valasz-' + kovetkezokerdes;
    label.textContent = 'Válasz ' + kovetkezokerdes + ':';
    textarea.id = 'valasz-' + kovetkezokerdes;
    checkbox.id = 'helyes-' + kovetkezokerdes;
    checkbox.value = kovetkezokerdes;
    checkbox.name = kovetkezokerdes;

    if(kovetkezopluszkerdes == 5)
    {
        var button = document.getElementById('button-valasz');
        button.disabled = true;
        button.style.display = 'none';
    }
}

function checkKivalasztas()
{
    var cboxcount = 1;
    let voltjeloles = false;

    while (document.getElementById('helyes-' + cboxcount))
    {
        checkbox = document.getElementById('helyes-' + cboxcount);
        if(checkbox.checked == true)
        {
            voltjeloles = true;
        }
        cboxcount++;
    }
    
    if(!voltjeloles)
    {
        hideOverlay();
        return confirm('Nem jelölt meg egyetlen válaszlehetőséget sem helyesként.\nBiztosan így szeretné elmenteni a kérdést?');
    }
    else
    {
        return true;
    };
}

function hideOverlay()
{
    setTimeout(
        function(){
            hideProgressOverlay();
        }, 1000
    );
}