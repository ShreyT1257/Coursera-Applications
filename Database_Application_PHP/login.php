<?php
session_start();
if ( isset($_POST['cancel'] ) ) { 
    header("Location: index.php");
    return;
}
$salt = 'XyZzy12*_';
$stored_hash = hash('md5', 'XyZzy12*_php123');;  // Pw is php123

$failure = false;  // If we have no POST data
if (isset($_SESSION['failure'])) {
    $failure = htmlentities($_SESSION['failure']);
    unset($_SESSION['failure']); 
}

// If we have some POST data, and process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['failure'] = "User name and password are required";
        header('Location: login.php');
        return;
    } 
    $email = htmlentities($_POST['email']);
    $pass = htmlentities($_POST['pass']);
    $check = hash('md5', $salt.$pass);
    if ($check != $stored_hash) {
        error_log("Login fail".$pass."$check");
        $_SESSION['failure'] = "Incorrect password";
        header('Location: login.php');
        return;
    } 
    error_log("Login success".$email);
    $_SESSION['name'] = $email;
    header('Location: index.php');
    return;
}
?>




<!DOCTYPE html>
<html>
<head>
<title>Shrey Trivedi's Login Page</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <h1>Please Log In</h1>
    <?php 
        if ($failure !== false) {
            echo('<p style="color: red;">'.htmlentities($failure).'</p> \n');
        }
    ?>
    <form method="POST">
        <label for="nam">User Name</label>
        <input type="text" name="email" id="nam"><br/>
        <label for="id_1723">Password</label>
        <input type="text" name="pass" id="id_1723">
        <br/>
        <button input type="submit" name="Log In" value="login">Login</button>
        <a href="index.php" name='cancel'>Cancel</a></p>
    </form>
    <p>
    For a password hint, view source and find a password hint
    in the HTML comments.
    <!-- Hint: The password is the three character name of the
    programming language used in this class (all lower case)
    followed by 123. -->
    </p>
</div>
</body>
</html>