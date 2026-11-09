var utrendez = 99999;
const mobilmenu = document.getElementById("menunyitzar");
const overlay = document.getElementById("overlay");

mobilmenu.addEventListener("click", () => showMenu());
overlay.addEventListener("click", () => showMenu(false));

var toaster = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
})


function goBack() {
    window.history.back();
};

function rejtMutat(id) {
    if(document.getElementById(id).style.display == "grid")
    {
        document.getElementById(id).style.display = "none"
    }
    else
    {
        document.getElementById(id).style.display = "grid";
    }
}

function resetSelect() {
    const selects = document.querySelectorAll('select');
    selects.forEach(s => s.selectedIndex = -1);
}

function hideMenu(linkek, gomb, overlay) {
    linkek.style.left = "";
    
    overlay.style.display = "none";
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.left = '';
    document.body.style.right = '';
}

function showMenu(show = true) {
    let linkek = document.getElementById("linkek");
    let gomb = document.getElementById("menunyitzar");
    let btn = document.getElementById('menunyitzar');
    btn.classList.toggle('active');
    linkek.classList.toggle('open');
    if(show)
        if(linkek.style.left == "0px") {
            hideMenu(linkek, gomb, overlay);
        }
        else {
            linkek.style.left = 0;
            
            overlay.style.display = "block";
            document.body.style.position = 'fixed';
            document.body.style.top = `-${scrollY}px`;
            document.body.style.left = '0';
            document.body.style.right = '0';
        }
    else {
        hideMenu(linkek, gomb, overlay);
    }
}

function tableQuickSort(colIndex, colType, tableId, nulllast = true) {
    const table = document.getElementById(tableId);
    const tbody = table.tBodies[0] || table;
    const rows = Array.from(tbody.rows).filter(row => row.cells.length);
    const nullval = nulllast ? "zzzzzzz" : ""; // üres értékek kezelése

    direction = (utrendez === colIndex && direction === "asc") ? "desc" : "asc";
    utrendez = colIndex;

    // Előkészítés rendezéshez
    const sortable = rows.map(row => {
        const text = row.cells[colIndex]?.textContent.trim() || nullval;
        let value;
        if (colType === "ip") value = ipToNumber(text);
        else if (colType === "i") value = parseFloat(text) || 0;
        else value = text.toLowerCase();
        return { row, value };
    });

    // Stabil rendezés
    sortable.sort((a, b) => {
        if (a.value < b.value) return direction === "asc" ? -1 : 1;
        if (a.value > b.value) return direction === "asc" ? 1 : -1;
        return 0;
    });

    // DOM frissítése
    const frag = document.createDocumentFragment();
    sortable.forEach(({ row }) => frag.appendChild(row));
    tbody.appendChild(frag);
}

function copyToClipboard(text) {
    try {
        navigator.clipboard.writeText(text);
        toaster.fire({
			icon: "success",
			title: "Link a vágólapra másolva!"
		});
    } catch (error) {
        toaster.fire({
            icon: "error",
            title: "Linket nem sikerült a vágólapra másolni!"
        })
    }
}

