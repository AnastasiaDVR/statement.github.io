<?php
require_once("db.php");

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

if (isset($_COOKIE['id'])) { 
  $id = mysqli_real_escape_string($dbLink, $_COOKIE['id']);
  $sql = mysqli_query($dbLink,"SELECT `hash_user`, `role_id` FROM `user` WHERE `id_user` = '".$id."'");
  $data = mysqli_fetch_assoc($sql);

  if ($_COOKIE['hash'] == $data['hash_user']){

	$group = mysqli_real_escape_string($dbLink, $_POST["group"]);

	if($data['role_id'] == '1') {
        $result = "SELECT * FROM `statement`INNER JOIN `discipline` ON `discipline_id` = `id_discipline` WHERE `group_id` = '".$group."' GROUP BY `id_discipline`";
    }else{ 
		$result = "SELECT * FROM `statement`INNER JOIN `discipline` ON `discipline_id` = `id_discipline` INNER JOIN `user` ON `user_id` = `id_user` WHERE `group_id` = '".$group."' AND `id_user` = '".$id."' GROUP BY `id_discipline`";
	}

	if (mysqli_query($dbLink, $result)) {
		$result = mysqli_query($dbLink, $result);
	    echo"<option value=''>---</option>";

		while ($row = mysqli_fetch_array($result))
		{ echo "<option value='".$row["id_discipline"]."'>".$row["name_discipline"]."</option>"; }
	}else{
		header('HTTP/1.1 500 No data');
		exit();
	}

	}else{ 
    echo 'Вы не авторизованы<br>';
    echo '<a href="../login.php">Авторизация</a>';
    } 
}elseif (isset($_COOKIE['idS'])) {
	$idS = mysqli_real_escape_string($dbLink, $_COOKIE['idS']);
    $sql = mysqli_query($dbLink,"SELECT `hash_student` FROM `student` WHERE `id_student` = '".$idS."'");
    $data = mysqli_fetch_assoc($sql);

 	if ($_COOKIE['hashS'] == $data['hash_student']){

		$group = mysqli_real_escape_string($dbLink, $_POST["group"]);

		$result = "SELECT * FROM `statement`INNER JOIN `discipline` ON `discipline_id` = `id_discipline` WHERE `group_id` = '".$group."' GROUP BY `id_discipline`";

		if (mysqli_query($dbLink, $result)) {
			$result = mysqli_query($dbLink, $result);
		    echo"<option value=''>---</option>";

			while ($row = mysqli_fetch_array($result))
			{ echo "<option value='".$row["id_discipline"]."'>".$row["name_discipline"]."</option>"; }
		}else{
			header('HTTP/1.1 500 No data');
			exit();
		}

	}else{ 
    echo 'Вы не авторизованы<br>';
    echo '<a href="../login.php">Авторизация</a>';
    } 
}else{ 
    echo 'Вы не авторизованы<br>';
    echo '<a href="../login.php">Авторизация</a>';
} 

}
?>