<?php
session_start();
if ( ! isset($_SESSION['name']) ) {
	die('ACCESS DENIED');
}
if (isset($_POST['cancel'])) {
  header('Location: index.php');
  return;
}
$status = false;
if (isset($_SESSION['status'])) {
  $status = htmlentities($_SESSION['status']);
  $status_color = htmlentities($_SESSION['color']);
  unset($_SESSION['status']);
  unset($_SESSION['color']);
}
try {
  $pdo = new PDO("mysql:host=localhost;dbname=misc",'root','shrey');
  $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
} 
catch (PDO_Exception $e) {
  echo "Connection failed: ".$e->getMEssage();
  die();
}
$name = htmlentities($_SESSION['name']);
$_SESSION['color'] = 'red';

if ( isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) && isset($_POST['mileage']) ) {
  if ( strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 || strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1 ) {
    $_SESSION['status'] = "All fields are required";
    header('Location: add.php');
    return;
  }
  if ( !is_numeric($_POST['year']) || !is_numeric($_POST['mileage'])) {
    $_SESSION['status'] = "must be an integer";
    header('Location: add.php');
    return;
  }
  $make = htmlentities($_POST['make']);
  $model = htmlentities($_POST['model']);
  $year = htmlentities($_POST['year']);
  $mileage = htmlentities($_POST['mileage']);
  $stmt = $pdo->prepare("INSERT INTO autos (make, model, year, mileage) VALUES (:make,:model,:year,:mileage)");
  $stmt->execute([
    ':make' => $make,
    ':model' => $model,
    ':year' => $year,
    ':mileage' => $mileage,
  ]);
  $_SESSION['status'] = "Record added";
  $_SESSION['color'] = 'green';
  header('Location: index.php');
  return;
}
?>




<!DOCTYPE html>
<html>
<head>
<title>Shrey Trivedi's Automobile Tracker</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>
<div class="container">
  <h1>Tracking Automobiles for <?php echo $name; ?></h1>
  <?php 
    if ($status !== false) {
      echo('<p style="color: '.$status_color.';">'.htmlentities($status)."</p> \n");
    }
  ?>
  <form method="post">
    <p>Make:
    <input type="text" name="make" size="40"/></p>
    <p>Model:
    <input type="text" name="model" size="40"/></p>
    <p>Year:
    <input type="text" name="year" size="10"/></p>
    <p>Mileage:
    <input type="text" name="mileage" size="10"/></p>

    <input type="submit" name='add' value="Add">
    <input type="submit" name="cancel" value="Cancel">
  </form>
  <p>
</div>
<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script></body>
</html>
