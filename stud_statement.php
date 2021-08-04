<?php
require_once("db.php");

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') { 

	$statement = mysqli_real_escape_string($dbLink, $_POST["statement"]);
	$group = mysqli_real_escape_string($dbLink, $_POST["group"]);
	$idS = mysqli_real_escape_string($dbLink, $_COOKIE['idS']);

	$Result = "SELECT `id_statement`,`date_open`,DATE_FORMAT(date_open, '%d.%m.%Y') as date_open, DATE_FORMAT(date_close, '%d.%m.%Y') as date_close,`discipline_id`,`name_discipline`,`name_group`,`surname_user`,`name_user`,`patronymic_user` FROM `statement` INNER JOIN `groups` ON `group_id` = `id_group` INNER JOIN `discipline` ON `discipline_id` = `id_discipline` INNER JOIN `user` ON `user_id` = `id_user` WHERE `id_statement` = '".$statement."'";

	if (mysqli_query($dbLink, $Result)) {
		$Result = mysqli_query($dbLink, $Result);
		$row = mysqli_fetch_assoc($Result);
		$id_statement = $row["id_statement"];
		$date_open = $row["date_open"];
		$date_close = $row["date_close"];
		$discipline_id = $row["discipline_id"];
		$name_discipline = $row["name_discipline"];
		$name_group = $row["name_group"];
		$teacher = $row["surname_user"]." ".$row["name_user"]." ".$row["patronymic_user"];
		if (isset($id_statement) && strlen($id_statement)>0) { 
			echo '<br><div class="label">Табель за <b>'.$date_open.' - '.$date_close.'</b> группа <b>'.$name_group.'</b> преподователь: <b>'.$teacher.'</b><br>';
			echo 'учебная дисциплина: <b>'.$name_discipline.'</b></div><br>';

		
		echo '<table id="statementTable_'.$row['id_statement'].'" border="1">'; ?>
			<thead>
		      <tr>
		        <th scope="colgroup" rowspan="2" colspan="2">Фамилия Имя</th>
		        <th scope="colgroup" colspan="26">ДАТЫ ПРОВЕДЕНИЯ ЗАНЯТИЙ</th>
		      </tr>
		    
		      <tr>
		      <?php
		      	$id_lesson = array();
		      	$Result = mysqli_query($dbLink, "SELECT * FROM `lesson` WHERE `statement_id` = '".$id_statement."' ORDER BY `id_lesson`");
		      	while ($row = mysqli_fetch_assoc($Result)) { 
		      		echo '<th id="date_lesson-id_lesson-'.$row['id_lesson'].'" class="edit date" scope="col" colspan="2">'.$row['date_lesson'].'</th>';
		      	}
		      ?>
		     </tr>
		    </thead>
		    <tbody>
		        <?php
		        $Result = mysqli_query($dbLink, "SELECT `id_student`,`surname_student`, `name_student` FROM `student` WHERE `group_id` = '".$group."' AND `id_student` = '".$idS."'");
		        while ($row = mysqli_fetch_assoc($Result)) { 
		        	$id_student = $row['id_student'];
		        	?>
			        <tr>
				        <td scope="row"><?php echo $row['surname_student']?></td>
				        <td scope="row"><?php echo $row['name_student']?></td>
			            <?php 
			            $sqlResult = mysqli_query($dbLink, "SELECT `id_lesson`, `date_lesson`, `control1`, `control2`, `statement_id`, `theme_id` FROM `lesson` WHERE `statement_id` = '".$id_statement."' ORDER BY `id_lesson`");		            
			            while ($row = mysqli_fetch_assoc($sqlResult)) {
			            	$id_lesson = $row['id_lesson'];

			            	$sql = mysqli_query($dbLink, "SELECT * FROM `record` WHERE `lesson_id` = '".$id_lesson."' AND `student_id` = '".$id_student."' ORDER BY `id_record`");
			            	$row = mysqli_fetch_assoc($sql);
			           		if (isset($row['id_record']) && strlen($row['id_record'])>0) {
			           			
		           				echo '<td onkeypress="if(this.value.length&gt;2) return false;" id="mark1-id_record-'.$row['id_record'].'" class="edit mark1" width="2">'.$row['mark1'].'</td>';
			            		echo '<td maxlength="2" id="mark2-id_record-'.$row['id_record'].'" class="edit mark2" width="2">'.$row['mark2'].'</td>';
				       			
				        	}
			           	}
		    	} 
		    	?>
		        </tr>

		        <tr>
		        	<td colspan="2">Контролирующие мероприятия</td>
		        	<?php 
			            $Result = mysqli_query($dbLink, "SELECT * FROM `lesson` INNER JOIN `theme` ON `theme_id` = `id_theme` WHERE `statement_id` = '".$id_statement."' ORDER BY `id_lesson`");
			           	while ($row = mysqli_fetch_assoc($Result)) { 
			           		echo '<td id="control1-id_lesson-'.$row['id_lesson'].'" class="edit control1" width="2">'.$row['control1'].'</td>';
			           		echo '<td id="control2-id_lesson-'.$row['id_lesson'].'" class="edit control2" width="2">'.$row['control2'].'</td>';
			           		 
			           	} ?>
		        </tr>
		    </tbody>	   
		</table>
		
	<br>
		<table id="hometask" border="1" style="float: left;">
			<thead>
				<th scope="col" rowspan="2">дата занятия</th>
		        <th scope="col" rowspan="2">КРАТКОЕ СОДЕРЖАНИЕ ЗАНЯТИЯ,<br> ДОМАШНЕЕ ЗАДАНИЕ</th>
			</thead>
			<tbody>
				<tr>			
			<?php
				$Result = mysqli_query($dbLink, "SELECT * FROM `lesson` INNER JOIN `theme` ON `theme_id` = `id_theme` WHERE `statement_id` = '".$id_statement."' ORDER BY `id_theme`");
			   	while ($row = mysqli_fetch_assoc($Result)) { 
			   		if ($row['date_lesson'] !== "") {
			   			echo '<td>'.$row['date_lesson']."</td>";
		           		echo '<td id="name_theme-id_theme-'.$row['id_theme'].'" class="edit theme" width="2">'.$row['name_theme'].'</td></tr>';
			   		}
			   	}
			?>		
			</tbody>
		</table>
		
	<?php
		}
	}else{
		header('HTTP/1.1 500 No data');
		exit();
	}
}
?>