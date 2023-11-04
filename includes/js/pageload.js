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