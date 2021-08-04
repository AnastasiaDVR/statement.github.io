$(document).ready(function () {
    $("#FormSubmit").click(function (e) {

        e.preventDefault();

        if ($("#contentText").val() === "") {
            alert("Введите текст!");
            return false;
        }

        var myData = "content_txt=" + $("#contentText").val();

        jQuery.ajax({
            type: "POST",
            url: "response.php",
            dataType: "text",
            data: myData,
            success: function (response) {
                $("#responds").append(response);
                $("#contentText").val('');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError);
            }
        });
    });

    $("body").on("click", "#responds .del_button", function (e) {
        e.preventDefault();
        var clickedID = this.id.split("-");
        var DbNumberID = clickedID[1];
        var myData = 'recordToDelete=' + DbNumberID;

        jQuery.ajax({
            type: "POST",
            url: "response.php",
            dataType: "text",
            data: myData,
            success: function (response) {
                $('#item_' + DbNumberID).fadeOut("slow");
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError);
            }
        });
    });
});