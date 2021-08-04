<!DOCTYPE html>
<html>
<head>
    <title>Дисциплины</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="stylish.css">
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script type="text/javascript" src="../js/test.js"></script>
</head>

<body>

<script>
    $(document).ready(function () {
        $("#saveDiscipline").click(function (e) {
            e.preventDefault();
            if ($("#nameDiscipline").val() === "" || ($("#disciplineUser option:selected").val() === "")) {
                alert("Заполните все поля!");
                return false;
            }

            var myData = {
                "name_discipline": $("#nameDiscipline").val(),
                "disciplineUserList": $("#disciplineUser").val()
            };

            jQuery.ajax({
                type: "POST",
                url: "../response.php",
                dataType: "text",
                data: myData,
                success: function (response) {
                    $("#disciplines").append(response);
                    $("#nameDiscipline").val('');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                }
            });
        });

        $("body").on("click", "#disciplines .del_button", function (e) {
            e.preventDefault();
            var clickedID = this.id.split("-");
            var DbNumberID = clickedID[1];
            var myData = 'disciplinesToDelete=' + DbNumberID;

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
include_once "../db.php";

if (isset($_COOKIE['id'])) {
    $id = mysqli_real_escape_string($dbLink, $_COOKIE['id']);
    $sql = mysqli_query($dbLink, "SELECT `hash_user` FROM `user` WHERE `id_user` = '" . $id . "'");
    $data = mysqli_fetch_assoc($sql);

    if ($_COOKIE['hash'] == $data['hash_user']) {
        ?>

        <form action="../" method="post">
            <button>Назад</button>
        </form>

        <dl id="disciplines">
            <dt>Дисциплины:</dt>
            <?php
            $sql = "SELECT `id_discipline`,`name_discipline`, `surname_user`, `name_user`, `patronymic_user` FROM `discipline` INNER JOIN `user` ON `user_id` = `id_user` ORDER BY `name_discipline`";
            $Result = mysqli_query($dbLink, $sql);

            while ($row = mysqli_fetch_array($Result)) {
                $FIOuser = $row["surname_user"] . " " . $row["name_user"] . " " . $row["patronymic_user"];
                echo '<dd id="item_' . $row["id_discipline"] . '" class="del_wrapper">' . $row["name_discipline"] . " - " . $FIOuser . " " . '<a href="#" class="del_button" id="del-' . $row["id_discipline"] . '"><img src="../css/cancel.gif" border="0"></a></dd>';
            }
            ?>
        </dl>
        <input onkeypress="return filter_input(event,/[A-ZА-ЯЁ ]/i)" id="nameDiscipline" name="name_discipline"
               type="text" placeholder="Название дисциплины" required>
        преподаватель <select id="disciplineUser" name="disciplineUserList">
            <option value="">---</option>
            <?php
            $Result = mysqli_query($dbLink, "SELECT * FROM `user` WHERE `role_id` = '3'");
            while ($row = mysqli_fetch_assoc($Result)) {
                $FIOuser = $row["surname_user"] . " " . $row["name_user"] . " " . $row["patronymic_user"];
                echo '<option value="' . $row['id_user'] . '">' . $FIOuser . '</option>';
            }
            ?>
        </select>

        <button id="saveDiscipline" class="clicker">Добавить</button>

        <br><br>
        <div id="discipline" class="change">
            <table>
                <thead>
                <tr>
                    <th>Дисциплина</th>
                    <th>ФИО преподавателя</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT `id_discipline`, `name_discipline` FROM `discipline` ORDER BY `name_discipline`";
                $Result = mysqli_query($dbLink, $sql);
                while ($row = mysqli_fetch_array($Result)) {
                    echo '<tr>';
                    echo '<td onkeypress="return filter_td(event,/[A-ZА-ЯЁ ]/i)" id="name_discipline-id_discipline-' . $row['id_discipline'] . '" contenteditable="true">' . $row['name_discipline'] . '</td>';

                    echo '<td><select id="user_id-id_discipline-' . $row['id_discipline'] . '" contenteditable="true">';
                    $Query = mysqli_query($dbLink, "SELECT * FROM `user` INNER JOIN `discipline` ON `id_user` = `user_id` WHERE `id_discipline` = '" . $row['id_discipline'] . "'");
                    $group = mysqli_fetch_assoc($Query);
                    if (isset($group['id_user']) && strlen($group['id_user']) > 0) {
                        $FIOuser = $group['surname_user'] . " " . $group['name_user'] . " " . $group['patronymic_user'];
                        echo '<option value="' . $group['id_user'] . '">' . $FIOuser . '</option>';
                    }
                    echo '<option value="">---</option>';

                    $Query = mysqli_query($dbLink, "SELECT * FROM `user` WHERE `role_id` = '3' ORDER BY `surname_user`, `name_user`, `patronymic_user`");
                    while ($option = mysqli_fetch_assoc($Query)) {
                        $FIOuser = $option['surname_user'] . " " . $option['name_user'] . " " . $option['patronymic_user'];
                        echo '<option value="' . $option['id_user'] . '">' . $FIOuser . '</option>';
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