/*
*       Online állapot ellenőrző
*       Node.js script a meglévő net-ping csomag felhasználásával az aktív eszközök online állapotának ellenőrzésére
*
#       
#
*/


var ping = require ("net-ping");
var session = ping.createSession ();
var settings = require('./onlinestate_check.json');
process.env.NODE_TLS_REJECT_UNAUTHORIZED = 0;
let most = new Date();
var timestamp = most.getFullYear() + "-" + String(most.getMonth()).padStart(2, 0) + "-" + String(most.getDate()).padStart(2, 0) + " " + String(most.getHours()).padStart(2, 0) + ":" + String(most.getMinutes()).padStart(2, 0) + ":" + String(most.getSeconds()).padStart(2, 0);

fetch(settings.api.url, {
    method: "GET",
    headers: {
        "Content-type": "application/json; charset=UTF-8",
        "Accept": "application/json; charset=UTF-8",
        "Authorization": settings.api.key
    }
})
.then((response) => {eredmeny = response.status
    if(eredmeny != 200)  {
        console.log(eredmeny);
        return false;
    }
    else
    {
        return response;
    }
})
.then((response) => response.json())
.then(data => {
    let messagebody = [];
    let db = data.length;
    for(let i = 0; i < db; i++)
    {
        session.pingHost (data[i].ipcim, function (error, target) {
            let messageitem = {
                deviceip:   data[i].ipcim,
                eszkid:     data[i].id,
                online:     data[i].online,
                cim:        null,
                szoveg:     null
            }
            if((error && !data[i].online) || (!error && data[i].online)) // Ha az eszköz offline, és eddig online volt, vagy offline, és eddig online volt, értesítés
            {
                messageitem.online = error ? 0 : 1;
                messageitem.cim = "A(z) " + data[i].nev + (error ? "elérhetetlen" : "elérhető");
                messageitem.szoveg = "A(z) " + data[i].nev + " (" + data[i].ipcim + ") eszköz " + (error ? "elérhetetlenné" : "elérhetővé") + " vált " + timestamp + "-kor.";
            }
            messagebody.push(messageitem);
        });
    }
    setTimeout(() => {console.log("messagebody: " + JSON.stringify(messagebody))}, 5000);
    
})
.then(data => console.log("data: " + JSON.stringify(data)));
//console.log(settings);

//.then(response => console.log(JSON.stringify(response)))