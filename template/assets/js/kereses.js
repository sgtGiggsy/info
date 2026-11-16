let keresbutton = document.getElementById("keresobutton");
let keresmezo = document.getElementById("kereses");

function keres() {
    const value = keresmezo.value.trim();
    if (!value) return; // üres keresés? meh
    const encoded = encodeURIComponent(value).replace(/%20/g, '+');;
    window.location.href = RootPath + `/` + selectedPage + `/kereses/${encoded}`;
}

keresbutton.addEventListener("click", keres);

keresmezo.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
        keres();
    }
});