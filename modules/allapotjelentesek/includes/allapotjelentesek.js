function severityFilter() {
    let listaszur = document.getElementById("severityfilter")
    window.location.href = RootPath + "/allapotjelentesek?trapfontossag=" + listaszur.value;
}

function eredmenyekNezet(){
    let nezet = document.getElementById("nezet")
    window.location.href = RootPath + "/allapotjelentesek?nezet=" + nezet.value;
}

function listaSzur(filterid, filterby) {
    let listaelemcollection = document.getElementsByClassName('allapotelem');
    let filter = document.getElementById(filterid).value;
    let listaelemek = Array.from(listaelemcollection);

    listaelemek.forEach(elem => {
        let szoveg = elem.querySelector(`#${filterby}`).textContent;

        // Első ág: megtalált és még nem volt elrejtve
        if(szoveg.toUpperCase().indexOf(filter.toUpperCase()) > -1 && (!elem.getAttribute("data-kiszurt") || elem.getAttribute("data-kiszurt") == filterby))
        {
            elem.style.display = "";
        }

        // Második ág, megtalált, el volt már rejtve a jelen szűrési feltétel által
        else if(szoveg.toUpperCase().indexOf(filter.toUpperCase()) > -1 && elem.getAttribute("data-kiszurt").toUpperCase().indexOf(filterby.toUpperCase()) > -1)
        {
            let filttorles = elem.getAttribute("data-kiszurt");
            let ujfilter = filttorles.replace(filterby, "");
            console.log(ujfilter);
            elem.setAttribute("data-kiszurt", ujfilter);
        }
        else
        {
            let regifilter = elem.getAttribute("data-kiszurt");
            if(regifilter == null)
            {
                elem.setAttribute("data-kiszurt", filterby);
            }
            else if(elem.getAttribute("data-kiszurt") && elem.getAttribute("data-kiszurt").toUpperCase().indexOf(filterby.toUpperCase()) < -1)
            {
                console.log("ujfilter");
                elem.setAttribute("data-kiszurt", regifilter + filterby);
            }
            elem.style.display = "none";
        }
    });
}