// Globális változók
var direction = "asc";
var utrendez = 99999;
var coltype = "i"; // s = string, i = int
var colnum = 0; // oszlop szám

// Partícionálsi függvény
// A partícionálás során a tömböt két részre osztjuk, az egyik részben a pivotnál kisebb, a másikban nagyobb elemeket tárolunk
function partitionArray(arr, low, high)
{
    // Pivot elem kiválasztása
    let pivot = arr[high];
    let pivotcol = pivot.getElementsByTagName("TD")[colnum];
    let i = low - 1;

    // A tömb bejárása a legkisebbtől a legnagyobb elemig, és az összes kisebb elem áthelyezése a bal oldalra
    // Minden iterációban a low-tól i-ig terjedő elemek kisebbek lesznek
    for (let j = low; j <= high - 1; j++) {
        let csere = false;
        let coltocheck = arr[j].getElementsByTagName("TD")[colnum];

        // Megvizsgáljuk, hogy van-e szükség cserére.
        if(coltype == "s")
        {
            if (direction == "desc" && (coltocheck.textContent.toLowerCase().localeCompare(pivotcol.textContent.toLowerCase(), navigator.languages[0] || navigator.language, {numeric: true, ignorePunctuation: true})) > 0)
                csere = true;
            else if (direction == "asc" && (coltocheck.textContent.toLowerCase().localeCompare(pivotcol.textContent.toLowerCase(), navigator.languages[0] || navigator.language, {numeric: true, ignorePunctuation: true})) < 0)
                csere = true;
        }
        else if(coltype == "i")
        {
            if (direction == "asc" && Number(coltocheck.textContent) < Number(pivotcol.textContent))
                csere = true;
            else if (direction == "desc" && Number(coltocheck.textContent) > Number(pivotcol.textContent))
                csere = true;
        }
        
        // Ha a csere szükséges, akkor növeljük az i-t és cseréljük meg az elemeket
        if (csere) {
            i++;
            arr[i].parentNode.insertBefore(arr[j], arr[i]);
        }
    }

    let pivind = i + 1;

    // Biztosítjuk, hogy a pivot az utolsó elem legyen, és visszaadjuk az értékét
    arr[pivind].parentNode.insertBefore(arr[high], arr[pivind]);

    return pivind;
}

// A rekurzív függvény, amely a quicksort algoritmust valósítja meg
function quickSort(arr, low, high)
{
    if (low < high) {
        let pivind = partitionArray(arr, low, high, colnum, coltype);

        // A függvény rekurzív része, ami meghívja magát a bal és jobb részre
        quickSort(arr, low, pivind - 1, colnum, coltype);
        quickSort(arr, pivind + 1, high, colnum, coltype);
    }
}

function sortTable(n, t, tname)
{
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById(tname);
    switching = true;
    if(t == "s") { dir = "asc"; } else { dir = "desc"; }
    while (switching) {
        switching = false;
        rows = table.rows;
        rowcount = rows.length - 1;
        if(rows[rowcount].getElementsByTagName("TH")[1])
        {
            rowcount = rows.length - 2;
        }
        for (i = 1; i < rowcount; i++) {
            shouldSwitch = false;
            if(!rows[i].getElementsByTagName("TD")[n])
            {
                i++;
            }
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            if(t == "s")
            {
                if (dir == "asc") {
                    if((x.textContent.toLowerCase().localeCompare(y.textContent.toLowerCase(), navigator.languages[0] || navigator.language, {numeric: true, ignorePunctuation: true})) > 0)
                    {
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir == "desc") {
                    if((x.textContent.toLowerCase().localeCompare(y.textContent.toLowerCase(), navigator.languages[0] || navigator.language, {numeric: true, ignorePunctuation: true})) < 0)
                    {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
            else if(t == "i")
            {
                if (dir == "asc") {
                    if (Number(x.textContent) < Number(y.innerHTML)) {
                    shouldSwitch = true;
                    break;
                    }
                } else if (dir == "desc") {
                    if (Number(x.textContent) > Number(y.innerHTML)) {
                    shouldSwitch = true;
                    break;
                    }
                }
            }
        }
        if (shouldSwitch) {
        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
        switching = true;
        switchcount ++;
        } else {
        if(t == "s") {
                if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
                }
        }
        else
            {
                if (switchcount == 0 && dir == "desc") {
                dir = "asc";
                switching = true;
                }
            }  
        }
    }
}

function tableQuickSort(coln, colt, tablename)
{
    let table, rows;
    colnum = coln;
    coltype = colt;
    table = document.getElementById(tablename);

    if(direction == "asc" && colnum == utrendez)
        direction = "desc"
    else
        direction = "asc";

    utrendez = colnum;
    rows = table.rows;
    rowcount = rows.length - 1;
    if(rows[rowcount].getElementsByTagName("TH")[1])
    {
        rowcount = rows.length - 2;
    }

    quickSort(rows, 1, rowcount, colnum, coltype);
}

function filterTable(szures, tablazat, oszlop, szuloszur = false)
{
    var input, filter, table, tr, td, i, txtValue, szuloelem;
    input = document.getElementById(szures);
    filter = input.value.toUpperCase();
    table = document.getElementById(tablazat);
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++)
    {
        td = tr[i].getElementsByTagName("td")[oszlop];
        tdfirst = tr[i].getElementsByTagName("td")[0];
        if(tdfirst && tdfirst.innerHTML && szuloszur)
        {
            szuloelem = tr[i];
            let szuroazon = szuloelem.getAttribute("szurazon");
            if (szuroazon == oszlop || !szuroazon)
            {
                szuloelem.style.display = "none";
                szuloelem.setAttribute("szurazon", oszlop);
            }
        }
        if(td)
        {
            txtValue = td.textContent;
            if (txtValue.toUpperCase().indexOf(filter) > -1)
            {
                let childazon = tr[i].getAttribute("szurazon");
                if (childazon == oszlop || !childazon)
                {
                    if(szuloszur && szuloelem.getAttribute("szurazon") == oszlop)
                    {
                        szuloelem.style.display = "";
                    }
                    tr[i].style.display = "";
                }
            }
            else
            {
                tr[i].style.display = "none";
                tr[i].setAttribute("szurazon", oszlop);
            }
        }

        //console.log(tdfirst.colSpan);
        
    }
}

function mutatOszlop(osztalynev) {
    let elemek = document.getElementsByClassName("hiddencol-" + osztalynev);
    let elemdb = elemek.length;
    let cursor = document.getElementById(osztalynev + "-cursor");
    if(cursor.textContent == ">")
    {
        cursor.textContent = "<";
    }
    else
    {
        cursor.textContent = ">";
    }

    for(let i = 0; i < elemdb; i++)
    {
        if(elemek[i].style.display == "none")
        {
            elemek[i].style.display = "";
        }
        else
        {
            elemek[i].style.display = "none";
        }
    }
}