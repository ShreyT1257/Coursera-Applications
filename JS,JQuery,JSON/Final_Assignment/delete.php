<?php 
session_start();
if(!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}
if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}
try {
    $pdo = new PDO("mysql:host=localhost;dbname=misc-js,jq","root","shrey");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    echo "Connection Failed: ".$e->getMessage();
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

// Guardian: Make sure that user_id is present
if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['status'] = "Missing user_id";
    header('Location: index.php');
    return;
}

if ( isset($_POST['Delete']) && isset($_POST['profile_id']) ) {
    $stmt = $pdo->prepare("DELETE FROM Profile WHERE profile_id = :profile_id");
    $stmt->execute(array(':profile_id' => $_POST['profile_id']));
    $_SESSION['status'] = 'Record deleted';
    $_SESSION['color'] = "green";
    header( 'Location: index.php' ) ;
    return;
}
$stmt = $pdo->prepare("SELECT first_name, last_name FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['status'] = 'Bad value for profile id';
    header('Location: index.php');
    return;
}
?>





<!DOCTYPE html>
<html>
<head>
    <title>Shrey Trivedi's Resume Registry</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" 
    crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" 
    crossorigin="anonymous"></script>
</head>
<body>
    <div class="container"> 
    <h1>Confirm Deleting Profile......</h1> 
        <?php 
            if( $status !== false ) {
                echo('<p style ="color: '.$status_color.';">'.htmlentities($status)."</p> \n");
            }
        ?>         
        <p>First Name: <?php echo($row['first_name']); ?></p>
        <p>Last Name: <?php echo($row['last_name']); ?></p>
        <form method="post">
            <input type="hidden" name="profile_id" value="<?php echo $_GET['profile_id'] ?>">
            <button type="submit" name="Delete" value="Delete">Delete</button>
            <button type="submit" name="cancel" value="cancel">Cancel</button>
        </form>
    </div>
</body>
</html>
