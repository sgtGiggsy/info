function updateNotif() {
    $.ajax({
    type: "POST",
    url: RootPath + "/ertesites?action=checkednotif",
});
}

function seenAllNotif() {
$.ajax({
    type: "POST",
    url: RootPath + "/ertesites?action=seenallnotif",
});

document.getElementById("notifcount").style.display = "none"
}

function seenNotif(notifid) {
    $.ajax({
        type: "POST",
        url: RootPath + "/ertesites?action=seennotif&notifid=" + notifid,	
    });
}

window.addEventListener("load", (event) => {
    seenNotif(urlParams.get('ertesites'));
    var notifcount = document.getElementById("notifcount").textContent;
    if(urlParams.get('ertesites'))
    {
        notifcount = notifcount - 1;
    }
    document.getElementById("notifcount").textContent = notifcount;
    if(notifcount == 0)
    {
        document.getElementById("notifcount").style.display = "none";
    }
    if(document.getElementById("notif-" + urlParams.get('ertesites')))
    {
        document.getElementById("notif-" + urlParams.get('ertesites')).className.replace("notifitem", "notifitem-latta");
    }
});