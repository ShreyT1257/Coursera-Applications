<?php
session_start();
$logged_in = false;
$autos = [];
if (isset($_SESSION['name'])) {
    $logged_in = true;
    $status = false;

    if (isset($_SESSION['status'])) {
        $status = htmlentities($_SESSION['status']);
        $status_color = htmlentities($_SESSION['color']);
        unset($_SESSION['status']);
        unset($_SESSION['color']);
    }
}
try {
    $pdo = new PDO("mysql:host=localhost;dbname=misc","root","shrey");
    $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $all_autos = $pdo->query("Select * from autos"); 
    while($row = $all_autos->fetch(PDO::FETCH_OBJ)) {
        $autos[] = $row;
    }
} catch(PDOException $e) {
    echo "Connection failed: ".$e->getMessage();
    die();
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Shrey Trivedi's Index Page</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>
<div class="container">
	<h1>Welcome to the Automobiles Database</h1>
    <?php if (!$logged_in) : ?>
	    <p><a href="login.php">Please log in</a></p>
	    <p>Attempt to <a href="add.php">add data</a> without logging in.</p>
	<?php else : ?>
        <?php
	    if ( $status !== false ) {
	        echo('<p style="color: '.$status_color.';" class="col-sm-10">'.$status."</p>\n");
        } 
        ?>
        <?php if (empty($autos)) : ?>
		<p>No rows found</p>
        <?php else : ?>
	<p>
    <table class="table">
		<thead>
			<tr>
				<th>Make</th>
				<th>Model</th>
				<th>Year</th>
			    <th>Mileage</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($autos as $auto) : ?>
			<tr>
			    <td><?php echo $auto->make; ?></td>
				<td><?php echo $auto->model; ?></td>
				<td><?php echo $auto->year; ?></td>
				<td><?php echo $auto->mileage; ?></td>
				<td><a href="edit.php?autos_id=<?php echo $auto->auto_id; ?>">Edit</a> / <a href="delete.php?autos_id=<?php echo $auto->auto_id; ?>">Delete</a></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
    </p>
	<?php endif; ?>
	<p><a href="add.php">Add New Entry</a></p>
	<p><a href="logout.php">Logout</a></p>
    <p>
<b>Note:</b> Your implementation should retain data across multiple 
logout/login sessions.  This sample implementation clears all its
data on logout - which you should not do in your implementation.
</p>
    <?php endif; ?>	
</div>