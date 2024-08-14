function getSwitchState() {
    let xhttp = new XMLHttpRequest();
    let response, count;
    let newsflashtext = "";

    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            response = JSON.parse(this.responseText);
            count = response.length;
            //console.log(jsonObj[0].sorrend);
            if(count == 0) {
                hideNewsflash();
            }
            else {
                document.getElementById("newsflashdesc").innerHTML = "Offline aktív eszközök:";
                let textarea = document.getElementById("newsflashtext");
                let duration = 0;

                if(count > 3) {
                    duration = count * 5;
                }

                textarea.style.animationDuration = duration + "s";
                for(let i = 0; i < count; i++) {
                    newsflashtext += response[i].beepitesinev + " (" + response[i].ipcim + ")";
                    if(i != count - 1) {
                        newsflashtext += "&nbsp&nbsp|&nbsp&nbsp";
                    }
                }

                showNewsflash();
                textarea.innerHTML = newsflashtext;
            }
        }
    };
    xhttp.open("GET", RootPath + "/modules/eszkozok/includes/eszkozonlinecheck.php", true);
    xhttp.send();
}

window.addEventListener("load", (event) => {
    getSwitchState();
    setInterval(getSwitchState, 3000);
});