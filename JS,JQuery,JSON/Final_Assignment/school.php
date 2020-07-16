<?php
session_start();
if( !isset($_SESSION['name']) ) {
    die("ACCESS DENIED");
}
if( isset($_REQUEST['term']) ) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=misc-js,jq",'root','shrey');
        $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    } 
    catch (PDOException $e) {
        echo "Connection failed: ".$e->getMEssage();
        die();
    }
    
    $stmt = $pdo->prepare("SELECT name FROM Institution WHERE name LIKE :prefix");
    $stmt->execute([ ':prefix' => $_REQUEST['term']."%" ]);
    $retval = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $retval[] = $row['name'];
    }
    
    echo(json_encode($retval, JSON_PRETTY_PRINT));
}

?>
