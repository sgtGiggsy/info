function listaSzur()
{
    let input, filter, table, tr, td, i, txtValue, szuloelem;
    input = document.getElementById("listaszur");
    filter = input.value.toUpperCase();
    lista = document.getElementsByClassName("twocolgrid3-1")
    darab = lista.length;

    for (i = 0; i < darab; i++)
    {
        
        if(lista[i].getAttribute("nev").toUpperCase().indexOf(filter) == -1 && lista[i].getAttribute("username").toUpperCase().indexOf(filter) == -1)
        {
            lista[i].style.display = "none";
        }
        else
        {
            lista[i].style.display = "";
        }
    }
}
