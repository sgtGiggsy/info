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

        // Első ág: megtalált
        if(szoveg.toUpperCase().includes(filter.toUpperCase()))
        {
            // Ha már el volt rejtve, de nem (csak) a jelen filter miatt, töröljük a jelen filtert a filterek listájáról
            if(elem.getAttribute("data-kiszurt") && elem.getAttribute("data-kiszurt") != filterby)
            {
                let filttorles = elem.getAttribute("data-kiszurt");
                let ujfilter = filttorles.replace(filterby, "");
                elem.setAttribute("data-kiszurt", ujfilter);
            }
            // Ha nem volt elrejtve, vagy el volt, de a jelen filter által, akkor az elemet megjelenítjük, és a filterbejegyzést töröljük róla
            else
            {
                elem.style.display = "";
                elem.setAttribute("data-kiszurt", "");
            }
        }

        // Második ág, nem megtalált, most kerül rejtésre
        else
        {
            let regifilter = elem.getAttribute("data-kiszurt");
            if(regifilter == null)
            {
                elem.setAttribute("data-kiszurt", filterby);
            }
            else if(!elem.getAttribute("data-kiszurt").includes(filterby))
            {
                console.log("régifilter")
                elem.setAttribute("data-kiszurt", regifilter + filterby);
            }
            elem.style.display = "none";
        }
    });
}