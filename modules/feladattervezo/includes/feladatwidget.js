function getFeladatlist() {
    let xhttp = new XMLHttpRequest();
    let terulet = document.getElementById("leftmenudata");
    terulet.style.display = "";
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            terulet.innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", RootPath + "/api/unsafe?modulnev=feladattervezo", true);
    xhttp.send();
}

window.addEventListener("load", (event) => {
    getFeladatlist();
    setInterval(getFeladatlist, 30000);
});
