$(document).ready(function () {
    $('#sub_discipline').css('display', 'none');
    $('#sub_date').css('display', 'none');
    $('#sub_statement').css('display', 'none');

    $("#get_group").change(function () {
        clearlist();
        $('#sub_date').css('display', 'none');
        $('#sub_statement').css('display', 'none');
        var groupValue = $("#get_group option:selected").val();
        if (groupValue === '') {
            clearlist();
            $('#sub_discipline').css('display', 'none');
        }
        getdiscipline();
    })

    function getdiscipline() {
        var group_value = $("#get_group option:selected").val();
        var p_id = $("#page_id").val();
        var area = $("#get_discipline");
        var getdiscipline_value = area.val();
        if (group_value === "") {
            area.attr("disabled", true);
        } else {
            area.attr("disabled", false);
            area.load('get_discipline.php', {group: group_value, page_id: p_id});
            $('#sub_discipline').css('display', 'block');
        }

        $("#get_discipline").change(function () {
            $('#sub_date').css('display', 'none');
            $('#sub_statement').css('display', 'none');
            getdate();
        })
    }

    function getdate() {
        // var discipline_value = $("#discipline_id").val();
        var group_value = $("#get_group option:selected").val();
        var discipline_value = $("#get_discipline option:selected").val();
        var p_id = $("#page_id").val();
        var area = $("#get_date");
        var date_value = area.val();
        if (discipline_value === "") {
            area.attr("disabled", true);
        } else {
            area.attr("disabled", false);
            area.load('get_date.php', {discipline: discipline_value, page_id: p_id, group: group_value});
            $('#sub_date').css('display', 'block');
        }

        $("#get_date").change(function () {
            $('#sub_statement').css('display', 'none');
            getStatement();
        })
    }

    function getStatement() {
        var group_value = $("#get_group option:selected").val();
        var discipline_value = $("#get_discipline option:selected").val();
        var date_value = $("#get_date option:selected").val();
        var p_id = $("#page_id").val();
        var area = $("#get_statement");
        if (date_value === "") {
            area.attr("disabled", true);
            $('#sub_statement').css('display', 'none');
            $("#get_statement").empty();
        } else {
            area.attr("disabled", false);
            area.load('get_statement.php', {
                page_id: p_id,
                statement: date_value,
                group: group_value,
                discipline: discipline_value
            });
            $('#sub_statement').css('display', 'block');
        }
    }

    function clearlist() {
        $("#get_discipline").empty();
        $("#get_date").empty();
        $("#get_statement").empty();
    }
});