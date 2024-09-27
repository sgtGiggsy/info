document.getElementById("tipus").onchange = function() {
    let tipus = document.getElementById("tipus").value;
    tipus = tipusValaszto(tipus);
    setShow(tipus);
}

var header = {
    "Content-type": "application/json; charset=UTF-8",
    "Accept": "application/json; charset=UTF-8"
}

function tipusValaszto(tipus)
{
    let tipusnev;
    if(tipus < 6) {
        tipusnev = "aktiveszkoz";
    }
    else if(tipus < 11) {
        tipusnev = "sohoeszkoz";
    }
    else if(tipus == 11) {
        tipusnev = "szamitogep";
    }
    else if(tipus == 12) {
        tipusnev = "nyomtato";
    }
    else if(tipus < 20) {
        tipusnev = "vegponti";
    }
    else if(tipus < 26) {
        tipusnev = "mediakonverter";
    }
    else if(tipus < 31) {
        tipusnev = "bovitomodul";
    }
    else if(tipus < 40) {
        tipusnev = "szerver";
    }
    else if(tipus == 40) {
        tipusnev = "telefonkozpont";
    }

    return tipusnev;
}

function tipusCompare(tipus, form)
{
    let elemek = form.split(";");

    if(elemek.indexOf(tipus) > -1) {
        return true;
    }
    else {
        return false;
    }
}

async function typesFromDB(tipus)
{
    return fetch(RootPath + "/modules/alap/includes/modelltipus.php?tipus=" + tipus, {
        method: "GET",
        headers: header
    })
    .then((response) => {
        if(response.status == 200)  {     
            return response.json();
        }
    })
}

async function setShow(tipus)
{
    let mezok = document.getElementsByClassName("formmezo");
    let selectlist = document.getElementsByTagName("select");
    let elemszam = mezok.length;
    let selectszam = selectlist.length;

    let formtartalom = await typesFromDB(tipus)

    for(let s = 0; s < selectszam; s++)
    {
        if(formtartalom[selectlist[s].getAttribute("data-selecttype")] && selectlist[s].length == 1)
        {
            let options = formtartalom[selectlist[s].getAttribute("data-selecttype")];
            let optionszam = options.length;

            for(let o = 0; o < optionszam; o++)
            {
                let optionelement = document.createElement("option");
                optionelement.value = options[o].id;
                optionelement.text = options[o].nev;

                selectlist[s].appendChild(optionelement);
            }
        }
    }

    for(let i = 0; i < elemszam; i++)
    {
        if(null !== mezok[i].getAttribute('data-type') && tipusCompare(tipus, mezok[i].getAttribute('data-type')))
        {
            if(mezok[i].getElementsByTagName("select")[0])
                mezok[i].getElementsByTagName("select")[0].disabled = false;
            if(mezok[i].style.display == "none")
            {
                mezok[i].style.display = "";
            }
        }
        else
        {
            if(mezok[i].getElementsByTagName("select")[0])
                mezok[i].getElementsByTagName("select")[0].disabled = true;
            if(mezok[i].style.display == "")
            {
                mezok[i].style.display = "none";
            }
        }
    }
}

setShow(tipus);