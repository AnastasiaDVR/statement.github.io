<!DOCTYPE html>
<html>
<head>
  <title>Табель</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="stylish.css">
  <script src="http://code.jquery.com/jquery-latest.js"></script>
  <script type="text/javascript" src="../js/test.js"></script>
  <script type="text/javascript" src="../js/update.js"></script>
</head>

<body>

<script>
$(document).ready(function() {
    $("#saveStatement").click(function (e) {
        e.preventDefault();
        if(($("#dateOpen").val()==="") || ($("#dateClose").val()==="") || ($("#disciplineStatement option:selected").val()==="") || ($("#groupStatement option:selected").val()===""))
        {
            alert("Заполните все поля и проверьте корректность введённых данных!");
            return false;
        }

        var myData = {"date_open":$("#dateOpen").val(), "date_close":$("#dateClose").val(), "disciplineStatementList":$("#disciplineStatement").val(), "groupStatementList":$("#groupStatement").val()};
        jQuery.ajax({
            type: "POST",
            url: "../response.php",
            dataType:"text",
            data:myData,
            success:function(response){
            $("#statements").append(response);
            $("#dateOpen").val('');
            $("#dateClose").val('');
            },
            error:function (xhr, ajaxOptions, thrownError){
                alert(thrownError);
            }
        });
    });

    $("body").on("click", "#statements .del_button", function(e) {
        e.preventDefault();
        var clickedID = this.id.split("-");
        var DbNumberID = clickedID[1];
        var myData = 'statementsToDelete='+ DbNumberID;

        jQuery.ajax({
            type: "POST",
            url: "../response.php",
            dataType:"text",
            data:myData,
            success:function(response){
            $('#item_'+DbNumberID).fadeOut("slow");
            },
            error:function (xhr, ajaxOptions, thrownError){
                alert(thrownError);
            }
        });
    });
});
</script>

<?php
include_once "../db.php";

if (isset($_COOKIE['id'])) { 
  $id = mysqli_real_escape_string($dbLink, $_COOKIE['id']);
  $sql = mysqli_query($dbLink,"SELECT `hash_user`, `role_id` FROM `user` WHERE `id_user` = '".$id."'");
  $data = mysqli_fetch_assoc($sql);

  if ($_COOKIE['hash'] == $data['hash_user']){
?>

<form action="../" method="post">
  <button>Назад</button>
</form>

<dl id="statements">
    <dt>Табель: </dt>
        <?php
        if($data['role_id'] == '1' or $data['role_id'] == '2') {
            $sql = "SELECT *, DATE_FORMAT(date_open, '%d.%m.%Y') as date_open, DATE_FORMAT(date_close, '%d.%m.%Y') as date_close FROM `statement` INNER JOIN `discipline` ON `discipline_id` = `id_discipline` INNER JOIN `groups` ON `group_id` = `id_group` ORDER BY `name_discipline`, `name_group`, `date_open`";
        }else{
            $sql = "SELECT *, DATE_FORMAT(date_open, '%d.%m.%Y') as date_open, DATE_FORMAT(date_close, '%d.%m.%Y') as date_close FROM `statement` INNER JOIN `discipline` ON `discipline_id` = `id_discipline` INNER JOIN `groups` ON `group_id` = `id_group` INNER JOIN `user` ON `user_id` = `id_user` WHERE `user_id` = '".$id."' ORDER BY `name_discipline`, `name_group`, `date_open`";
        }
        $Result = mysqli_query($dbLink, $sql);

        while($row = mysqli_fetch_array($Result))
        {echo '<dd id="item_'.$row["id_statement"].'" class="del_wrapper">'."(".$row["date_open"]." - ".$row["date_close"].") ".$row["name_discipline"]." ".$row["name_group"]." ".'<a href="#" class="del_button" id="del-'.$row["id_statement"].'"><img src="../css/cancel.gif" border="0"></a></dd>';}
        ?>
</dl>    
    табель с <input id="dateOpen" type="date"> до <input id="dateClose" type="date">
    учебная дисциплина <select id="disciplineStatement" name = "disciplineStatementList">
                        <option value="">---</option>
                        <?php
                        if($data['role_id'] == '1' or $data['role_id'] == '2') {
                            $Result = mysqli_query($dbLink, "SELECT * FROM `discipline` ORDER BY `name_discipline`");
                        }else{
                            $Result = mysqli_query($dbLink, "SELECT * FROM `discipline` INNER JOIN `user` ON `user_id` = `id_user` WHERE `user_id` = '".$id."' ORDER BY `name_discipline`");
                        }
                            while ($row = mysqli_fetch_assoc($Result)) {
                                echo '<option value="'.$row['id_discipline'].'">'.$row['name_discipline'].'</option>';
                            }
                        ?>
                    </select>

    группа <select id="groupStatement" name = "groupStatementList">
            <option value="">---</option>
            <?php
                $Result = mysqli_query($dbLink, "SELECT * FROM `groups` ORDER BY `name_group`");
                while ($row = mysqli_fetch_assoc($Result)) {
                    echo '<option value="'.$row['id_group'].'">'.$row['name_group'].'</option>';
                }
            ?>
        </select>
    <button id="saveStatement" name="Statement" class="clicker">Добавить</button>

    

<br><br>
<div id="statement" class="change">
    <table>
        <thead>
            <tr>
                <th>Дата начала</th>
                <th>Дата окончания</th>
                <th>Учебная дисциплина</th>
                <th>Группа</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($data['role_id'] == '1' or $data['role_id'] == '2') {
                $sql = "SELECT *, DATE_FORMAT(date_open, '%d.%m.%Y') as date_open, DATE_FORMAT(date_close, '%d.%m.%Y') as date_close FROM `statement` INNER JOIN `discipline` ON `discipline_id` = `id_discipline` INNER JOIN `groups` ON `group_id` = `id_group` ORDER BY `name_discipline`, `name_group`, `date_open`";
            }else{
                $sql = "SELECT *, DATE_FORMAT(date_open, '%d.%m.%Y') as date_open, DATE_FORMAT(date_close, '%d.%m.%Y') as date_close FROM `statement` INNER JOIN `discipline` ON `discipline_id` = `id_discipline` INNER JOIN `groups` ON `group_id` = `id_group` INNER JOIN `user` ON `user_id` = `id_user` WHERE `user_id` = '".$id."' ORDER BY `name_discipline`, `name_group`, `date_open`";
            }
            $Result = mysqli_query($dbLink, $sql);
            while ($row = mysqli_fetch_array($Result)) {
                echo '<tr>';
                echo '<td id="date_open-id_statement-'.$row['id_statement'].'" contenteditable="true">'.$row['date_open'].'</td>';
                echo '<td id="date_close-id_statement-'.$row['id_statement'].'" contenteditable="true">'.$row['date_close'].'</td>';

                echo '<td><select id="discipline_id-id_statement-'.$row['id_statement'].'" contenteditable="true">';
                $Query = mysqli_query($dbLink, "SELECT * FROM `discipline` INNER JOIN `statement` ON `id_discipline` = `discipline_id` WHERE `id_statement` = '".$row['id_statement']."'");
                $group = mysqli_fetch_assoc($Query);
                if (isset($group['id_discipline']) && strlen($group['id_discipline'])>0) {
                    echo '<option value="'.$group['id_discipline'].'">'.$group['name_discipline'].'</option>';
                }
                echo '<option value="">---</option>';

                if($data['role_id'] == '1' or $data['role_id'] == '2') {
                    $Query = mysqli_query($dbLink, "SELECT * FROM `discipline` GROUP BY `name_discipline` ORDER BY `name_discipline`");
                }else{
                    $Query = mysqli_query($dbLink, "SELECT * FROM `discipline` INNER JOIN `user` ON `user_id` = `id_user` WHERE `user_id` = '".$id."'  GROUP BY `name_discipline` ORDER BY `name_discipline`");
                }
                while ($option = mysqli_fetch_assoc($Query)) {
                    echo '<option value="'.$option['id_discipline'].'">'.$option['name_discipline'].'</option>';
                }
                echo '</select></td>';


                echo '<td><select id="group_id-id_statement-'.$row['id_statement'].'" contenteditable="true">';
                $Query = mysqli_query($dbLink, "SELECT * FROM `groups` INNER JOIN `statement` ON `id_group` = `group_id` WHERE `id_statement` = '".$row['id_statement']."'");
                $group = mysqli_fetch_assoc($Query);
                if (isset($group['id_group']) && strlen($group['id_group'])>0) {
                    echo '<option value="'.$group['id_group'].'">'.$group['name_group'].'</option>';
                }
                echo '<option value="">---</option>';

                $Query = mysqli_query($dbLink, "SELECT * FROM `groups` GROUP BY `name_group` ORDER BY `name_group`");
                while ($option = mysqli_fetch_assoc($Query)) {
                    echo '<option value="'.$option['id_group'].'">'.$option['name_group'].'</option>';
                }
                echo '</select></td>';
                
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
    <button id="save" class="clicker">Сохранить</button>
</div>

<?php
    }else{ 
    echo 'Вы не авторизованы<br>';
    echo '<a href="../login.php">Авторизация</a>';
    } 
}else{ 
    echo 'Вы не авторизованы<br>';
    echo '<a href="../login.php">Авторизация</a>';
} 
?>

<script type="text/javascript">
var contentold={};

function savedata(elementidsave,contentsave) {  

    $.ajax({
        url: '../save.php',
        type: 'POST',
        data: {
            content: contentsave,
            id:elementidsave
            },
        });
}
      
$(document).ready(function() {
    $('[contenteditable="true"]')
        .mousedown(function (e)
              {
                e.stopPropagation();
                elementid=this.id;
                contentold[elementid]=$(this).html();
                $(this).bind('keydown', function(e) {
                    if(e.keyCode==27){
                         e.preventDefault();
                         $(this).html(contentold[elementid]);
                    }
                });
                $("#save").show();
              })
          .blur(function (event)
              {
                  var elementidsave=this.id;
                  var  cont = $(this).val();
                  if (cont != "") {
                    var  contentsave = $(this).val(); 
                  }else{
                    var  contentsave = $(this).html(); 
                  }
                  event.stopImmediatePropagation();
                  if (contentsave!=contentold[elementidsave])
                        {    
                            savedata(elementidsave,contentsave);
                            }
                });     
});
</script>

</body>
</html>