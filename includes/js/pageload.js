window.addEventListener('load', () => {
    const status = navigator.onLine;
    //console.log(status);
})

window.addEventListener('offline', (e) => {
    console.log('offline');
});

window.addEventListener('online', (e) => {
    console.log('online');
});

window.onload = function()
{
    if(typeof menunyit !== 'undefined' && document.getElementById(menunyit) != null)
        document.getElementById(menunyit).style.display = "";
}