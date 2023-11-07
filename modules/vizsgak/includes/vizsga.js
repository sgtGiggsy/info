let ido = 0;
if(typeof hatralevoido !== 'undefined' && hatralevoido)
{
    document.addEventListener("DOMContentLoaded", visszaSzamlalo);
    ido = hatralevoido;
}

function halasztKuldSwitch()
{
    var kuldgomb = document.getElementById('valaszkuld');
    kuldgomb.value = 'Válasz beküldése';
}

function visszaSzamlalo()
{
    var perc = Math.floor(ido / 60);
    var masodperc = ido % 60;
    var idoString = String(perc).padStart(2, 0) + ":" + String(masodperc).padStart(2, 0);
    document.getElementById('hatralevoido').innerHTML = idoString;
    if(ido > 0)
    {
        if(ido < 60)
        {
            document.getElementById('hatralevoido').style.background = "var(--offline)";
        }
        ido--;
        setTimeout("visszaSzamlalo()", 1000);
    }
    else
    {
        document.getElementById('valaszkuld').disabled = true;
        document.getElementById('valaszkuld').value = 'Válasz beküldése';
        document.getElementById('hatralevoido').style.visibility = 'hidden';
        var x = document.getElementById("lejartido");
        x.className = "show";
        vizsgaVeglegesit();
    }
}

function vizsgaVeglegesit()
{
    console.log("Vizsga véglegesítése");
    var data = new FormData();
    data.append('kitoltesid', '<?=$kitoltesid?>');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "./vizsgazas?action=finalize", true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
        setTimeout("window.location.replace('./vizsgareszletezo/<?=$kitoltesid?>')", 5000);
    }};

    xhr.send(data);
}