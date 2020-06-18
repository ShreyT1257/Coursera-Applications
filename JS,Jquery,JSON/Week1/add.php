<?php
session_start();
if ( ! isset($_SESSION['name']) ) {
	die('Not logged in');
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
catch (PDOException $e) {
  echo "Connection failed: ".$e->getMEssage();
  die();
}
$name = htmlentities($_SESSION['name']);
$_SESSION['color'] = 'red';
if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {
  if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
    $_SESSION['status'] = "All fields are required";
    header('Location: add.php');
    return;
  }
  $stmt = $pdo->prepare('INSERT INTO profile(user_id,first_name,last_name,email,headline,summary) 
  VALUES(:user_id,:first_name,:last_name,:email,:headline,:summary)');
  $stmt->execute([
      ':user_id' => $_SESSION['user_id'],
      ':first_name' => $_POST['first_name'],
      ':last_name' => $_POST['last_name'],
      ':email' => $_POST['email'],
      ':headline' => $_POST['headline'],
      ':summary' => $_POST['summary']
    ]);
  $_SESSION['status'] = "Record Added";
  $_SESSION['color'] = "green";
  header("Location: index.php");
  return;
} 
?>




<!DOCTYPE html>
<html>
<head>
<title>Shrey Trivedi's Profile Add</title>
<!-- bootstrap.php - this is HTML -->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>
<div class="container">
  <h1>Adding Profile for <?php echo $name; ?></h1>
  <?php
    if ($status !== false) {
      echo('<p style ="color: '.$status_color.';">'.htmlentities($status)."</p> \n");
    }
  ?>
  <form method="post">
    <p>First Name:
    <input type="text" name="first_name" size="60"/></p>
    <p>Last Name:
    <input type="text" name="last_name" size="60"/></p>
    <p>Email:
    <input type="text" name="email" size="30"/></p>
    <p>Headline:<br/>
    <input type="text" name="headline" size="80"/></p>
    <p>Summary:<br/>
    <textarea name="summary" rows="8" cols="80"></textarea>
    <p>
    <input type="submit" value="Add">
    <input type="submit" name="cancel" value="Cancel">
    </p>
  </form>
</div>
</body>
</html>