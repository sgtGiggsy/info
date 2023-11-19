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
TODO    API elkészítése
TODO    A Node.js modul elkészítése
TODO    Az oldalon megjelenítés megvalósítása (közös felületen az összes trap, illetve az eszközök egyéni oldalán a rájuk vonatkozó)
#
?       A jelenleg ismert trap oid-k:
?       1.3.6.1.6.3.1.1.5.1 - coldStart
?       1.3.6.1.6.3.1.1.5.2 - warmStart
?       1.3.6.1.6.3.1.1.5.3 - linkDown
?       1.3.6.1.6.3.1.1.5.4 - linkUp
?       1.3.6.1.6.3.1.1.5.5 - authenticationFailure
*/

var oid = {
    "1.3.6.1.2.1.1.3.0": "sysuptime",
    "1.3.6.1.6.3.1.1.4.1": "trap"
}

var snmp = require ("net-snmp");

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
    if(error) {
        //TODO Az értesítés API meghívása, és értesítés adása a hiba adataival
        console.error (error);
    } else {
        let messagebody = data.pdu.varbinds;
        let messagecount = messagebody.length;
        let trapbody = {
            deviceip:   data.rinfo.address,
            sysuptime:  messagebody[0].value / 100,
            event:      messagebody[1].value,
            misc:       "" // Szebb lenne tömbként, de ez csak ideiglenes funkció, egy az egyben megy az adatbázisba stringként, így fölösleges
        }

        //! Ez csak addig lesz használatban, amíg az összes lehető trap beazonosításra kerül, utána mindenképp törölni!
        for(let i = 2; i < messagecount; i++)
        {
            trapbody.misc += "OID: " + messagebody[i] + " Value: " + messagebody[i].value + "\n";
        }
        console.log(JSON.stringify(trapbody, null, 2));
        //console.log (JSON.stringify(data, null, 2));

        //TODO Itt következik az oldal SNMP API-jának meghívása
    }
};

receiver = snmp.createReceiver(options, callback);
receiver.authorizer.addCommunity("public");
//console.log(receiver.authorizer.communities);