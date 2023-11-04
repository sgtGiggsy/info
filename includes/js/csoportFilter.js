function filterCsoport(szures, tablazat)
{
    var input, filter, table, tr, i, txtValue;
    input = document.getElementById(szures);
    filter = input.value.toUpperCase();
    table = document.getElementById(tablazat);
    tr = table.getElementsByTagName("tr");

    //console.log(tr.length);
    for (i = 1; i < tr.length; i++)
    {
        txtValue = table.rows[i].className;
        if (txtValue.toUpperCase().indexOf(filter) > -1)
        {
            if(tr[i].style.color == filter)
            {
                tr[i].style.display = "";
                tr[i].style.color = "";
            }
        }
        else
        {
            tr[i].style.display = "none";
            tr[i].style.color = filter;
        }
    }
}

function showHideCsoport(csoport, tablazat)
{
    var filter, table, tr, td, i, txtValue;
    var voltmar = false;
    filter = csoport.toUpperCase();
    table = document.getElementById(tablazat);
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++)
    {
        txtValue = table.rows[i].id;
        csoportcim = csoport+"0";
        if (txtValue.toUpperCase().indexOf(filter) > -1 && txtValue != csoportcim)
        {
            voltmar = true;
            if(tr[i].style.display == "none")
            {
                tr[i].style.display = "";
            }
            else
            {
                tr[i].style.display = "none";
            }
        }
        else if(voltmar)
        {
            break;
        }
    }
}