<?php
session_start();
if( !isset($_SESSION['name']) ) {
    die("Not logged in");
}
try {
    $pdo = new PDO("mysql:host=localhost;dbname=misc","root","shrey");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: ".$e->getMessage();
    die();
}
if (isset($_REQUEST['profile_id'])) {
    $profile_id = htmlentities($_REQUEST['profile_id']);
    $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :profile_id");
    $stmt->execute([':profile_id' => $profile_id,]);
    $profile = $stmt->fetch(PDO::FETCH_OBJ);
}
?>





<!DOCTYPE html>
<html>
<head>
<title>Shrey Trivedi's Resume Registry</title>
<!-- bootstrap.php - this is HTML
	 Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <h1>Profile Information</h1>
    <p>
      First Name:
      <?php echo $profile->first_name; ?> 
    </p>
    <p>
      Last Name:
      <?php echo $profile->last_name; ?>
    </p>
    <p>
      Email: 
      <?php echo $profile->email; ?>
    </p>
    <p>
      Headline: 
      <?php echo $profile->headline; ?>
    </p>
    <p>
      Summary: 
      <br>
      <?php echo $profile->summary; ?>
    </p>
    <p><a href="index.php">Done</a></p>
</div>
</body>
</html>