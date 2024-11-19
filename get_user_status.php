<?php
// get_user_status.php
include 'db_connect.php';

$user_id = $_GET['user_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE User_ID = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($user);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
