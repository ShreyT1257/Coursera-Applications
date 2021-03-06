<?php
session_start();
try {
    $pdo = new PDO("mysql:host=localhost;dbname=misc-js,jq","root","shrey");
    $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $all_profiles = $pdo->query("Select * from users"); 
    while($row = $all_profiles->fetch(PDO::FETCH_OBJ)) {
        $profiles[] = $row;
    }
} 
catch(PDOException $e) {
    echo "Connection failed: ".$e->getMessage();
    die();
}
if ( isset($_POST['cancel'] ) ) { 
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_'; // Pw is php123
$failure = false;
if ( isset($_SESSION['failure']) ) {
    $failure = htmlentities($_SESSION['failure']);
    unset($_SESSION['failure']);
}
// Check if we have some POST data, Yes then process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['failure'] = "Email and Password required";
        header("Location: login.php");
        return;
    } else {
        $check = hash('md5',$salt.$_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id,name FROM users WHERE email=:em AND password=:pw');
        $stmt->execute([
            ':em' => $_POST['email'],
            ':pw' => $check
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row !== false) {
            $_SESSION['name'] = $row['name'];
            $_SESSION['user_id'] = $row['user_id'];
            header("Location: index.php");
            return;
        }  
        if ($check !== $row['password']) {
            error_log("Login fail".$_POST['email']."$check");
            $_SESSION['failure'] = "Incorrect password";
            header('Location: login.php');
            return;
        }
    }
}
?>




<!DOCTYPE html>
<html>
<head>
    <title>Shrey Trivedi's Login Page</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" 
        integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" 
        crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" 
        crossorigin="anonymous"></script>

</head>
<body>
<div class="container">
    <h1>Please Log In</h1>
    <?php 
        if ($failure !== false) {
            echo('<p style="color: red;">'.htmlentities($failure).'</p>');
        }
    ?>
    <form method="POST">
        <label for="email">Email</label>
        <input type="text" name="email" id="email"><br>
        <label for="id_1723">Password</label>
        <input type="password" name="pass" id="id_1723">
        <br>
        <br>
        <input type="submit" onclick="return doValidate();" value="Log In">
        <input type="submit" name="cancel" value="Cancel">
    </form>
    <br>
    <p>
    For a password hint, view source and find an account and password hint
    in the HTML comments.
    <!-- Hint: 
    The account is umsi@umich.edu & password is the three character name of the 
    programming language used in this class (all lower case) followed by 123. -->
    </p>

    <script>
        function doValidate() {
            console.log('Validating...');
            try {
                addr = document.getElementById('email').value;
                pw = document.getElementById('id_1723').value;
                console.log("Validating addr="+addr+" pw="+pw);
                if (addr == null || addr == "" || pw == null || pw == "") {
                    alert("Both fields must be filled out");
                    return false;
                } if ( addr.indexOf('@') == -1 ) {
                    alert("Invalid email address");
                    return false;
                }
                return true;
            } catch(e) {
                return false;
            }
            return false;
        }
    </script>
</div>
</body>
</html>
