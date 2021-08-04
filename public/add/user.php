<!DOCTYPE html>
<html>
<head>
    <title>Пользователи</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="stylish.css">
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script type="text/javascript" src="../js/test.js"></script>
</head>

<body>

<script>
    $(document).ready(function () {
        $("#saveUser").click(function (e) {
            e.preventDefault();
            if (($("#surnameUser").val() === "") || ($("#nameUser").val() === "") || ($("#loginUser").val() === "") || ($("#passwordUser").val() === "") || ($("#userRole option:selected").val() === "")) {
                alert("Заполните все поля!");
                return false;
            }

            var myData = {
                "surname_user": $("#surnameUser").val(),
                "name_user": $("#nameUser").val(),
                "patronymic_user": $("#patronymicUser").val(),
                "login_user": $("#loginUser").val(),
                "password_user": $("#passwordUser").val(),
                "userRoleList": $("#userRole").val()
            };

            jQuery.ajax({
                type: "POST",
                url: "../response.php",
                dataType: "text",
                data: myData,
                success: function (response) {
                    $("#users").append(response);
                    $("#surnameUser").val('');
                    $("#nameUser").val('');
                    $("#patronymicUser").val('');
                    $("#loginUser").val('');
                    $("#passwordUser").val('');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                }
            });
        });

        $("body").on("click", "#users .del_button", function (e) {
            e.preventDefault();
            var clickedID = this.id.split("-");
            var DbNumberID = clickedID[1];
            var myData = 'userToDelete=' + DbNumberID;

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
    $id = mysqli_real_escape_string($dbLink, $_COOKIE['id']);
    $sql = mysqli_query($dbLink, "SELECT `hash_user` FROM `user` WHERE `id_user` = '" . $id . "'");
    $data = mysqli_fetch_assoc($sql);

    if ($_COOKIE['hash'] == $data['hash_user']) {
        ?>

        <form action="../" method="post">
            <button>Назад</button>
        </form>

        <dl id="users">
            <dt>Пользователи:</dt>
            <?php
            $Result = mysqli_query($dbLink, "SELECT `id_user`,`surname_user`, `name_user`, `patronymic_user` FROM `user` WHERE `role_id` = '2' ORDER BY `surname_user`, `name_user`, `patronymic_user`");
            while ($row = mysqli_fetch_array($Result)) {
                $FIOuser = $row["surname_user"] . " " . $row["name_user"] . " " . $row["patronymic_user"];
                echo '<dd id="item_' . $row["id_user"] . '" class="del_wrapper">' . $FIOuser . " " . '<a href="#" class="del_button" id="del-' . $row["id_user"] . '"><img src="../css/cancel.gif" border="0"></a></dd>';
            }
            ?>
            <dt>Преподаватели:</dt>
            <?php
            $Result = mysqli_query($dbLink, "SELECT `id_user`,`surname_user`, `name_user`, `patronymic_user` FROM `user` WHERE `role_id` = '3' ORDER BY `surname_user`, `name_user`, `patronymic_user`");
            while ($row = mysqli_fetch_array($Result)) {
                $FIOuser = $row["surname_user"] . " " . $row["name_user"] . " " . $row["patronymic_user"];
                echo '<dd id="item_' . $row["id_user"] . '" class="del_wrapper">' . $FIOuser . " " . '<a href="#" class="del_button" id="del-' . $row["id_user"] . '"><img src="../css/cancel.gif" border="0"></a></dd>';
            }
            ?>
        </dl>
        <input onkeypress="return filter_input(event,/[А-ЯЁ]/i)" maxlength="15" id="surnameUser" name="surname_user"
               type="text" placeholder="Фамилия" required>
        <input onkeypress="return filter_input(event,/[А-ЯЁ]/i)" maxlength="15" id="nameUser" name="name_user"
               type="text" placeholder="Имя" required>
        <input onkeypress="return filter_input(event,/[А-ЯЁ]/i)" maxlength="15" id="patronymicUser"
               name="patronymic_user" type="text" placeholder="Отчество"><br><br>

        <input onkeypress="return filter_input(event,/[A-ZА-ЯЁ0-9]/i)" maxlength="15" id="loginUser" name="login_user"
               type="text" placeholder="Логин" required>
        <input maxlength="15" id="passwordUser" name="password_user" type="password" placeholder="Пароль" required>
        доступ <select id="userRole" name="userRoleList">
            <option value="">---</option>
            <?php
            $result = mysqli_query($dbLink, "SELECT * FROM `role` WHERE `id_role` = '2'");
            $row = mysqli_fetch_assoc($result);
            echo '<option value="2">' . $row['name_role'] . '</option>';
            $result = mysqli_query($dbLink, "SELECT * FROM `role` WHERE `id_role` = '3'");
            $row = mysqli_fetch_assoc($result);
            echo '<option value="3">' . $row['name_role'] . '</option>';
            ?>
        </select><br>
        <button id="saveUser" class="clicker">Добавить</button>

        <br><br>
        <div id="user" class="change">
            <table>
                <thead>
                <tr>
                    <th>Доступ</th>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Отчество</th>
                    <th>Логин</th>
                    <th>Пароль</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT `id_user`, `surname_user`, `name_user`, `patronymic_user`, `login_user`, `password_user`, `role_id` FROM `user` INNER JOIN `role` ON `id_role` = `role_id` WHERE `role_id` = '2' or `role_id` = '3' ORDER BY `name_role`, `surname_user`, `name_user`, `patronymic_user`";
                $Result = mysqli_query($dbLink, $sql);
                while ($row = mysqli_fetch_array($Result)) {
                    echo '<tr>';

                    echo '<td><select id="role_id-id_user-' . $row['id_user'] . '" contenteditable="true">';
                    $Query = mysqli_query($dbLink, "SELECT * FROM `role` INNER JOIN `user` ON `id_role` = `role_id` WHERE `id_user` = '" . $row['id_user'] . "'");
                    $group = mysqli_fetch_assoc($Query);
                    if (isset($group['id_role']) && strlen($group['id_role']) > 0) {
                        echo '<option value="' . $group['id_role'] . '">' . $group['name_role'] . '</option>';
                    }
                    echo '<option value="">---</option>';

                    $Query = mysqli_query($dbLink, "SELECT `id_role`, `name_role` FROM `role` WHERE `id_role` = '2'");
                    $option = mysqli_fetch_assoc($Query);
                    echo '<option value="' . $option['id_role'] . '">' . $option['name_role'] . '</option>';
                    $Query = mysqli_query($dbLink, "SELECT `id_role`, `name_role` FROM `role` WHERE `id_role` = '3'");
                    $option = mysqli_fetch_assoc($Query);
                    echo '<option value="' . $option['id_role'] . '">' . $option['name_role'] . '</option>';
                    echo '</select></td>';

                    echo '<td onkeypress="return filter_td(event,/[А-Я]/i)" id="surname_user-id_user-' . $row['id_user'] . '" contenteditable="true">' . $row['surname_user'] . '</td>';
                    echo '<td onkeypress="return filter_td(event,/[А-Я]/i)" onkeypress="return filter_td(event,/[A-ZА-Я]/i)" id="name_user-id_user-' . $row['id_user'] . '" contenteditable="true">' . $row['name_user'] . '</td>';
                    echo '<td onkeypress="return filter_td(event,/[А-Я]/i)" id="patronymic_user-id_user-' . $row['id_user'] . '" contenteditable="true">' . $row['patronymic_user'] . '</td>';
                    echo '<td onkeypress="return filter_td(event,/[A-ZА-Я]/i)" id="login_user-id_user-' . $row['id_user'] . '" contenteditable="true">' . $row['login_user'] . '</td>';
                    echo '<td id="password_user-id_user-' . $row['id_user'] . '" contenteditable="true"></td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
            <button id="save" class="clicker">Сохранить</button>
        </div>

        <br><br>
        <div id="studentLogin" class="change">
            <table>
                <thead>
                <tr>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Отчество</th>
                    <th>Группа</th>
                    <th>Логин</th>
                    <th>Пароль</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT `id_student`,`surname_student`, `name_student`, `patronymic_student`, `login_student`, `password_student` FROM `student` ORDER BY `surname_student`, `name_student`, `patronymic_student`";
                $Result = mysqli_query($dbLink, $sql);
                while ($row = mysqli_fetch_array($Result)) {
                    echo '<tr>';
                    echo '<td onkeypress="return filter_td(event,/[А-Я]/i)" id="surname_student-id_student-' . $row['id_student'] . '" contenteditable="true">' . $row['surname_student'] . '</td>';
                    echo '<td onkeypress="return filter_td(event,/[А-Я]/i)" id="name_student-id_student-' . $row['id_student'] . '" contenteditable="true">' . $row['name_student'] . '</td>';
                    echo '<td onkeypress="return filter_td(event,/[А-Я]/i)" id="patronymic_student-id_student-' . $row['id_student'] . '" contenteditable="true">' . $row['patronymic_student'] . '</td>';

                    echo '<td><select id="group_id-id_student-' . $row['id_student'] . '" contenteditable="true">';
                    $Query = mysqli_query($dbLink, "SELECT * FROM `groups` INNER JOIN `student` ON `id_group` = `group_id` WHERE `id_student` = '" . $row['id_student'] . "'");
                    $group = mysqli_fetch_assoc($Query);
                    if (isset($group['id_group']) && strlen($group['id_group']) > 0) {
                        echo '<option value="' . $group['id_group'] . '">' . $group['name_group'] . '</option>';
                    }
                    echo '<option value="">---</option>';

                    $Query = mysqli_query($dbLink, "SELECT `id_group`, `name_group` FROM `groups` GROUP BY `name_group` ORDER BY `name_group`");
                    while ($option = mysqli_fetch_assoc($Query)) {
                        echo '<option value="' . $option['id_group'] . '">' . $option['name_group'] . '</option>';
                    }
                    echo '</select></td>';

                    echo '<td id="login_student-id_student-' . $row['id_student'] . '" contenteditable="true">' . $row['login_student'] . '</td>';
                    echo '<td onkeypress="return filter_td(event,/[A-ZА-Я0-9]/i)" id="password_student-id_student-' . $row['id_student'] . '" contenteditable="true"></td>';

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