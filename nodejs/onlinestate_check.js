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
process.env.NODE_TLS_REJECT_UNAUTHORIZED = 0; // Erre azért van szükség, mert máskülönben csak érvényes CERT-eket fogad el HTTPS-es API-knál
var timestamp = createTimestamp();
var interval = settings.interval * 1000 * 60;

var getheader = {
    "Content-type": "application/json; charset=UTF-8",
    "Accept": "application/json; charset=UTF-8",
    "Authorization": settings.getapi.key
}

var postheader = {
    "Content-type": "application/json; charset=UTF-8",
    "Authorization": settings.postapi.key
}

async function pingHost(dev)
{
    return new Promise((resolve, reject) => {    
        session.pingHost (dev.ipcim, function (error, target) {
            let messageitem = {
                deviceip:   dev.ipcim,
                eszkid:     dev.id,
                online:     dev.online,
                cim:        null,
                szoveg:     null
            }
            if((error && (dev.online == 1) || dev.online === null) || (!error && dev.online == 0)) // Ha az eszköz offline, és eddig online volt, vagy offline, és eddig online volt, értesítés
            {
                messageitem.online = error ? 0 : 1;
                messageitem.cim = "A(z) " + dev.nev + (error ? " elérhetetlen" : " elérhető");
                messageitem.szoveg = "A(z) " + dev.nev + " (" + dev.ipcim + ") eszköz " + (error ? "elérhetetlenné" : "elérhetővé") + " vált " + timestamp + "-kor.";
            }
            console.log("A(z) " + dev.nev + (error ? " elérhetetlen" : " elérhető"));
            resolve(messageitem);
        });
    });
}

async function processItems(devarray)
{
    const promiseArray = devarray.map((dev) => {
        return pingHost(dev);
    });
    return await Promise.all(promiseArray);
}

function apiCallException(responsecode)
{
    const err = new Error("API hívás sikertelen!\nHibakód: " + responsecode);
    err.name = "API hiba!";
    err.code = responsecode;
    return err;
}

function createTimestamp()
{
    let most = new Date();
    return most.getFullYear() + "-" + String(most.getMonth()).padStart(2, 0) + "-" + String(most.getDate()).padStart(2, 0) + " " + String(most.getHours()).padStart(2, 0) + ":" + String(most.getMinutes()).padStart(2, 0) + ":" + String(most.getSeconds()).padStart(2, 0);
}

function foFolyamat()
{
    timestamp = createTimestamp();
    console.log("A kiválasztott eszközök állapotának ellenőrzése megkezdődött: " + timestamp + "-kor.");
    fetch(settings.getapi.url, {
        method: "GET",
        headers: getheader
    })
    .then((response) => {
        if(response.status != 200)  {
            throw apiCallException(response.status);
        }
        else
        {        
            return response.json();
        }
    })
    .then(data => {
        processItems(data)
        .then((devdata) => {
            fetch(settings.postapi.url, {
                method: "POST",
                body: JSON.stringify(devdata),
                headers: postheader
            })
            .then((response) => {
                //console.log(response.status)
                console.log("A kiválasztott eszközök ellenőrzése hiba nélkül befejeződött " + createTimestamp() + "-kor. A folyamat újraindul " + settings.interval + " perc múlva.");
            })
            .catch(err => console.error("Adatbázis írási hiba! \n" + err.name + "\n" + err.message));
        });
    })
    .catch(err => console.error(err.name + "\n" + err.message));
}

foFolyamat();

setInterval(() => {
    foFolyamat();
}, interval);