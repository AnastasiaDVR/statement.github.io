<!DOCTYPE html>
<html>
<head>
  <title>Группы</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="stylish.css">
  <script src="http://code.jquery.com/jquery-latest.js"></script>
  <script type="text/javascript" src="../js/test.js"></script>
</head>

<body>

<script>
$(document).ready(function() {
    $("#saveGroup").click(function (e) {
        e.preventDefault();
        if($("#nameGroup").val()==="")
        {
            alert("Введите текст!");
            return false;
        }

        var myData = "name_group="+ $("#nameGroup").val();

        jQuery.ajax({
            type: "POST",
            url: "../response.php",
            dataType:"text",
            data:myData,
            success:function(response){
            $("#groups").append(response);
            $("#nameGroup").val('');
            },
            error:function (xhr, ajaxOptions, thrownError){
                alert(thrownError);
            }
        });
    });

    $("body").on("click", "#groups .del_button", function(e) {
        e.preventDefault();
        var clickedID = this.id.split("-");
        var DbNumberID = clickedID[1];
        var myData = 'groupsToDelete='+ DbNumberID;

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
  $sql = mysqli_query($dbLink,"SELECT `hash_user` FROM `user` WHERE `id_user` = '".$id."'");
  $data = mysqli_fetch_assoc($sql);

  if ($_COOKIE['hash'] == $data['hash_user']){
?>

<form action="../" method="post">
  <button>Назад</button>
</form>

<dl id="groups">
	<dt>Группы: </dt>
		<?php
		$Result = mysqli_query($dbLink, "SELECT * FROM `groups` ORDER BY `name_group`");
		while($row = mysqli_fetch_array($Result)) {
            echo '<dd id="item_'.$row["id_group"].'" class="del_wrapper">'.$row["name_group"]." ".'<a href="#" class="del_button" id="del-'.$row["id_group"].'"><img src="../css/cancel.gif" border="0"></a></dd>';
        }
		?>
</dl>	
    <input onkeypress="return filter_input(event,/\d/)" maxlength="3" id="nameGroup" name="name_group" type="text" placeholder="Номер группы" required>
	<button id="saveGroup" class="clicker">Добавить</button>

<br><br>
<div id="groups" class="change">
    <table>
        <thead>
            <tr>
                <th>Название группы</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT `id_group`, `name_group` FROM `groups` ORDER BY `name_group`";
            $Result = mysqli_query($dbLink, $sql);
            while ($row = mysqli_fetch_array($Result)) {
                echo '<tr>';
                echo '<td onkeypress="return filter_td(event,/\d/i)" id="name_group-id_group-'.$row['id_group'].'" contenteditable="true">'.$row['name_group'].'</td>';
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