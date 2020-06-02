<?php
session_start();
if ( ! isset($_SESSION['name']) ) {
	die("ACCESS DENIED");
}
try {
    $pdo = new PDO("mysql:host=localhost;dbname=misc", "root", "shrey");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
if (isset($_REQUEST['autos_id'])){
    $auto_id = htmlentities($_REQUEST['autos_id']);
    if (isset($_POST['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM autos
            WHERE auto_id = :auto_id");
            
        $stmt->execute([
            ':auto_id' => $auto_id, 
        ]);
        $_SESSION['status'] = 'Record deleted';
        $_SESSION['color'] = 'green';
        header('Location: index.php');
        return;
    }
    $stmt = $pdo->prepare("SELECT * FROM autos 
        WHERE auto_id = :auto_id");

    $stmt->execute([
        ':auto_id' => $auto_id, 
    ]);
    $auto = $stmt->fetch(PDO::FETCH_OBJ);
}
?>




<html>
<head>
<title>Deleting...</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>
<div class="container">
<p>Confirm: Deleting <?php echo $auto->make; ?></p>
<form method="post">
    <button input type="submit" value="Delete" name="delete">Delete</button>
    <a href="index.php">Cancel</a>
</form>
</div>
</body>
