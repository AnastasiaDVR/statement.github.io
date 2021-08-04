<!DOCTYPE html>
<html>
<head>
    <title>Statement</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <meta charset="utf-8">
    <script src="//code.jquery.com/jquery-3.4.1.js"></script>
    <script type="text/javascript" src="js/select.js"></script>
    <script type="text/javascript" src="js/add.js"></script>
    <script type="text/javascript" src="js/update.js"></script>
</head>

<body>
<?php
require_once "db.php";

if (isset($_COOKIE['id'])) {
    $id = mysqli_real_escape_string($dbLink, $_COOKIE['id']);
    $sql = mysqli_query($dbLink, "SELECT `hash_user` FROM `user` WHERE `id_user` = '" . $id . "'");
    $data = mysqli_fetch_assoc($sql);

    if ($_COOKIE['hash'] == $data['hash_user']) {

        ?>

        <div class="header">
            <a href="logout.php">Выйти</a>
        </div>

        <div class="content">

        <?php
        $query = mysqli_query($dbLink, "SELECT `surname_user`, `name_user`, `patronymic_user`, `role_id` FROM `user` WHERE `id_user` = '" . $id . "'");
        $data = mysqli_fetch_assoc($query);

        if ($data['role_id'] == '1') {
            echo '<div class="create">';
            echo '<a href="add/user.php">Пользователи</a>';
            echo '<a href="add/groups.php">Группы</a>';
            echo '<a href="add/student.php">Студенты</a>';
            echo '<a href="add/discipline.php">Дисциплины</a>';
            echo '<a href="add/statement.php">Табель</a>';
            echo '</div>';
        } elseif ($data['role_id'] == '2') {
            echo '<div class="create">';
            echo '<a href="add/groups.php">Группы</a>';
            echo '<a href="add/student.php">Студенты</a>';
            echo '<a href="add/discipline.php">Дисциплины</a>';
            echo '<a href="add/statement.php">Табель</a>';
            echo '</div>';
        } elseif ($data['role_id'] == '3') {
            echo '<div class="create">';
            echo '<a href="add/statement.php">Табель</a>';
            echo '</div>';
        }

        if ($data['role_id'] == '1' or $data['role_id'] == '3') {
            ?>

            <div class="container">
                <div class="selectDIV">
                    <label class="prefix" for="get_group">Группа:</label>
                    <select id="get_group" name="get_group">
                        <option value="">---</option>
                        <?php
                        if ($data['role_id'] == '1') {
                            $result = mysqli_query($dbLink, "SELECT * FROM `groups` GROUP BY `name_group`");
                        } else {
                            $result = mysqli_query($dbLink, "SELECT `id_group`, `name_group` FROM `statement` INNER JOIN `groups` ON `group_id` = `id_group` INNER JOIN `discipline` ON `discipline_id` = `id_discipline` INNER JOIN `user` ON `user_id` = `id_user` WHERE `id_user` = '" . $id . "' GROUP BY `name_group`");
                        }
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<option value='" . $row["id_group"] . "'>" . $row["name_group"] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div id="sub_discipline" class="selectDIV">
                    <label class="prefix" for="get_discipline">Дисциплина:</label>
                    <select id="get_discipline" name="get_discipline"></select>
                </div>

                <div id="sub_date" class="selectDIV">
                    <label class="prefix" for="get_date">Период:</label>
                    <select id="get_date" name="get_date"></select>
                </div>

                <div id="sub_statement">
                    <div id="get_statement"></div>
                </div>
            </div>
            </div>

            <?php
        }
    } else {
        echo 'Вы не авторизованы<br>';
        echo '<a href="login.php">Авторизация</a>';
    }

} elseif (isset($_COOKIE['idS'])) {
    $sql = mysqli_query($dbLink, "SELECT `hash_student` FROM `student` WHERE `id_student` = '" . $_COOKIE['idS'] . "'");
    $data = mysqli_fetch_assoc($sql);

    if ($_COOKIE['hashS'] == $data['hash_student']) {
        ?>

        <div class="header">
            <a href="logout.php">Выйти</a>
        </div>

        <div class="content">

            <?php
            $query = mysqli_query($dbLink, "SELECT `surname_student`, `name_student`, `patronymic_student`, `group_id` FROM `student` WHERE `id_student` = '" . $_COOKIE['idS'] . "'");
            $data = mysqli_fetch_assoc($query);

            echo "Привет, " . $data['name_student'];
            ?>
            <a href="add/tabel.php">Табель</a>

        </div>
    <?php } else {
        echo 'Вы не авторизованы<br>';
        echo '<a href="login.php">Авторизация</a>';
    }

} else {
    echo 'Вы не авторизованы<br>';
    echo '<a href="login.php">Авторизация</a>';
}
?>

</body>
</html>