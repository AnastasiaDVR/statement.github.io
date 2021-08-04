<?php
include_once "db.php";
?>

<form action="./" method="post">
  <button>Назад</button>
</form>

<form method="post">
с <input type="date" name="dateOpen"> до <input type="date" name="dateClose">
<input type="submit" name="subbut" value="Отправить">
</form>

<?php
if (isset($_POST["subbut"])) {
  $test_dataOpen = preg_replace('/[^0-9\.]/u', '', trim($_POST['dateOpen']));
  $test_dataOpen_ar = explode('.', $test_dataOpen);

  $test_dataClose = preg_replace('/[^0-9\.]/u', '', trim($_POST['dateClose']));
  $test_dataClose_ar = explode('.', $test_dataClose);

$dateTest=($test_dataOpen_ar > $test_dataClose_ar);

  if ($dateTest) {
    echo "<br>".$_POST['dateOpen']." > ".$_POST['dateClose'];
  }
}
?>