$("form").on("submit", function (e) {
    var dataString = $(this).serialize();

    $.ajax({
        type: "POST",
        data: dataString,
        url: RootPath + "/portdb?action=update&tipus=switch",
        success: function () {
            showToaster("Port szerkesztése sikeres...");
        }
    });
    e.preventDefault();
});

function sendAllForms()
{
    var forms = document.getElementsByTagName("FORM");
    var elemszam = forms.length;
    showProgressOverlay();
    for (var i = 0; i < elemszam; i++) {
        var dataString = $(forms[i]).serialize();
        $.ajax({
            type: "POST",
            data: dataString,
            url: RootPath + "/portdb?action=update&tipus=switch",
            success: function () {
                if(i == elemszam)
                {
                    hideProgressOverlay();
                }
            }
        });
    }
}

function setVlan(id) {
    for(var i = 1; i < 1000; i++) {
        if(document.getElementById("vlan-" + i)) {
            select = document.getElementById("vlan-" + i);
            select.value = id;
        }
        else {
            break;
        }
    }
    showPopup('largebutton-popup');
}

if(typeof webeszkoz !== 'undefined')
{
    var atfedes = document.getElementById("atfedes");
    var btn = document.getElementById("manage");
    var span = document.getElementsByClassName("close")[0];

    btn.onclick = function() {
        atfedes.style.display = "block";
    }

    span.onclick = function() {
        atfedes.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == atfedes) {
            atfedes.style.display = "none";
        }
    }
}

if(typeof snmp !== 'undefined')
{
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("snmpdata").innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", RootPath + "/modules/eszkozok/includes/snmp_portdata.php?ip=" + deviceip + "&community=" + snmpcommunity, true);
    //xhttp.setRequestHeader(header, value);
    xhttp.send();

    let trapdata = new XMLHttpRequest();
    
    trapdata.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("traplist").innerHTML = this.responseText;
        }
    };
    trapdata.open("GET", RootPath + "/modules/eszkozok/includes/snmp_traps.php?devid=" + devid, true);
    trapdata.send();

}