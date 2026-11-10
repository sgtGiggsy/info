const jelszohossz = 12;
const jelszo = document.getElementById("jelszo");
const jelszoismetles = document.getElementById("jelszoismetles");
const jelszocsik = document.getElementById("jelszocsik");
const jelszoerotext = document.getElementById("jelszoero");

const rules = {
    length: document.getElementById("rule-length"),
    lower: document.getElementById("rule-lower"),
    upper: document.getElementById("rule-upper"),
    number: document.getElementById("rule-number"),
    match: document.getElementById("rule-match")
};

function setState(elem, valid) {
    elem.classList.remove("valid", "invalid");
    if (valid === true) elem.classList.add("valid");
    if (valid === false) elem.classList.add("invalid");
}

function calcJelszoero(pwd) {
    let score = 0;

    if (pwd.length >= jelszohossz) score++;
    if (/[a-z]/.test(pwd)) score++;
    if (/[A-Z]/.test(pwd)) score++;
    if (/\d/.test(pwd)) score++;
    if (/[^a-zA-Z0-9]/.test(pwd)) score++;

    return score;
}

function updateJelszoBox(score, pwd) {
    const levels = [
        { label: "Nincs jelszó", color: "transparent", width: "0%" },
        { label: "Nagyon gyenge", color: "red", width: "20%" },
        { label: "Gyenge", color: "orange", width: "40%" },
        { label: "Közepes", color: "yellow", width: "60%" },
        { label: "Erős", color: "lightgreen", width: "80%" },
        { label: "Nagyon erős", color: "green", width: "100%" }
    ];

    const level = levels[score];

    jelszocsik.style.width = level.width;
    jelszocsik.style.background = level.color;
    jelszoerotext.textContent = `Jelszó erősség: ${pwd.length ? level.label : "-"}`;
}

function validate() {
    const pwd = jelszo.value;
    const pwd2 = jelszoismetles.value;

    const lengthOk = pwd.length >= jelszohossz;
    const lowerOk = /[a-z]/.test(pwd);
    const upperOk = /[A-Z]/.test(pwd);
    const numberOk = /\d/.test(pwd);
    const matchOk = pwd.length > 0 && pwd === pwd2;
    const score = calcJelszoero(pwd);
    updateJelszoBox(score, pwd);

    setState(rules.length, pwd.length === 0 ? null : lengthOk);
    setState(rules.lower, pwd.length === 0 ? null : lowerOk);
    setState(rules.upper, pwd.length === 0 ? null : upperOk);
    setState(rules.number, pwd.length === 0 ? null : numberOk);
    setState(rules.match, pwd2.length === 0 ? null : matchOk);

    let error = "";
    if (!lengthOk) error = "A jelszó minimális hossza " + jelszohossz + " karakter";
    else if (!lowerOk || !upperOk || !numberOk)
        error = "A jelszónak tartalmaznia kell kisbetűt, nagybetűt és számot is!";

    jelszo.setCustomValidity(error);

    if (!matchOk && pwd2.length > 0) {
        jelszoismetles.setCustomValidity("A beírt jelszavak nem egyeznek!");
    } else {
        jelszoismetles.setCustomValidity("");
    }
}

jelszo.addEventListener("input", validate);
jelszoismetles.addEventListener("input", validate);