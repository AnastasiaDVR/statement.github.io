<?php
require_once("db.php");

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    if (isset($_COOKIE['id'])) {
        $id = mysqli_real_escape_string($dbLink, $_COOKIE['id']);
        $sql = mysqli_query($dbLink, "SELECT `hash_user`, `role_id` FROM `user` WHERE `id_user` = '" . $id . "'");
        $data = mysqli_fetch_assoc($sql);

        if ($_COOKIE['hash'] == $data['hash_user']) {

            $statement = mysqli_real_escape_string($dbLink, $_POST["statement"]);
            $group = mysqli_real_escape_string($dbLink, $_POST["group"]);

            if ($data['role_id'] == '1') {
                $Result = "SELECT `id_statement`, DATE_FORMAT(date_open, '%d.%m.%Y') as date_open, DATE_FORMAT(date_close, '%d.%m.%Y') as date_close,`discipline_id`,`name_discipline`,`name_group`,`surname_user`,`name_user`,`patronymic_user` FROM `statement` INNER JOIN `groups` ON `group_id` = `id_group` INNER JOIN `discipline` ON `discipline_id` = `id_discipline` INNER JOIN `user` ON `user_id` = `id_user` WHERE `id_statement` = '" . $statement . "'";
            } else {
                $Result = "SELECT `id_statement`, DATE_FORMAT(date_open, '%d.%m.%Y') as date_open, DATE_FORMAT(date_close, '%d.%m.%Y') as date_close,`discipline_id`,`name_discipline`,`name_group`,`surname_user`,`name_user`,`patronymic_user` FROM `statement` INNER JOIN `groups` ON `group_id` = `id_group` INNER JOIN `discipline` ON `discipline_id` = `id_discipline` INNER JOIN `user` ON `user_id` = `id_user` WHERE `id_statement` = '" . $statement . "' AND `id_user` = '" . $id . "'";
            }

            if (mysqli_query($dbLink, $Result)) {
                $Result = mysqli_query($dbLink, $Result);
                $row = mysqli_fetch_assoc($Result);
                $id_statement = $row["id_statement"];
                $date_open = $row["date_open"];
                $date_close = $row["date_close"];
                $discipline_id = $row["discipline_id"];
                $name_discipline = $row["name_discipline"];
                $name_group = $row["name_group"];
                $teacher = $row["surname_user"] . " " . $row["name_user"] . " " . $row["patronymic_user"];
                if (isset($id_statement) && strlen($id_statement) > 0) {
                    echo '<br><div class="label">Табель за <b>' . $date_open . ' - ' . $date_close . '</b> группа <b>' . $name_group . '</b> преподователь: <b>' . $teacher . '</b><br>';
                    echo 'учебная дисциплина: <b>' . $name_discipline . '</b></div><br>';


                    echo '<table id="statementTable_' . $row['id_statement'] . '" border="1">'; ?>
                    <div>
                        <thead>
                        <tr>
                            <th scope="col" rowspan="2">№</th>
                            <th scope="colgroup" rowspan="2" colspan="2">Фамилия Имя</th>
                            <th scope="colgroup" colspan="26">ДАТЫ ПРОВЕДЕНИЯ ЗАНЯТИЙ</th>
                        </tr>

                        <tr>
                            <?php
                            $id_lesson = array();
                            $Result = mysqli_query($dbLink, "SELECT * FROM `lesson` WHERE `statement_id` = '" . $id_statement . "' ORDER BY `id_lesson`");
                            while ($row = mysqli_fetch_assoc($Result)) {
                                echo '<th id="date_lesson-id_lesson-' . $row['id_lesson'] . '" class="edit date" scope="col" colspan="2" contenteditable="true">' . $row['date_lesson'] . '</th>';
                            }
                            ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $count = 1;
                        $Result = mysqli_query($dbLink, "SELECT `id_student`,`surname_student`, `name_student` FROM `student` WHERE `group_id` = '" . $group . "' ORDER BY `surname_student`, `name_student`");
                        while ($row = mysqli_fetch_assoc($Result)) {
                        $id_student = $row['id_student'];
                        ?>
                        <tr>
                            <td scope="row"><?php echo $count ?></td>
                            <td scope="row"><?php echo $row['surname_student'] ?></td>
                            <td scope="row"><?php echo $row['name_student'] ?></td>
                            <?php
                            $sqlResult = mysqli_query($dbLink, "SELECT `id_lesson`, `date_lesson`, `control1`, `control2`, `statement_id`, `theme_id` FROM `lesson` WHERE `statement_id` = '" . $id_statement . "' ORDER BY `id_lesson`");
                            while ($row = mysqli_fetch_assoc($sqlResult)) {
                                $id_lesson = $row['id_lesson'];

                                $sql = mysqli_query($dbLink, "SELECT * FROM `record` WHERE `lesson_id` = '" . $id_lesson . "' AND `student_id` = '" . $id_student . "' ORDER BY `id_record`");
                                $row = mysqli_fetch_assoc($sql);
                                if (isset($row['id_record']) && strlen($row['id_record']) > 0) {

                                    echo '<td id="mark1-id_record-' . $row['id_record'] . '" class="edit mark1" width="2" contenteditable="true">' . $row['mark1'] . '</td>';
                                    echo '<td id="mark2-id_record-' . $row['id_record'] . '" class="edit mark2" width="2" contenteditable="true">' . $row['mark2'] . '</td>';

                                } else {
                                    $sql = mysqli_query($dbLink, "SELECT max(`id_record`) as `id_record` FROM `record`");
                                    $response = mysqli_fetch_assoc($sql);
                                    $id_record = $response['id_record'] + 1;
                                    $sql = mysqli_query($dbLink, "INSERT INTO `record`(`mark1`, `mark2`, `lesson_id`, `student_id`) VALUES  ('', '', '" . $id_lesson . "','" . $id_student . "')");
                                    echo '<td id="mark1-id_record-' . $id_record . '" class="edit mark1" width="2" contenteditable="true"></td>';
                                    echo '<td id="mark2-id_record-' . $id_record . '" class="edit mark2" width="2" contenteditable="true"></td>';
                                }
                            }
                            $count++;
                            }
                            ?>
                        </tr>

                        <tr>
                            <td colspan="3">Контролирующие мероприятия</td>
                            <?php
                            $Result = mysqli_query($dbLink, "SELECT * FROM `lesson` INNER JOIN `theme` ON `theme_id` = `id_theme` WHERE `statement_id` = '" . $id_statement . "' ORDER BY `id_lesson`");
                            while ($row = mysqli_fetch_assoc($Result)) {
                                echo '<td id="control1-id_lesson-' . $row['id_lesson'] . '" class="edit control1" width="2" contenteditable="true">' . $row['control1'] . '</td>';
                                echo '<td id="control2-id_lesson-' . $row['id_lesson'] . '" class="edit control2" width="2" contenteditable="true">' . $row['control2'] . '</td>';

                            } ?>
                        </tr>
                        </tbody>
                        </table>
                    </div>
                    <div>
                        <button id="save" class="clicker">Сохранить</button>
                        <div id="status"></div>
                    </div>
                    <br>
                    <div>
                        <table id="hometask" border="1" style="float: left;">
                            <thead>
                            <th scope="col" rowspan="2">дата занятия</th>
                            <th scope="col" rowspan="2">КРАТКОЕ СОДЕРЖАНИЕ ЗАНЯТИЯ,<br> ДОМАШНЕЕ ЗАДАНИЕ</th>
                            </thead>
                            <tbody>
                            <tr>
                                <?php
                                $Result = mysqli_query($dbLink, "SELECT * FROM `lesson` INNER JOIN `theme` ON `theme_id` = `id_theme` WHERE `statement_id` = '" . $id_statement . "' ORDER BY `id_theme`");
                                while ($row = mysqli_fetch_assoc($Result)) {
                                    if ($row['date_lesson'] !== "") {
                                        echo '<td>' . $row['date_lesson'] . "</td>";
                                        echo '<td id="name_theme-id_theme-' . $row['id_theme'] . '" class="edit theme" width="2" contenteditable="true">' . $row['name_theme'] . '</td></tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                }
            } else {
                header('HTTP/1.1 No data');
                exit();
            }

        } else {
            echo 'Вы не авторизованы<br>';
            echo '<a href="../login.php">Авторизация</a>';
        }
    } else {
        echo 'Вы не авторизованы<br>';
        echo '<a href="../login.php">Авторизация</a>';
    }

}
?>

<script type="text/javascript">
    var contentold = {};

    function savedata(elementidsave, contentsave) {

        $.ajax({
            url: 'save.php',
            type: 'POST',
            data: {
                content: contentsave,
                id: elementidsave
            },
            success: function (data) {
                if (contentsave !== null) {
                    $('#' + elementidsave).html(data);
                    $('<div id="status">Сохранено</div>')
                        .insertAfter('#' + elementidsave)
                        .addClass("success")
                        .fadeIn('fast')
                        .delay(1000)
                        .fadeOut('slow', function () {
                            this.remove();
                        });
                } else {
                    $('<div id="status">Ошибка</div>')
                        .insertAfter('#' + elementidsave)
                        .addClass("error")
                        .fadeIn('fast')
                        .delay(3000)
                        .fadeOut('slow', function () {
                            this.remove();
                        });
                }
            }
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
                var contentsave = $(this).html();
                event.stopImmediatePropagation();
                if (contentsave != contentold[elementidsave]) {
                    savedata(elementidsave, contentsave);
                }
            });
    });
</script>