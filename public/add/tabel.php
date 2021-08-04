<!DOCTYPE html>
<html>
<head>
    <title>Табель</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="stylish.css">
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script type="text/javascript" src="../js/student.js"></script>
</head>

<body>

<form action="../" method="post">
    <button>Назад</button>
</form>

<?php
require_once "../db.php";

if (isset($_COOKIE['idS'])) {
$idS = mysqli_real_escape_string($dbLink, $_COOKIE['idS']);
$sql = mysqli_query($dbLink, "SELECT `hash_student` FROM `student` WHERE `id_student` = '" . $idS . "'");
$data = mysqli_fetch_assoc($sql);

if ($_COOKIE['hashS'] == $data['hash_student']){

$query = mysqli_query($dbLink, "SELECT `surname_student`, `name_student`, `patronymic_student`, `group_id` FROM `student` WHERE `id_student` = '" . $idS . "'");
$data = mysqli_fetch_assoc($query);
?>

<div class="selectDIV">
    <label class="prefix" for="get_group">Группа:</label>
    <select id="get_group" name="get_group">
        <option value="">---</option>
        <?php
        $result = mysqli_query($dbLink, "SELECT * FROM `groups` WHERE `id_group` = '" . $data['group_id'] . "' GROUP BY `name_group`");
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

<?php
} else {
    echo 'Вы не авторизованы<br>';
    echo '<a href="../login.php">Авторизация</a>';
}
} else {
    echo 'Вы не авторизованы<br>';
    echo '<a href="../login.php">Авторизация</a>';
}
