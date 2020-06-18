<?php
session_start();
$logged_in = false;
$profiles = [];
if (isset($_SESSION['name'])) {
    $logged_in = true;
    $status = false;

    if (isset($_SESSION['status'])) {
        $status = htmlentities($_SESSION['status']);
        $status_color = htmlentities($_SESSION['color']);
        unset($_SESSION['status']);
        unset($_SESSION['color']);
    } try {
    $pdo = new PDO("mysql:host=localhost;dbname=misc","root","shrey");
    $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $all_profiles = $pdo->query("SELECT * from profile"); 
    while($row = $all_profiles->fetch(PDO::FETCH_OBJ)) {
        $profiles[] = $row;
        }
    } catch(PDOException $e) {
    echo "Connection failed: ".$e->getMessage();
    die();
    }
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
<h1>Shrey Trivedi's Resume Registry</h1>
<?php if(!$logged_in) : ?>
    <p><a href="login.php">Please log in</a></p>
    <p>Attempt <a href="add.php">add data</a> without logging in.</p>

<?php else : ?>
<?php 
    if ($status !== false) {
        echo('<p style ="color: '.$status_color.';">'.htmlentities($status)."</p> \n");
    }
?>

<?php if (empty($profiles)) : ?>
    <p>No rows found</p>
<?php else : ?>
<div class="row">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Headline</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($profiles as $profile) : ?>
                <tr>
                    <td>
                        <a href="view.php?profile_id=<?php echo $profile->profile_id; ?>">
                        <?php echo $profile->first_name . ' ' .$profile->last_name; ?></a>
                    </td>
                    <td><?php echo $profile->headline ?></td>
                    <td>
                        <a href="edit.php?profile_id=<?php echo $profile->profile_id; ?>">Edit</a> /
                        <a href="delete.php?profile_id=<?php echo $profile->profile_id; ?>">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<p><a href="logout.php">Logout</a></p>
<p><a href="add.php">Add New Entry</a></p>
<?php endif; ?>
</div>
</body>
</html>
