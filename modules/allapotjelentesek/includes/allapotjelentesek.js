function severityFilter() {
    let listaszur = document.getElementById("severityfilter")
    window.location.href = RootPath + "/allapotjelentesek?trapfontossag=" + listaszur.value;
}