function generatePassword(hossz = null)
{
    let jelszohossz;
    let pass = "";
    let used = [];

    if(hossz === null)
        jelszohossz = window.prompt("Hány karaketeres jelszót szeretnél generálni?", 12);
    else
        jelszohossz = hossz;

    const charlist = [
        {
            chars   : "qwertzuipasdfghjklyxcvbnm",
            length  : 0
        },
        {
            chars   : "QWERTZUIPASDFGHJKLYXCVBNM",
            length  : 0
        },
        {
            chars   : "0123456789",
            length  : 0
        },
        {
            chars   : "?!@&%+#",
            length  : 0
        }
    ];
    charlist[0].length = charlist[0].chars.length;
    charlist[1].length = charlist[1].chars.length;
    charlist[2].length = charlist[2].chars.length;
    charlist[3].length = charlist[3].chars.length;

    do
    {
        pass = "";
        used = [0, 1, 2, 3];
        for(let i = 0; i < jelszohossz; i++)
        {
            let arrnum = Math.floor(Math.random() * 4)
            used.splice(used.indexOf(arrnum), 1);
            
            let index = Math.floor(Math.random() * charlist[arrnum].length);
            pass += charlist[arrnum].chars.charAt(index);
        }
        
    } while (used.length > 0);

    if(!window.isSecureContext)
    {
        window.alert("A generált jelszó a következő:\n" + pass);
    }
    else
    {
        window.alert("A generált jelszó a következő:\n" + pass + "\nA jelszó automatikusan a vágólapra lett másolva.");
        navigator.clipboard.writeText(pass);
    }
}