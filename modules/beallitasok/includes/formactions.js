function fieldEnable(index)
{
    //document.getElementById(id).disabled = false;
    let elementsarr = document.querySelectorAll('[id$="-' + index + '"]');
    let arrayszam = elementsarr.length;
    for(let i = 0; i < arrayszam; i++)
    {
        let menupont = elementsarr[i].id.split('-')[0];
        let name = "menupont" + '[' + index + '][' + menupont + ']';
        elementsarr[i].name = name;
    }
}