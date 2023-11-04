$("form").on("submit", function (e) {
    var dataString = $(this).serialize();

    $.ajax({
    type: "POST",
    data: dataString,
    url: RootPath + "/portdb?action=update&tipus=mediakonverter",
    success: function () {
        showToaster("Port szerkesztése sikeres...");
    }
});
e.preventDefault();
});