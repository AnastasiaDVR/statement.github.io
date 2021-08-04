<?php 
include("db.php");

if (isset($_COOKIE['id'])) { 
  $sql = mysqli_query($dbLink, "SELECT `hash_user` FROM `user` WHERE `id_user` = '".mysqli_real_escape_string($dbLink, $_COOKIE['id'])."'");
  $data = mysqli_fetch_assoc($sql);

  if ($_COOKIE['hash'] == $data['hash_user']){


      $id = filter_input (INPUT_POST ,  'id' , FILTER_SANITIZE_STRING );
      $id = explode('-', $id);

      $name_column = mysqli_real_escape_string($dbLink, $id[0]); //что обновлять
      $id_row = mysqli_real_escape_string($dbLink, $id[1]); //где обновлять
      $id_string = mysqli_real_escape_string($dbLink, $id[2]); //id строки

      $t = explode('_', $id_row);
      $table = mysqli_real_escape_string($dbLink, $t[1]); // имя таблицы
      if ($table == "group") {
            $table.='s';
      }
      
      $content = $_POST['content'];
	  $content = mysqli_real_escape_string($dbLink, $content);

      if (preg_match("/^[a-zа-яёA-ZА-ЯЁ0-9. ]+$/u", $content)){
      	if ($name_column == "date_open" or $name_column == "date_close") {
      		if (!preg_match("/\d{2}.\d{2}.\d{4}/", $content) && !preg_match("/^[0-9.]+$/u", $content)) {
      			header('HTTP/1.1 500 Data is incorrect');
        		exit();	
      		}else{
      			$sql = mysqli_query($dbLink, "SELECT DATE_FORMAT(date_open, '%d.%m.%Y') as date_open, DATE_FORMAT(date_close, '%d.%m.%Y') as date_close FROM `".$table."` WHERE `".$id_row."` = '".$id_string."'");
              	$row = mysqli_fetch_assoc($sql);

              	$dataO = preg_replace('/[^0-9\.]/u', '', trim($row['date_open']));
			    $dataO = explode('.', $dataO);
			    $dataC = preg_replace('/[^0-9\.]/u', '', trim($row['date_close']));
			    $dataC = explode('.', $dataC);

			    $content = preg_replace('/[^0-9\.]/u', '', trim($content));
				$content = explode('.', $content);

				$checkDate = checkdate($content[1], $content[0], $content[2]);

				if ($checkDate) {
					if ($name_column == "date_open") {
	              		$day = ($content < $dataC);
	              		if (!$day) {
	              			header('HTTP/1.1 500 Data of open is incorrect');
	        				exit();	
	              		}else{
	              			$year = $dataC[2] - $content[2];
	              			if ($year > 1) {
	              				header('HTTP/1.1 500 Year is incorrect');
	        					exit();	
	              			}else{
	              				$content = $content[2].$content[1].$content[0];
	              			}
	              		}
	              	}elseif ($name_column == "date_close") {
	              		$day = ($content > $dataO);
	              		if (!$day) {
	              			header('HTTP/1.1 500 Data of open is incorrect');
	        				exit();	
	              		}else{
	              			$year = $content[2] - $dataO[2];
	              			if ($year > 1) {
	              				header('HTTP/1.1 500 Year is incorrect');
	        					exit();	
	              			}else{
	              				$content = $content[2].$content[1].$content[0];
	              			}
	              		}
	              	}
				}else{
					header('HTTP/1.1 500 Data is incorrect');
	        		exit();	
	        	}
      		}
      	}

      	if ($name_column == "date_lesson") {
      		if (!preg_match("/\d{2}.\d{2}/", $content) && !preg_match("/^[0-9.]+$/u", $content)) {
      			header('HTTP/1.1 500 Data is incorrect');
        		exit();	
      		}
      	}

      	if ($name_column == "mark1" or $name_column == "mark2") {
      		if (!preg_match("/^[0-9]|[н]+$/u", $content)) {
      			header('HTTP/1.1 500 Data is incorrect');
        		exit();	
      		}
      	}

        if ($name_column == "password_user") {
              $content = md5(md5($content));
        }else{
              $sql = mysqli_query($dbLink, "SELECT `".$name_column."` FROM `".$table."` WHERE `".$name_column."` = '".$content."'");
              $row = mysqli_fetch_assoc($sql);
        }

        $test_column = explode('_', $name_column);
        $test_columnID = mysqli_real_escape_string($dbLink, $test_column[1]); 
        $test_columnPass = mysqli_real_escape_string($dbLink, $test_column[0]);

        if(isset($row[$name_column]) && strlen($row[$name_column])>0 && $test_columnID !== "id" && $test_columnPass !== 'password' && $name_column !== 'mark1' && $name_column !== 'mark2' && $name_column !== 'date_lesson' && $name_column !== 'control1' && $name_column !== 'control2' && $name_column !== 'name_theme' && $name_column !== "date_open" && $name_column !== "date_close"){
              header('HTTP/1.1 500 Record already exists');
              exit();
        }else{
              $sql = mysqli_query($dbLink, "UPDATE `".$table."` SET `".$name_column."` = '".$content."' WHERE `".$id_row."` = '".$id_string."'");
        }
      }else{
        header('HTTP/1.1 500 Data is incorrect');
        exit();
      }
	
      
  }else{ 
      echo 'Вы не авторизованы<br>';
      echo '<a href="login.php">Авторизация</a>';
      } 
}else{ 
    echo 'Вы не авторизованы<br>';
    echo '<a href="login.php">Авторизация</a>';
}
?>