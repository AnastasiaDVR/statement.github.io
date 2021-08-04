<?php
require_once "db.php";

if (isset($_COOKIE['id']) or isset($_COOKIE['idS'])) {
    $id = mysqli_real_escape_string($dbLink, $_COOKIE['id'] ?? null); // user
    $idS = mysqli_real_escape_string($dbLink, $_COOKIE['idS'] ?? null); // student

    $query = mysqli_query($dbLink, "SELECT * FROM `user` WHERE `id_user` = '" . intval($id) . "' LIMIT 1");
    $userdata = mysqli_fetch_assoc($query);

    $query = mysqli_query($dbLink, "SELECT * FROM `student` WHERE `id_student` = '" . intval($idS) . "' LIMIT 1");
    $studentdata = mysqli_fetch_assoc($query);

    $coockieHash = $_COOKIE['hash'] ?? null;
    $coockieHashS = $_COOKIE['hashS'] ?? null;
    if (
        ($userdata['hash_user'] !== $coockieHash)
        or ($userdata['id_user'] !== $id) and ($studentdata['id_student'] !== $idS)
        or ($studentdata['hash_student'] !== $coockieHashS)
    ) {
        setcookie("id", "", time() - 3600 * 24, "/");
        setcookie("hash", "", time() - 3600 * 24, "/", null, null, true);
        setcookie("idS", "", time() - 3600 * 24, "/");
        setcookie("hashS", "", time() - 3600 * 24, "/", null, null, true);
        echo "Ошибка в id";
    } else {
        header("Location: index.php");
        exit();
    }
} else {
    echo "Время ожидания истекло";
}
