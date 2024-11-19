<?php
session_start();
include 'db_connect.php';

$title = "Strive High School - Verify OTP";
$errorMessage = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $otpCode = htmlspecialchars(trim($_POST['otpCode']));
    $userID = $_SESSION['userID'];

    try {
        // Check if the OTP code is correct using user_ID
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE user_ID = :user_ID AND otp_code = :otp_code");
        $stmt->execute([':user_ID' => $userID, ':otp_code' => $otpCode]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Update the user to mark them as verified
            $stmt = $pdo->prepare("UPDATE Users SET verified = 1 WHERE user_ID = :user_ID");
            $stmt->execute([':user_ID' => $userID]);

            $successMessage = "Your phone number has been successfully verified. You can now log in.";
        } else {
            $errorMessage = "Invalid OTP. Please try again.";
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $errorMessage = "An error occurred during verification. Please try again later.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Your custom styles -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">Strive High School</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- OTP Verification Form -->
    <section class="main-section">
        <div class="container">
            <h2>Verify OTP</h2>

            <!-- Error/Success Messages -->
            <?php if ($successMessage): ?>
                <div class="alert alert-success"><?php echo $successMessage; ?></div>
            <?php elseif ($errorMessage): ?>
                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
            <?php endif; ?>

            <form action="" method="post">
                <!-- OTP Code -->
                <div class="mb-3">
                    <label for="otpCode" class="form-label">Enter OTP Code</label>
                    <input type="text" class="form-control" id="otpCode" name="otpCode" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <span>© <?php echo date("Y"); ?> Strive High School. All rights reserved.</span>
        </div>
    </footer>

    <!-- Bootstrap JS for functionality -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
