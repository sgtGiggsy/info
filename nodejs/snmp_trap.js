/*
*       SNMP Trap Fogadó
*       Node.js script a meglévő net-snmp csomag felhasználásával az SNMP trap üzenetek elkapására
*
#       A koncepció, hogy a Node.js-es modul fogadja a trap-eket, majd a megfelelő API-n keresztül továbbítja az adatbázisba.
#       Jelenleg a script ebből annyit valósít meg, hogy elolvassa az érkező trap-et és kiírja konzolra.
#
#       A hosszútávú terv NEM egy teljes felügyeleti rendszer elkészítése, mert abból rengeteg, széleskörű tudással bíró létezik.
#       Ez a modul csupán a trap-ek fogadására szolgál, hogy alapszíntű felügyeleti képességgel ruházza fel a már meglévő rendszert.
#       A munka oroszlánrészét a net-snmp csomag végzi, az én modulom csak a net-snmptől vett adatot alakítja át az oldal számára.
#
*/

var snmp = require ("net-snmp");
var settings = require('./snmp_trap.json');
console.log(settings);

// Default options
var options = {
    port: 162,
    disableAuthorization: false,
    accessControlModelType: snmp.AccessControlModelType.None,
    engineID: "8000B98380XXXXXXXXXXXXXXXXXXXXXXXX", // where the X's are random hex digits
    address: null,
    transport: "udp4"
};

var callback = function (error, data) {
    let most = new Date();
    let timestamp = most.getFullYear() + "-" + String(most.getMonth()).padStart(2, 0) + "-" + String(most.getDate()).padStart(2, 0) + " " + String(most.getHours()).padStart(2, 0) + ":" + String(most.getMinutes()).padStart(2, 0) + ":" + String(most.getSeconds()).padStart(2, 0);
    console.log(timestamp + " Trap érkezett");
    var eredmeny = 200;
    if(error) {
        //TODO Az értesítés API meghívása, és értesítés adása a hiba adataival
        console.error (error);
        fs.readFileSync(settings.logfile, "utf8");
        fs.appendFile(settings.logfile, "[ISMERETLEN] SNMP trap hiba!" + timestamp + "\n", (err) => {});
    } else {
        let messagebody = data.pdu.varbinds;
        let trapbody;
        let startindex = 2;
        if(data.pdu.type == 167)
        {
            trapbody = {
                deviceip:   data.rinfo.address,
                sysuptime:  messagebody[0].value / 100,
                event:      messagebody[1].value,
                eventlocal: "", // Gyakorlatban küldött snmp_trap-nél megnézni, hogy kinyerhető-e ez az adat
                misc:       []
            }
        }
        else if(data.pdu.type == 164)
        {
            startindex = 0;
            trapbody = {
                deviceip:   data.pdu.agentAddr,
                sysuptime:  data.pdu.upTime / 100,
                event:    "1.3.6.1.6.3.1.1.5",
                eventlocal: "", // Gyakorlatban küldött snmp_trap-nél megnézni, hogy kinyerhető-e ez az adat
                misc:       []
            }
        }
        
        let messagecount = messagebody.length;
        //console.log(data.pdu);

        //! Ez csak addig lesz használatban, amíg az összes lehető trap beazonosításra kerül, utána mindenképp törölni!
        for(let i = startindex; i < messagecount; i++)
        {
                //console.log(messagebody[i]);
                trapbody.misc.push({
                    OID: messagebody[i].oid,
                    TrapVal: "" + messagebody[i].value // Hülyeségnek tűnhet, de enélkül van, hogy bytekódot továbbít az API felé, és az adatbázis írás (ami stringet próbál bevinni) nem sikerül
                })

        }

        fetch(settings.api.url, {
            method: "POST",
            body: JSON.stringify(trapbody),
            headers: {
                "Content-type": "application/json; charset=UTF-8",
                "Authorization": settings.api.key
            }
        })
        .then((response) => eredmeny = response.status);
        if(eredmeny != 200 && eredmeny != 201)  {
            fs.readFileSync(settings.logfile, "utf8");
            fs.appendFile(settings.logfile, `[ADATBÁZIS HIBA] ${timestamp} A(z) ${trapbody.deviceip} IP című eszköz által küldött ${trapbody.event} OID-jű SNMP trap adatbázisba írása sikertelen\n`, (err) => {});
        }
    }
};

let receiver = snmp.createReceiver(options, callback);
receiver.authorizer.addCommunity(settings.community);
//console.log(receiver.authorizer.communities);