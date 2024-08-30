var kattintva = 1;

function templateBeszur(szoveg, id)
{
    let szovegdoboz = document.getElementById('leiras');
    if(szovegdoboz.value == "")
    {
        kattintva = 0;
    }

    if(kattintva > 0)
    {
        szovegdoboz.value = szovegdoboz.value + ", " + szoveg;
    }
    else
    {
        let elsobetu = szoveg.charAt(0);
        szovegdoboz.value = elsobetu.toUpperCase() + szoveg.slice(1);
    }
    kattintva++

    $.ajax({
        type: "POST",
        url: RootPath + "/munkatemplatedb?action=hasznalt&tempid=" + id,
    });
}

function templateTorol(szoveg)
{
    let szovegdoboz = document.getElementById('leiras');
    szovegdoboz.value = szovegdoboz.value.replace(', ' + szoveg, '');
    szovegdoboz.value = szovegdoboz.value.replace(szoveg + ', ', '');
    szovegdoboz.value = szovegdoboz.value.replace(szoveg, '');
}