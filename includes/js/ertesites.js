$(document).ready(function($) {
    seenNotif(urlParams.get('ertesites'));
    var notifcount = document.getElementById("notifcount").textContent;
    notifcount = notifcount - 1;
    document.getElementById("notifcount").textContent = notifcount;
    document.getElementById("notif-" + urlParams.get('ertesites')).className.replace("notifitem", "notifitem-latta");
});