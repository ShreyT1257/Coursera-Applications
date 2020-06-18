<?php
session_start();

if ( ! isset($_SESSION['name']) ) {
    die("Not logged in");
}
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
if (isset($_REQUEST['profile_id'])) {
    $profile_id = htmlentities($_REQUEST['profile_id']);
    if (isset($_POST['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM profile WHERE profile_id = :profile_id");
        $stmt->execute([':profile_id' => $profile_id]);
        $_SESSION['status'] = 'Record deleted';
        $_SESSION['color'] = 'green';
        header('Location: index.php');
        return;
    }
    $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :profile_id");
    $stmt->execute([':profile_id' => $profile_id]);
    $profile = $stmt->fetch(PDO::FETCH_OBJ);
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
    <p>Confirm: Deleting Profile</p>
    <p>
        First Name:
        <?php echo $profile->first_name; ?>
    </p>
    <p>
        Last Name:
        <?php echo $profile->last_name; ?>
    </p>
    <form method="post">
        <input type="hidden" name="profile_id" value="<?php echo $_GET['profile_id'] ?>">
        <button input type="submit" value="Delete" name="delete">Delete</button>
        <button type="submit" name="cancel" value="Cancel">Cancel</button>
    </form>
</div>
</body>
</html>