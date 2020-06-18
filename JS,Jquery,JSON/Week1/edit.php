<?php
session_start();
if (isset($_SESSION['name']) == false) {
	die("Not logged in");
}
// If the user requested logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}
try {
    $pdo = new PDO("mysql:host=localhost;dbname=misc", "root", "shrey");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
$status = false;
if ( isset($_SESSION['status']) ) {
	$status = htmlentities($_SESSION['status']);
	$status_color = htmlentities($_SESSION['color']);
	unset($_SESSION['status']);
	unset($_SESSION['color']);
}

$_SESSION['color'] = 'red';
if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {
	if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1 ) {
		$_SESSION['status'] = "All fields required";
		header("Location: edit.php");
		return;
	} else if (strpos($_POST['email'],'@') == false) {
		$_SESSION['status'] = "Email must have an at (@) sign";
		header("Location: edit.php");
		return;
	}
	else {
		$stmt = $pdo->prepare("UPDATE profile SET first_name=:fn, last_name=:ln, email=:em, headline=:he, summary=:su
		WHERE profile_id = :p_idmrk");
		$stmt->execute([
			':fn' => $_POST['first_name'],
			':ln' => $_POST['last_name'],
			':em' => $_POST['email'],
			':he' => $_POST['headline'],
			':su' => $_POST['summary'],
			':p_idmrk' => $_POST['profile_id']
		]);
		$_SESSION['status'] = 'Record edited';
		$_SESSION['color'] = 'green';
		header('Location: index.php');
		return;
	}
}
$stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :xyz");
$stmt->execute([':xyz' => $_GET['profile_id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row === false) {
	$_SESSION['status'] = "Bad value for profile_id";
	header("Location: add.php");
	return;
}
$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$he = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
$p_id = htmlentities($row['profile_id']);
?>




<!DOCTYPE html>
<html>
<head>
<title>Shrey Trivedi's Resume Registry</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <h1>Editing Profile for <?php echo $fn; ?></h1>
    <?php
        if ($status !== false) {
            echo('<p style="color: ' .$status_color. ';">'.htmlentities($status)."</p>\n");
        }
    ?>
    <form method="post">
        <p>
			First Name:
			<input type="text" name="first_name" size="60" value="<?= $fn ?>"/>
		</p>
		<p>
			Last Name:
			<input type="text" name="last_name" size="60" value="<?= $ln ?>"/>
		</p>
		<p>
			Email:
			<input type="text" name="email" size="30" value="<?= $em ?>"/>
		</p>
		<p>
			Headline:
			<input type="text" name="headline" size="60" value="<?= $he ?>"/>
		</p>
		<p>
			Summary:
			<input type="text" name="summary" size="60" value="<?= $su ?>"/>
		</p>
		<p>
			<input type="hidden" name="profile_id" value="<?= $p_id ?>"/>
			<button type="submit" name="save" value="Save">Save</button>
			<button type="submit" name="cancel" value="Cancel">Cancel</button>
		</p>
    </form>
</div>
</body>
</html>
