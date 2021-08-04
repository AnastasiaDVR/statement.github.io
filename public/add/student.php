<!DOCTYPE html>
<html>
<head>
    <title>Студенты</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="stylish.css">
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script type="text/javascript" src="../js/test.js"></script>
</head>

<body>

<script>
    $(document).ready(function () {
        $("#saveStudents").click(function (e) {
            e.preventDefault();
            if (($("#surnameStudent").val() === "") || ($("#nameStudent").val() === "") || ($("#patronymicStudent").val() === "") || ($("#studentGroup option:selected").val() === "")) {
                alert("Заполните все поля!");
                return false;
            }

            var myData = {
                "surname_student": $("#surnameStudent").val(),
                "name_student": $("#nameStudent").val(),
                "patronymic_student": $("#patronymicStudent").val(),
                "studentGroupList": $("#studentGroup").val()
            };

            jQuery.ajax({
                type: "POST",
                url: "../response.php",
                dataType: "text",
                data: myData,
                success: function (response) {
                    $("#students").append(response);
                    $("#surnameStudent").val('');
                    $("#nameStudent").val('');
                    $("#patronymicStudent").val('');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                }
            });
        });

        $("body").on("click", "#students .del_button", function (e) {
            e.preventDefault();
            var clickedID = this.id.split("-");
            var DbNumberID = clickedID[1];
            var myData = 'studentsToDelete=' + DbNumberID;

            jQuery.ajax({
                type: "POST",
                url: "../response.php",
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
</script>

<?php
require_once "../db.php";

if (isset($_COOKIE['id'])) {
    $idCook = mysqli_real_escape_string($dbLink, $_COOKIE['id']);
    $sql = mysqli_query($dbLink, "SELECT `hash_user` FROM `user` WHERE `id_user` = '" . $idCook . "'");
    $data = mysqli_fetch_assoc($sql);

    if ($_COOKIE['hash'] == $data['hash_user']) {
        ?>
        <form action="../" method="post">
            <button>Назад</button>
        </form>

        <dl id="students">
            <dt>Студенты:</dt>
            <?php
            $sql = "SELECT `id_student`,`surname_student`, `name_student`, `patronymic_student`, `name_group` FROM `student` INNER JOIN `groups` ON `group_id` = `id_group` ORDER BY `name_group`, `surname_student`, `name_student`, `patronymic_student`";
            $Result = mysqli_query($dbLink, $sql);

            while ($row = mysqli_fetch_array($Result)) {
                $FIOstudent = $row["surname_student"] . " " . $row["name_student"] . " " . $row["patronymic_student"];
                echo '<dd id="item_' . $row["id_student"] . '" class="del_wrapper">' . $row["name_group"] . " " . $FIOstudent . " " . '<a href="#" class="del_button" id="del-' . $row["id_student"] . '"><img src="../css/cancel.gif" border="0"></a></dd>';
            }
            ?>
        </dl>
        <input onkeypress="return filter_input(event,/[А-ЯЁ]/i)" maxlength="15" id="surnameStudent"
               name="surname_student" type="text" placeholder="Фамилия" required>
        <input onkeypress="return filter_input(event,/[А-ЯЁ]/i)" maxlength="15" id="nameStudent" name="name_student"
               type="text" placeholder="Имя" required>
        <input onkeypress="return filter_input(event,/[А-ЯЁ]/i)" maxlength="15" id="patronymicStudent"
               name="patronymic_student" type="text" placeholder="Отчество">
        группа <select id="studentGroup" name="studentGroupList">
            <option value="">---</option>
            <?php
            $Result = mysqli_query($dbLink, "SELECT * FROM `groups` ORDER BY `name_group`");
            while ($row = mysqli_fetch_assoc($Result)) {
                echo '<option value="' . $row['id_group'] . '">' . $row['name_group'] . '</option>';
            }
            ?>
        </select>
        <button id="saveStudents" class="clicker">Добавить</button>

        <br><br>
        <div id="student" class="change">
            <table>
                <thead>
                <tr>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Отчество</th>
                    <th>Группа</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT `id_student`,`surname_student`, `name_student`, `patronymic_student` FROM `student` ORDER BY `surname_student`";
                $Result = mysqli_query($dbLink, $sql);
                while ($row = mysqli_fetch_array($Result)) {
                    echo '<tr>';
                    echo '<td onkeypress="return filter_td(event,/[А-ЯЁ]/i)" id="surname_student-id_student-' . $row['id_student'] . '" contenteditable="true">' . $row['surname_student'] . '</td>';
                    echo '<td onkeypress="return filter_td(event,/[А-ЯЁ]/i)" id="name_student-id_student-' . $row['id_student'] . '" contenteditable="true">' . $row['name_student'] . '</td>';
                    echo '<td onkeypress="return filter_td(event,/[А-ЯЁ]/i)" id="patronymic_student-id_student-' . $row['id_student'] . '" contenteditable="true">' . $row['patronymic_student'] . '</td>';

                    echo '<td><select id="group_id-id_student-' . $row['id_student'] . '" contenteditable="true">';
                    $Query = mysqli_query($dbLink, "SELECT * FROM `groups` INNER JOIN `student` ON `id_group` = `group_id` WHERE `id_student` = '" . $row['id_student'] . "'");
                    $group = mysqli_fetch_assoc($Query);
                    if (isset($group['id_group']) && strlen($group['id_group']) > 0) {
                        echo '<option value="' . $group['id_group'] . '">' . $group['name_group'] . '</option>';
                    }
                    echo '<option value="">---</option>';

                    $Query = mysqli_query($dbLink, "SELECT * FROM `groups` GROUP BY `name_group` ORDER BY `name_group`");
                    while ($option = mysqli_fetch_assoc($Query)) {
                        echo '<option value="' . $option['id_group'] . '">' . $option['name_group'] . '</option>';
                    }
                    echo '</select></td>';

                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
            <button id="save" class="clicker">Сохранить</button>
        </div>

        <?php
    } else {
        echo 'Вы не авторизованы<br>';
        echo '<a href="../login.php">Авторизация</a>';
    }
} else {
    echo 'Вы не авторизованы<br>';
    echo '<a href="../login.php">Авторизация</a>';
}
?>

<script type="text/javascript">
    var contentold = {};

    function savedata(elementidsave, contentsave) {

        $.ajax({
            url: '../save.php',
            type: 'POST',
            data: {
                content: contentsave,
                id: elementidsave
            },
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
                if (contentsave != contentold[elementidsave]) {
                    savedata(elementidsave, contentsave);
                }
            });
    });
</script>

</body>
</html>