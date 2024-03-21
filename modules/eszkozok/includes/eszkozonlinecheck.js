function getSwitchState() {
    let xhttp = new XMLHttpRequest();
    let response, count;
    let newsflashtext = "<strong>Offline aktív eszközök: </strong>";

    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            response = JSON.parse(this.responseText);
            count = response.length;
            //console.log(jsonObj[0].sorrend);
            if(count == 0) {
                hideNewsflash();
            }
            else {
                for(let i = 0; i < count; i++) {
                    newsflashtext += response[i].beepitesinev + " (" + response[i].ipcim + ")";
                    if(i != count) {
                        newsflashtext += "\t";
                    }
                }

                showNewsflash();
                document.getElementById("newsflash").innerHTML = newsflashtext;
            }
        }
    };
    xhttp.open("GET", RootPath + "/modules/eszkozok/includes/eszkozonlinecheck.php", true);
    xhttp.send();
}

window.addEventListener("load", (event) => {
    setInterval(getSwitchState, 3000);
});


