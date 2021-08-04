<?php
require_once "db.php";

function generateCode($length = 6)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
        $code .= $chars[mt_rand(0, $clen)];
    }
    return $code;
}

if (isset($_POST['submit'])) {
    $login = mysqli_real_escape_string($dbLink, $_POST['login']);
    $password = mysqli_real_escape_string($dbLink, $_POST['password']);

    $query = mysqli_query($dbLink, "SELECT `id_user`, `hash_user`, `password_user` FROM `user` WHERE `login_user` = '" . $login . "' LIMIT 1");
    $data = mysqli_fetch_assoc($query);

    $query = mysqli_query($dbLink, "SELECT `id_student`, `hash_student`, `password_student` FROM `student` WHERE `login_student` = '" . $login . "' LIMIT 1");
    $student = mysqli_fetch_assoc($query);

    if ($data['password_user'] === md5(md5($password))) {
        $hash = md5(generateCode(10));

        mysqli_query($dbLink, "UPDATE `user` SET `hash_user` = '" . $hash . "' WHERE `id_user` = '" . $data['id_user'] . "'");

        setcookie("id", $data['id_user'], time() + 3600 * 24, "/");
        setcookie("hash", $hash, time() + 3600 * 24, "/", null, null, true);
        header("Location: check.php");
        exit();
    } elseif ($student['password_student'] === $password) {
        $hash = md5(generateCode(10));

        mysqli_query($dbLink, "UPDATE `student` SET `hash_student` = '" . $hash . "' WHERE `id_student` = '" . $student['id_student'] . "'");

        setcookie("idS", $student['id_student'], time() + 3600 * 24, "/");
        setcookie("hashS", $hash, time() + 3600 * 24, "/", null, null, true);
        header("Location: check.php");
        exit();
    } else {
        print "Неправильный логин или пароль";
    }
}
?>

<link rel="stylesheet" type="text/css" href="st.css">
<div class="log">
    <form method="POST">
        Логин <input name="login" type="text" required><br>
        Пароль <input name="password" type="password" required><br>
        <input name="submit" type="submit" value="Войти">
    </form>
</div>