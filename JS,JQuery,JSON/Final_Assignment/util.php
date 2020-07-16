<?php 
function ValidateProfile() {
    if (strlen($_POST['first_name']) == 0 || strlen($_POST['last_name']) == 0 || 
        strlen($_POST['email']) == 0 || strlen($_POST['headline']) == 0 ||
        strlen($_POST['summary']) == 0) {
            $_SESSION['status'] = "All fields are required";
        return;
    }
    if (strpos($_POST['email'], '@') === false) {
        $_SESSION['status'] = "Email must contain @";
        return;
    }
    return true;
}

function ValidatePos() {
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i])) continue;
        if (!isset($_POST['desc' . $i])) continue;

        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        if (strlen($year) == 0 || strlen($desc) == 0) {
            $_SESSION['status'] = "All fields are required";
            return;
        }
        if (!is_numeric($year)) {
            $_SESSION['status'] = "Position year must be numeric";
            return;
        }
    }
    return true; 
}

function ValidateEdu() {
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['edu_year'.$i])) continue;
        if (!isset($_POST['edu_school'.$i])) continue;

        $edu_year = $_POST['edu_year'.$i];
        $edu_school = $_POST['edu_school'.$i];

        if (strlen($edu_year) == 0 || strlen($edu_school) == 0) {
            $_SESSION['status'] = "All fields are required";
            return;
        }

        if (!is_numeric($edu_year)) {
            $_SESSION['status'] = "Education year must be numeric";
            return;
        }
    }
    return true;
}

function LoadPro($pdo, $profile_id) {
    $stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :prof");
    $stmt->execute([ ':prof' => $profile_id ]);
    $profile = $stmt->fetch();
    return $profile;
}

function LoadPos($pdo, $profile_id) {
    $stmt = $pdo->prepare("SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank");
    $stmt->execute([ ':prof' => $profile_id ]);
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $positions;
}

function LoadEdu($pdo, $profile_id) {
    $stmt = $pdo->prepare("SELECT year,name FROM Education e JOIN Institution i ON e.institution_id = i.institution_id 
                        WHERE profile_id = :prof ORDER BY rank");
    $stmt->execute([ ':prof' => $profile_id ]);
    $educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $educations;
}

function InsertPosition($pdo, $profile_id) {
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year'.$i])) continue;
        if (!isset($_POST['desc'.$i])) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        
        $stmt = $pdo->prepare('INSERT INTO Position(profile_id, rank, year, description) VALUES(:pid, :rank, :year, :desc)');

        $stmt->execute([
                ':pid' => $profile_id,
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc
        ]);
        $rank++;
    }
}

function InsertEdu($pdo, $profile_id) {
    $rank = 1;
    for($i=1; $i<=9; $i++) {
        if (!isset($_POST['edu_year'.$i])) continue;
        if (!isset($_POST['edu_school'.$i])) continue;
        $year = $_POST['edu_year'.$i];
        $school = $_POST['edu_school'.$i];

        $institution_id = false;
        $stmt = $pdo->prepare("SELECT institution_id FROM Institution WHERE name = :name");
        $stmt->execute([ ':name' => $school ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row != false) $institution_id = $row['institution_id'];

        if($institution_id === false) {
            $stmt = $pdo->prepare("INSERT INTO Institution(name) VALUES(:name)");
            $stmt->execute([ ':name' => $school ]);
            $institution_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare("INSERT INTO Education(profile_id, rank, year, institution_id) VALUES(:pid, :rank, :year, :iid)");
        $stmt->execute([
            'pid' => $profile_id,
            ':rank' => $rank,
            ':year' => $year,
            ':iid' => $institution_id
        ]);
        $rank++;
    }
}

?>
