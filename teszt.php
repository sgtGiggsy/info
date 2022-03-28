<?php

?>
<div id='message'></div>
<div id="tesztform">
    <form name="formtotest" action=""> <!-- onsubmit="submitForm('teszt.php?action=update&tipus=switch'); return false;"> -->
        <select name="allapot">
            <option value="0">Letiltva</option>
            <option value="1">Engedélyezve</option>
        </select>

        <select name="mode">
            <option value="1">Trunk</option>
            <option value="2">Access</option>
        </select>
        <input type="submit" value="Módosítás">
    </form>
</div>

<div id="snackbar">Some text some message..</div>

<script>
    $("form").on("submit", function (e) {
    var dataString = $(this).serialize();
     
    $.ajax({
      type: "POST",
      data: dataString,
      url: "./tesztdb",
      success: function () {
        showToaster();
      }
    });
 
    e.preventDefault();
});

function showToaster() {
  // Get the snackbar DIV
  var x = document.getElementById("snackbar");

  // Add the "show" class to DIV
  x.className = "show";

  // After 3 seconds, remove the show class from DIV
  setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
}
</script>