<?php 
session_start();
include "util.php";

// If the user requested cancel, go back to index.php
if(isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

//Make sure REQUEST parameter is present
if (!isset($_REQUEST['profile_id'])) {
    $_SESSION['status'] = "Missing profile_id";
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

if (isset($_POST['first_name']) && isset($_POST['last_name']) && 
    isset($_POST['email']) && isset($_POST['headline']) &&
    isset($_POST['summary'])) {

    if (!ValidateProfile()) {
        $_SESSION['status'] = ValidateProfile();
        header("Location: edit.php?profile_id=".$_REQUEST["profile_id"]);
    }

    if (!ValidatePos()) {
        $_SESSION['status'] = ValidatePos();
        header("Location: edit.php?profile_id=".$_REQUEST["profile_id"]);
    }

    if (!ValidateEdu()) {
        $_SESSION['status'] = ValidateEdu();
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
    }

    // Updating Data
    $stmt = $pdo->prepare('UPDATE Profile SET first_name = :fn, last_name = :ln,email=:em, headline=:he,summary=:su
                    WHERE profile_id = :pid AND user_id=:uid');
    $stmt->execute([
            ':pid' => $_REQUEST['profile_id'],
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary']
    ]);

    // Clearing out the old Position Entries
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

    //Insert Position Entries
    InsertPosition($pdo, $_REQUEST['profile_id']);

    // Clearing out old Education Entries
    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

    // Insert Education Entries
    InsertEdu($pdo, $_REQUEST['profile_id']);

    $_SESSION['status'] = 'Profile updated';
    $_SESSION['color'] = "green";
    header('Location: index.php');
    return;
}

$profile = LoadPro($pdo, $_REQUEST['profile_id']);
$positions = LoadPos($pdo, $_REQUEST['profile_id']);
$schools = LoadEdu($pdo, $_REQUEST['profile_id']);

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
    <form>
    <div class="container">
        <h1>Editing Profile for <?php htmlentities($_SESSION['name']); ?></h1>
        <?php
            if ($status !== false) {
                echo('<p style="color: ' .$status_color. ';">'.htmlentities($status)."</p>\n");
                unset($_SESSION['status']);
            }
        ?>
        <form method="post">
            <p>First Name:
                <input type="text" name="first_name" size="60" value="<?php echo $profile['first_name']; ?>"/></p>
            <p>Last Name:
                <input type="text" name="last_name" size="60" value="<?php echo $profile['last_name']; ?>"/></p>
            <p>Email:
                <input type="text" name="email" size="30" value="<?php echo $profile['email']; ?>"/></p>
            <p>Headline:<br/>
                <input type="text" name="headline" size="80" value="<?php echo $profile['headline']; ?>"/></p>
            <p>Summary:<br/>
                <textarea name="summary" rows="8" cols="80"><?php echo $profile['summary']; ?></textarea></p>
            <?php
                $countEdu = 0;
                echo('<p>Education: <input type="submit" id="addEdu" value="+">' . "\n");
                echo('<div id="edu_fields">');
                if (count($schools) > 0) {
                    foreach ($schools as $school) {
                        $countEdu++;
                        echo('<div id="edu' . $countEdu . '">');
                        echo
                            '<p>Year: <input type="text" name="edu_year' . $countEdu . '" value="' . $school['year'] . '">
                            <input type="button" value="-" onclick="$(\'#edu' . $countEdu . '\').remove();return false;\"></p>
                            <p>School: <input type="text" size="80" name="edu_school' . $countEdu . '" class="school" 
                            value="' . htmlentities($school['name']) . '" />';
                            echo "\n</div>\n";
                    }
                }
                echo "</div></p>\n";

                $countPos = 0;
                echo('<p>Position: <input type="submit" id="addPos" value="+">' . "\n");
                echo('<div id="position_fields">');
                if (count($positions) > 0) {
                    foreach ($positions as $position) {
                        $countPos++;
                        echo('<div id="position' . $countPos . '">');
                        echo
                            '<p>Year: <input type="text" name="year' . $countPos . '" value="' . htmlentities($position['year']) . '">
                            <input type="button" value="-" onclick="$(\'#position' . $countPos . '\').remove();return false;"><br>';
                        echo '<textarea name="desc' . $countPos . '"rows="8" cols="80">' . "\n";
                        echo htmlentities($position['description']) . "\n";
                        echo "\n</textarea>\n</div>\n";
                    }
                }
                echo("</div></p>\n");
            ?>
            <p>
                <input type="submit" value="Save">
                <input type="submit" name="cancel" value="Cancel">
            </p>        
        </form>

    <script>
        countPos = 0;
        countEdu = 0;
        $(document).ready(function () {
            window.console && console.log('Document ready called');
            $('#addPos').click(function (event) {
                event.preventDefault();
                if (countPos >= 9) {
                    alert("Maximum of nine position entries exceeded");
                    return;
                }
                countPos++;
                window.console && console.log("Adding position " + countPos);
                $('#position_fields').append(
                    '<div id="position' + countPos + '"> \
                    <p>Year: <input type="text" name="year' + countPos + '" value="" /> \
                    <input type="button" value="-" onclick="$(\'#position' + countPos + '\').remove();return false;"><br><br>\
                    <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>\
                    </div>');
            });

            
            $('#addEdu').click(function (event) {
                event.preventDefault();
                if (countEdu >= 9) {
                    alert("Maximum of nine education entries exceeded");
                    return;
                }
                countEdu++;
                window.console && console.log("Adding education " + countEdu);
                $('#edu_fields').append(
                    '<div id="edu' + countEdu + '"> \
                <p>Year: <input type="text" name="edu_year' + countEdu + '" value="" /> \
                <input type="button" value="-" onclick="$(\'#edu' + countEdu + '\').remove();return false;"><br>\
                <p>School: <input type="text" size="80" name="edu_school' + countEdu + '" class="school" value="" />\
                </p></div>'
                );


                // Grab some HTML with hot spots and insert into the DOM
                var source = $('#edu-template').html();
                $('#edu_fields').append(source.replace(/@COUNT@/g,countEdu));

                // Add the event handler to the new ones
                $('.school').autocomplete({
                    source: "school.php"
                });
            });

            $('.school').autocomplete({
                source: "school.php"
            });
        });
    </script>
    <script id="edu-template" type="text">
        <div id="edu@COUNT@">
            <p>Year: <input type="text" name="edu_year@COUNT@" value="" />
            <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false;"><br>
            <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value="" />
            </p>
        </div>
    </script>
</div>
</body>
</html>
