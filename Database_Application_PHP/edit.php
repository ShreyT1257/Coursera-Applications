<?php
session_start();
if ( ! isset($_SESSION['name']) ) {
	die("ACCESS DENIED");
}
// If the user requested logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}
$status = false;
if ( isset($_SESSION['status']) ) {
	$status = htmlentities($_SESSION['status']);
	$status_color = htmlentities($_SESSION['color']);
	unset($_SESSION['status']);
	unset($_SESSION['color']);
}
try {
    $pdo = new PDO("mysql:host=localhost;dbname=misc", "root", "shrey");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
$name = htmlentities($_SESSION['name']);
$_SESSION['color'] = 'red';
if (isset($_REQUEST['autos_id'])) {
// Check to see if we have some POST data, if we do process it
	if (isset($_POST['mileage']) && isset($_POST['year']) && isset($_POST['make']) && isset($_POST['model'])) {
	    if (strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 || strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1) {
	        $_SESSION['status'] = "All fields are required";
	        header("Location: edit.php?autos_id=" . htmlentities($_REQUEST['autos_id']));
	        return;
	    }
	    if (!is_numeric($_POST['year']) ) {
	        $_SESSION['status'] = "Year must be an integer";
	        header("Location: edit.php?autos_id=" . htmlentities($_REQUEST['autos_id']));
			return;
	    } 
	    if ( !is_numeric($_POST['mileage'])) {
	        $_SESSION['status'] = "Mileage must be an integer";
	        header("Location: edit.php?autos_id=" . htmlentities($_REQUEST['autos_id']));
	        return;
	    } 
	    $make = htmlentities($_POST['make']);
	    $model = htmlentities($_POST['model']);
	    $year = htmlentities($_POST['year']);
	    $mileage = htmlentities($_POST['mileage']);

    	$auto_id = htmlentities($_REQUEST['autos_id']);
	    $stmt = $pdo->prepare("
	    	UPDATE autos
	    	SET make = :make, model = :model, year = :year, mileage = :mileage
		    WHERE auto_id = :auto_id
	    ");

	    $stmt->execute([
	        ':make' => $make, 
	        ':model' => $model, 
	        ':year' => $year,
	        ':mileage' => $mileage,
	        ':auto_id' => $auto_id,
	    ]);
	    $_SESSION['status'] = 'Record edited';
	    $_SESSION['color'] = 'green';
	    header('Location: index.php');
		return;
	}
	$auto_id = htmlentities($_REQUEST['autos_id']);
    
    $stmt = $pdo->prepare("
	    SELECT * FROM autos 
	    WHERE auto_id = :auto_id
	");

	$stmt->execute([
	    ':auto_id' => $auto_id, 
	]);
	$auto = $stmt->fetch(PDO::FETCH_OBJ);
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
    <h1>Editing Automobile</h1>
    <?php
        if ($status !== false) {
            echo('<p style="color: ' .$status_color. ';">'.htmlentities($status)."</p>\n");
        }
    ?>
    <form method="post">
        <p>Make:
        <input type="text" name="make" id="make" value="<?php echo htmlentities($auto->make); ?>">
        <p>Model:
        <input type="text" name="model" id="model" value="<?php echo htmlentities($auto->model); ?>">
        <p>Year:
        <input type="text" name="year" id="year" value="<?php echo htmlentities($auto->year); ?>">
        <p>Mileage:
        <input type="text" name="mileage" id="mileage" value="<?php echo htmlentities($auto->mileage); ?>">
        <button type="submit" name="save" value="Save">Save</button>
        <input type="submit" name="logout" value="Cancel" />
    </form>
    <p>
</div>
</body>
</html>
