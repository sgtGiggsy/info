function firmwareSzur()
{
    let szures = document.getElementById('szures');
    let rejtendo = document.getElementsByClassName('legfrissebb');
    let rejtendodb = rejtendo.length;

    if(szures.value == "legfrissebbrejt")
    {
        for(let i = 0; i < rejtendodb; i++)
        {
            rejtendo[i].style.display = "none";
        }
    }
    else
    {
        for(let i = 0; i < rejtendodb; i++)
        {
            rejtendo[i].style.display = "";
        }
    }
}