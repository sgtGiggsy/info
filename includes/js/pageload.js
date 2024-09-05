window.addEventListener('load', () => {
    const status = navigator.onLine;
    
    if(typeof loginid !== "undefined") {
        console.log(loginid);
        userDeviceParams(loginid);
    }

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