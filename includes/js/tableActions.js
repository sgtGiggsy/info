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
                    if((x.innerHTML.toLowerCase().localeCompare(y.innerHTML.toLowerCase(), navigator.languages[0] || navigator.language, {numeric: true, ignorePunctuation: true})) > 0)
                    {
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir == "desc") {
                    if((x.innerHTML.toLowerCase().localeCompare(y.innerHTML.toLowerCase(), navigator.languages[0] || navigator.language, {numeric: true, ignorePunctuation: true})) < 0)
                    {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
            else if(t == "i")
            {
                if (dir == "asc") {
                    if (Number(x.innerHTML) < Number(y.innerHTML)) {
                    shouldSwitch = true;
                    break;
                    }
                } else if (dir == "desc") {
                    if (Number(x.innerHTML) > Number(y.innerHTML)) {
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
            if (szuloelem.title == oszlop || !szuloelem.title)
            {
                szuloelem.style.display = "none";
                szuloelem.title = oszlop;
            }
        }
        if(td)
        {
            txtValue = td.textContent;
            if (txtValue.toUpperCase().indexOf(filter) > -1)
            {
                if(tr[i].title == oszlop || !tr[i].title)
                {
                    if(szuloszur && szuloelem.title == oszlop)
                    {
                        szuloelem.style.display = "";
                    }
                    tr[i].style.display = "";
                }
            }
            else
            {
                tr[i].style.display = "none";
                tr[i].title = oszlop;
            }
        }

        //console.log(tdfirst.colSpan);
        
    }
}