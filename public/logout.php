<?php
setcookie("id", "", time() - 3600 * 24, "/");
setcookie("hash", "", time() - 3600 * 24, "/", null, null, true);
setcookie("idS", "", time() - 3600 * 24, "/");
setcookie("hashS", "", time() - 3600 * 24, "/", null, null, true);

header("Location: index.php");
exit;

?>
<form method="POST">
    Логин <input name="login" type="text" required><br>
    Пароль <input name="password" type="password" required><br>
    <input name="submit" type="submit" value="Войти">
</form>