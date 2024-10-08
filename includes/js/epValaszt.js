function epValaszt()
{
    document.getElementById(helyisegselect).value = "";
    let epulet = document.getElementById('epulet').value;

    let helyisegek = document.getElementsByClassName('optgrp');
    let helyisegdb = helyisegek.length;

    for(let i = 0; i < helyisegdb; i++)
    {
        helyisegek[i].style.display = "none";
        if(helyisegek[i].id == epulet + "-epulet")
            helyisegek[i].style.display = "";
    }
}