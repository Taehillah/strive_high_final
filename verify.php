<?php
include 'db_connect.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    try {
        // Check if the token exists in the database
        $stmt = $pdo->prepare("SELECT User_ID FROM Users WHERE verification_token = :token AND verified = 0");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Update the user's verified status
            $stmt = $pdo->prepare("UPDATE Users SET verified = 1, verification_token = NULL WHERE User_ID = :userId");
            $stmt->bindParam(':userId', $user['User_ID']);
            $stmt->execute();

            echo "Your email has been verified! You can now log in.";
        } else {
            echo "Invalid or expired token.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No token provided.";
}
?>
