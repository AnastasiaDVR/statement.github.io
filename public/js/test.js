function filter_input(e, regexp) {
    e = e || window.event;
    var target = e.target || e.srcElement;
    var isIE = document.all;

    if (target.tagName.toUpperCase() == 'INPUT') {
        var code = isIE ? e.keyCode : e.which;
        if (code < 32 || e.ctrlKey || e.altKey) return true;

        var char = String.fromCharCode(code);
        if (!regexp.test(char)) return false;
    }
    return true;
}

function filter_td(e, regexp) {
    e = e || window.event;
    var target = e.target || e.srcElement;
    var isIE = document.all;

    if (target.tagName.toUpperCase() == 'TD') {
        var code = isIE ? e.keyCode : e.which;
        if (code < 32 || e.ctrlKey || e.altKey) return true;

        var char = String.fromCharCode(code);
        if (!regexp.test(char)) return false;
    }
    return true;
}