var form = document.getElementById('portresetform');
form.addEventListener('submit', e => {
    var result = confirm('Figyelem!!!\nEzzel a két kiválasztott port között az ÖSSZES port helyiség és rack hozzárendelését törölni fogod. Biztosan ezt szeretnéd tenni?');
    if(result == true) {
        return true;
    }
    else {
        e.preventDefault();
        setTimeout(
            function(){
                hideProgressOverlay();
            }, 100
        );
    }
});