var contentold = {};

function savedata(elementidsave, contentsave) {
    $.ajax({
        url: 'save.php',
        type: 'POST',
        data: {
            content: contentsave,
            id: elementidsave
        },
        // success:function (data) {
        // if (data == contentsave)
        // {
        //     $('#'+elementidsave).html(data);
        //     $('<div id="status">Данные успешно сохранены:'+data+'</div>')
        //         .insertAfter('#'+elementidsave)
        //         .addClass("success")
        //         .fadeIn('fast')
        //         .delay(1000)
        //         .fadeOut('slow', function() {this.remove();});
        //     }
        //     else
        //     {
        //         $('<div id="status">Запрос завершился ошибкой:'+data+'</div>')
        //             .insertAfter('#'+elementidsave)
        //             .addClass("error")
        //             .fadeIn('fast')
        //             .delay(3000)
        //             .fadeOut('slow', function() {this.remove();});
        //     }
        // }
    });
}

$(document).ready(function () {
    $('[contenteditable="true"]')
        .mousedown(function (e) {
            e.stopPropagation();
            elementid = this.id;
            contentold[elementid] = $(this).html();
            $(this).bind('keydown', function (e) {
                if (e.keyCode == 27) {
                    e.preventDefault();
                    $(this).html(contentold[elementid]);
                }
            });
            $("#save").show();
        })
        .blur(function (event) {
            var elementidsave = this.id;
            var cont = $(this).val();
            if (cont != "") {
                var contentsave = $(this).val();
            } else {
                var contentsave = $(this).html();
            }
            event.stopImmediatePropagation();
            // if (elementid===elementidsave)
            // {$("#save").hide(); }
            if (contentsave != contentold[elementidsave]) {
                savedata(elementidsave, contentsave);
            }
        });
});