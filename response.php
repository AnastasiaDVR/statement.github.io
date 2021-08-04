<?php
require_once("db.php");

if (isset($_COOKIE['id'])) { 
  $sql = mysqli_query($dbLink,"SELECT `hash_user` FROM `user` WHERE `id_user` = '".$_COOKIE['id']."'");
  $data = mysqli_fetch_assoc($sql);

    if ($_COOKIE['hash'] == $data['hash_user']) {
################ П О Л Ь З О В А Т Е Л Ь ##############
        if(isset($_POST["surname_user"]) && strlen($_POST["surname_user"])>0)
        {
            $surnameToSave = filter_var($_POST["surname_user"],FILTER_SANITIZE_STRING);
            $surnameToSave = htmlspecialchars(strip_tags(stripslashes($surnameToSave)));
            $surnameToSave = mysqli_real_escape_string($dbLink, $surnameToSave);

            $nameToSave = filter_var($_POST["name_user"],FILTER_SANITIZE_STRING);
            $nameToSave = htmlspecialchars(strip_tags(stripslashes($nameToSave)));
            $nameToSave = mysqli_real_escape_string($dbLink, $nameToSave);

            $patronymicToSave = filter_var($_POST["patronymic_user"],FILTER_SANITIZE_STRING);
            $patronymicToSave = htmlspecialchars(strip_tags(stripslashes($patronymicToSave)));
            $patronymicToSave = mysqli_real_escape_string($dbLink, $patronymicToSave);

            $loginToSave = filter_var($_POST["login_user"],FILTER_SANITIZE_STRING);
            $loginToSave = htmlspecialchars(strip_tags(stripslashes($loginToSave)));
            $loginToSave = mysqli_real_escape_string($dbLink, $loginToSave);

            $passwordToSave = filter_var($_POST["password_user"],FILTER_SANITIZE_STRING);
            $passwordToSave = mysqli_real_escape_string($dbLink, $passwordToSave);
            $passwordToSave = md5(md5(trim($passwordToSave)));

            $roleToSave = filter_var($_POST["userRoleList"],FILTER_SANITIZE_STRING);
            $roleToSave = mysqli_real_escape_string($dbLink, $roleToSave);

            $content = $surnameToSave.$nameToSave.$patronymicToSave;

            if (preg_match("/^[а-яёА-ЯЁ]+$/u", $content)) {
                $sql = mysqli_query($dbLink, "SELECT `login_user` FROM `user` WHERE `login_user` = '".$loginToSave."'");
                $row = mysqli_fetch_assoc($sql);

                if (isset($row['login_user']) && strLen($row['login_user'])>0) {
                    header('HTTP/1.1 500 Login already exists');
                    exit();
                }else{
                    $sqlQuery = "INSERT INTO `user`(`surname_user`, `name_user`, `patronymic_user`, `login_user`, `password_user`, `role_id`) VALUES ('".$surnameToSave."', '".$nameToSave."', '".$patronymicToSave."', '".$loginToSave."', '".$passwordToSave."', '".$roleToSave."')";
                    if(mysqli_query($dbLink, $sqlQuery))
                    {
                        $my_id = mysqli_insert_id($dbLink);
                        $contentToSave = $surnameToSave." ".$nameToSave." ".$patronymicToSave;
                        echo '<dd id="item_'.$my_id.'" class="del_wrapper">'.$contentToSave." ".'<a href="#" class="del_button" id="del-'.$my_id.'"><img src="../css/cancel.gif" border="0"></a></dd>';
                        mysqli_close($dbLink);

                    }else{
                        header('HTTP/1.1 500 Could not insert record!');
                        exit();
                    }
                }
            }else{
                header('HTTP/1.1 500 Data is incorrect');
                exit();
            }

        }elseif(isset($_POST["userToDelete"]) && strlen($_POST["userToDelete"])>0 && is_numeric($_POST["userToDelete"])) {
            $idToDelete = filter_var($_POST["userToDelete"],FILTER_SANITIZE_NUMBER_INT);
            $idToDelete = mysqli_real_escape_string($dbLink, $idToDelete);

            $sqlQuery = "DELETE FROM `user` WHERE `id_user` = ".$idToDelete;
            if(!mysqli_query($dbLink, $sqlQuery))
            {
                header('HTTP/1.1 500 Could not delete record!');
                exit();
            }
            mysqli_close($dbLink);

        }

        ################ Г Р У П П А ##############
        elseif(isset($_POST["name_group"]) && strlen($_POST["name_group"])>0)
        {
            $contentToSave = filter_var($_POST["name_group"],FILTER_SANITIZE_STRING);
            $contentToSave = mysqli_real_escape_string($dbLink, $contentToSave);

            if (ctype_digit($contentToSave)){
                $sql = mysqli_query($dbLink, "SELECT `name_group` FROM `groups` WHERE `name_group` = '".$contentToSave."'");
                $row = mysqli_fetch_assoc($sql);

                if(isset($row["name_group"]) && strlen($row["name_group"])>0){
                    header('HTTP/1.1 500 Group already exists');
                    exit();
                }else{
                    $sqlQuery = "INSERT INTO `groups` (`name_group`) VALUES ('".$contentToSave."')";
                    if(mysqli_query($dbLink, $sqlQuery))
                    {
                        $my_id = mysqli_insert_id($dbLink);
                        echo '<dd id="item_'.$my_id.'" class="del_wrapper">'.$contentToSave." ".'<a href="#" class="del_button" id="del-'.$my_id.'"><img src="../css/cancel.gif" border="0"></a></dd>';
                        mysqli_close($dbLink);

                    }else{
                        header('HTTP/1.1 500 Could not insert record!');
                        exit();
                    }
                }
            }else{
                header('HTTP/1.1 500 Data is incorrect');
                        exit();
            }

        }elseif(isset($_POST["groupsToDelete"]) && strlen($_POST["groupsToDelete"])>0 && is_numeric($_POST["groupsToDelete"])) {
            $idToDelete = filter_var($_POST["groupsToDelete"],FILTER_SANITIZE_NUMBER_INT);
            $idToDelete = mysqli_real_escape_string($dbLink, $idToDelete);

            $sqlQuery = mysqli_query($dbLink, "SELECT `id_student` FROM `student` WHERE `group_id` = '".$idToDelete."'");
            $row = mysqli_fetch_assoc($sqlQuery);

            if(isset($row['id_student']) && strlen($row['id_student'])>0){
                header('HTTP/1.1 500 There are students in the group');
                exit();
            }else{
                $sqlQuery = "DELETE FROM `groups` WHERE `id_group` = ".$idToDelete;
                if(!mysqli_query($dbLink, $sqlQuery))
                {
                    header('HTTP/1.1 500 Could not delete record!');
                    exit();
                }
                mysqli_close($dbLink);
            }
        }


        ################ С Т У Д Е Н Т ##############
        elseif(isset($_POST["surname_student"]) && strlen($_POST["surname_student"])>0)
        {
            $surnameToSave = filter_var($_POST["surname_student"],FILTER_SANITIZE_STRING);
            $surnameToSave = htmlspecialchars(strip_tags(stripslashes($surnameToSave)));
            $surnameToSave = mysqli_real_escape_string($dbLink, $surnameToSave);

            $nameToSave = filter_var($_POST["name_student"],FILTER_SANITIZE_STRING);
            $nameToSave = htmlspecialchars(strip_tags(stripslashes($nameToSave)));
            $nameToSave = mysqli_real_escape_string($dbLink, $nameToSave);

            $patronymicToSave = filter_var($_POST["patronymic_student"],FILTER_SANITIZE_STRING);
            $patronymicToSave = htmlspecialchars(strip_tags(stripslashes($patronymicToSave)));
            $patronymicToSave = mysqli_real_escape_string($dbLink, $patronymicToSave);

            $loginToSave = $surnameToSave.substr($nameToSave, 0, 2).substr($patronymicToSave, 0, 2);

            $content = $surnameToSave.$nameToSave.$patronymicToSave;

            if (preg_match("/^[а-яёА-ЯЁ]+$/u", $content)) {
                $sql = mysqli_query($dbLink, "SELECT `login_student` FROM `student` WHERE `login_student` = '".$loginToSave."'");
                $row = mysqli_fetch_assoc($sql);
                if (isset($row['loginToSave']) && strLen($row['loginToSave'])>0) {
                    $chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
                    $max=3;
                    $size=StrLen($chars)-1;
                        while($max--)
                        {$loginToSave.=$chars[rand(0,$size)];}
                }


                $passwordToSave = $loginToSave;

                $studentGroupList = filter_var($_POST["studentGroupList"],FILTER_SANITIZE_STRING);
                $studentGroupList = mysqli_real_escape_string($dbLink, $studentGroupList);

                $sql = "SELECT `name_group` FROM `groups` WHERE `id_group` = '".$studentGroupList."'";
                $sqlResult = mysqli_query($dbLink, $sql);
                $row = mysqli_fetch_assoc($sqlResult);
                $nameGroup = $row['name_group'];

                $sqlQuery = "INSERT INTO `student`(`surname_student`, `name_student`, `patronymic_student`, `group_id`, `login_student`, `password_student`) 
                                VALUES ('".$surnameToSave."', '".$nameToSave."', '".$patronymicToSave."', ".$studentGroupList.", '".$loginToSave."', '".$passwordToSave."')";
                if(mysqli_query($dbLink, $sqlQuery))
                {
                    $my_id = mysqli_insert_id($dbLink);
                    $contentToSave = $surnameToSave." ".$nameToSave." ".$patronymicToSave." ".$nameGroup;
                    echo '<dd id="item_'.$my_id.'" class="del_wrapper">'.$contentToSave." ".'<a href="#" class="del_button" id="del-'.$my_id.'"><img src="../css/cancel.gif" border="0"></a></dd>';
                    mysqli_close($dbLink);

                }else{
                    header('HTTP/1.1 500 Could not insert record!');
                    exit();
                }
            }else{
                header('HTTP/1.1 500 Data is incorrect');
                exit();
            }

        }elseif(isset($_POST["studentsToDelete"]) && strlen($_POST["studentsToDelete"])>0 && is_numeric($_POST["studentsToDelete"])) {
            $idToDelete = filter_var($_POST["studentsToDelete"],FILTER_SANITIZE_NUMBER_INT);
            $idToDelete = mysqli_real_escape_string($dbLink, $idToDelete);

            $sqlq = mysqli_query($dbLink, "DELETE FROM `record` WHERE `student_id` = '".$idToDelete."'");

            $sqlQuery = "DELETE FROM `student` WHERE `id_student` = ".$idToDelete;
            if(!mysqli_query($dbLink, $sqlQuery))
            {
                header('HTTP/1.1 500 Could not delete record!');
                exit();
            }
            mysqli_close($dbLink);

        }

        ################ П Р Е Д М Е Т ##############
        elseif(isset($_POST["name_discipline"]) && strlen($_POST["name_discipline"])>0)
        {
            $content = filter_var($_POST["name_discipline"],FILTER_SANITIZE_STRING);
            $content = htmlspecialchars(strip_tags(stripslashes($content)));
            $content = mysqli_real_escape_string($dbLink, $content);

            if (preg_match("/^[а-яёА-ЯЁ]+$/u", $content)) {

                $disciplineUserList = filter_var($_POST["disciplineUserList"],FILTER_SANITIZE_STRING);
                $disciplineUserList = mysqli_real_escape_string($dbLink, $disciplineUserList);

                $sql = "SELECT `surname_user`, `name_user`, `patronymic_user` FROM `user` WHERE `id_user` = ".$disciplineUserList;
                $sqlResult = mysqli_query($dbLink, $sql);
                $row = mysqli_fetch_assoc($sqlResult);
                $nameUser = $row['surname_user']." ".$row['name_user']." ".$row['patronymic_user'];

                $sqlQuery = "INSERT INTO `discipline`(`name_discipline`, `user_id`) VALUES ('".$content."', '".$disciplineUserList."')";
                if(mysqli_query($dbLink, $sqlQuery))
                {
                    $my_id = mysqli_insert_id($dbLink);
                    $contentToSave = $content." - ".$nameUser;
                    echo '<dd id="item_'.$my_id.'" class="del_wrapper">'.$contentToSave." ".'<a href="#" class="del_button" id="del-'.$my_id.'"><img src="../css/cancel.gif" border="0"></a></dd>';
                    mysqli_close($dbLink);

                }else{
                    header('HTTP/1.1 500 Could not insert record!');
                    exit();
                }
            }else{
                header('HTTP/1.1 500 Data is incorrect');
                exit();
            }

        }elseif(isset($_POST["disciplinesToDelete"]) && strlen($_POST["disciplinesToDelete"])>0 && is_numeric($_POST["disciplinesToDelete"])) {
            $idToDelete = filter_var($_POST["disciplinesToDelete"],FILTER_SANITIZE_NUMBER_INT);
            $idToDelete = mysqli_real_escape_string($dbLink, $idToDelete);

            $sqlQuery = "DELETE FROM `discipline` WHERE `id_discipline` = ".$idToDelete;
            if(!mysqli_query($dbLink, $sqlQuery))
            {
                header('HTTP/1.1 500 Could not delete record!');
                exit();
            }
            mysqli_close($dbLink);

        }

        ################ Т А Б Е Л Ь ##############
        elseif((isset($_POST["date_open"]) && strlen($_POST["date_open"])>0) && (isset($_POST["date_close"]) && strlen($_POST["date_close"])>0))
        {
              $test_dataOpen = preg_replace('/[^0-9\.]/u', '', trim($_POST['date_open']));
              $test_dataOpen_ar = explode('.', $test_dataOpen);

              $test_dataClose = preg_replace('/[^0-9\.]/u', '', trim($_POST['date_close']));
              $test_dataClose_ar = explode('.', $test_dataClose);

              $dateTest = ($test_dataOpen_ar > $test_dataClose_ar);

            if ($dateTest) {
                header('HTTP/1.1 500 Date entered is not correct!');
                exit();
            }else{
                $year = $test_dataClose_ar[0] - $test_dataOpen_ar[0];
                if ($year > 1) {
                    header('HTTP/1.1 500 Year entered is not correct!');
                    exit();
                }else{

                    $date_open = mysqli_real_escape_string($dbLink, $_POST["date_open"]);
                    $date_close = mysqli_real_escape_string($dbLink, $_POST["date_close"]);
                    $disciplineStatementList = mysqli_real_escape_string($dbLink, $_POST["disciplineStatementList"]);
                    $groupStatementList = mysqli_real_escape_string($dbLink, $_POST["groupStatementList"]);

                    $sql = "SELECT `name_discipline` FROM `discipline` WHERE `id_discipline` = ".$disciplineStatementList;
                    $sqlResult = mysqli_query($dbLink, $sql);
                    $row = mysqli_fetch_assoc($sqlResult);
                    $name_discipline = $row['name_discipline'];

                    $sql = "SELECT `name_group` FROM `groups` WHERE `id_group` = ".$groupStatementList;
                    $sqlResult = mysqli_query($dbLink, $sql);
                    $row = mysqli_fetch_assoc($sqlResult);
                    $name_group = $row['name_group'];

                    $sqlQuery = "INSERT INTO `statement`(`date_open`, `date_close`, `discipline_id`, `group_id`) VALUES ('".$date_open."', '".$date_close."', '".$disciplineStatementList."', '".$groupStatementList."')";
                    if(mysqli_query($dbLink, $sqlQuery))
                    {
                        $my_id = mysqli_insert_id($dbLink);
                        $contentToSave = "(".$date_open." - ".$date_close.") ".$name_discipline." ".$name_group;
                        echo '<dd id="item_'.$my_id.'" class="del_wrapper">'.$contentToSave." ".'<a href="#" class="del_button" id="del-'.$my_id.'"><img src="../css/cancel.gif" border="0"></a></dd>';

                        for ($i=0; $i < 13; $i++) { 
                            $sqlQuery = mysqli_query($dbLink, "INSERT INTO `theme`(`name_theme`,`discipline_id`) VALUES ('', '".$disciplineStatementList."')");
                            $id_theme = mysqli_insert_id($dbLink);

                            $sqlQuery = mysqli_query($dbLink, "INSERT INTO `lesson`(`date_lesson`, `control1`, `control2`, `statement_id`, `theme_id`) VALUES ('','','','".$my_id."','".$id_theme."')");;

                            $sqlQuery = mysqli_query($dbLink, "SELECT * FROM `student` WHERE `group_id` = ".$groupStatementList);
                            while ($row = mysqli_fetch_assoc($sqlQuery)) {
                                $id_student = $row['id_student'];

                                $sql = mysqli_query($dbLink, "SELECT  MAX(`id_lesson`) as `id_lesson`, `date_lesson`, `control1`, `control2`, `statement_id`, `theme_id` FROM `lesson`");
                                $row = mysqli_fetch_assoc($sql);
                                $id_lesson = $row["id_lesson"];

                                $sql = mysqli_query($dbLink, "SELECT `id_record`, `mark1`, `mark2`, `lesson_id`, `student_id` FROM `record` WHERE `lesson_id` = '".$id_lesson."' AND `student_id` = '".$id_student."'");
                                $row = mysqli_fetch_assoc($sql);
                                if (!isset($row['id_record']) && strlen($row['id_record'])==0) {
                                    $Result = mysqli_query($dbLink, "INSERT INTO `record`(`mark1`, `mark2`, `lesson_id`, `student_id`) VALUES  ('', '', '".$id_lesson."','".$id_student."')");
                                }
                            }
                        }
                        mysqli_close($dbLink);

                    }else{
                        header('HTTP/1.1 500 Could not insert record!');
                        exit();
                    }
                }
            }
        }elseif(isset($_POST["statementsToDelete"]) && strlen($_POST["statementsToDelete"])>0 && is_numeric($_POST["statementsToDelete"])) {
            $idToDelete = filter_var($_POST["statementsToDelete"],FILTER_SANITIZE_NUMBER_INT);
            $idToDelete = mysqli_real_escape_string($dbLink, $idToDelete);

            $sql = mysqli_query($dbLink, "SELECT `theme_id` FROM `lesson` WHERE `statement_id` = '".$idToDelete."' ORDER BY `theme_id`");
            while($row = mysqli_fetch_assoc($sql)){
                $sqlq = mysqli_query($dbLink, "DELETE FROM `theme` WHERE `id_theme` = ".$row['theme_id']);
            }

            $sql = mysqli_query($dbLink, "SELECT `id_lesson` FROM `lesson` WHERE `statement_id` = '".$idToDelete."' ORDER BY `id_lesson`");
            while($row = mysqli_fetch_assoc($sql)){
                $sqlq = mysqli_query($dbLink, "DELETE FROM `record` WHERE `lesson_id` = ".$row['id_lesson']);
            }

            $sql = mysqli_query($dbLink, "DELETE FROM `lesson` WHERE `statement_id` = '".$idToDelete."'");

            $sqlQuery = "DELETE FROM `statement` WHERE `id_statement` = ".$idToDelete;
            if(!mysqli_query($dbLink, $sqlQuery))
            {
                header('HTTP/1.1 500 Could not delete record!');
                exit();
            }
            mysqli_close($dbLink);

        }else{
            header('HTTP/1.1 500 Error occurred, Could not process request!');
            exit();
        }
#########
    }else{ 
        echo 'Вы не авторизованы<br>';
        echo '<a href="login.php">Авторизация</a>';
    }
}else{ 
    echo 'Вы не авторизованы<br>';
    echo '<a href="login.php">Авторизация</a>';
}
?>