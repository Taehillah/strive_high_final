<?php
session_start();
include 'db_connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use League\OAuth2\Client\Provider\Google;

require 'vendor/autoload.php';

$title = "Strive High School - Register";
$errorMessage = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    // Form Data
    $role = !empty($_POST['role']) ? $_POST['role'] : null;
    $fullName = htmlspecialchars(trim($_POST['fullName']));
    $grade = $_POST['grade'];
    $route = $_POST['route'];
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $phoneNumber = htmlspecialchars(trim($_POST['phoneNumber']));
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Validate inputs
    if (!$role) {
        $errorMessage = "Please select a role.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email address.";
    } elseif (strlen($password) < 8) {
        $errorMessage = "Password must be at least 8 characters long.";
    } else {
        // Generate OTP
        $otpCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        try {
            // Check if the email already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $emailExists = $stmt->fetchColumn();

            if ($emailExists) {
                $errorMessage = "An account with this email already exists.";
            } else {
                // Assign a bus to the user based on their route
                $stmt = $pdo->prepare("
                    SELECT TOP 1 Bus_Registration 
                    FROM Buses 
                    WHERE Bus_Route = :route AND service_status = 'Operational' 
                    AND (SELECT COUNT(*) FROM Users WHERE assigned_bus = Bus_Registration) < capacity
                ");
                $stmt->execute([':route' => $route]);
                $assignedBus = $stmt->fetchColumn();

                if (!$assignedBus) {
                    $assignedBus = null; // No available bus, user goes to the waiting list
                }

                // Insert the user into the database
                $stmt = $pdo->prepare("
                    INSERT INTO Users (role, email, password, route, full_name, grade, assigned_bus, otp_code, verified, phoneNumber) 
                    VALUES (:role, :email, :password, :route, :full_name, :grade, :assigned_bus, :otp_code, 0, :phone_number)
                ");
                $stmt->bindParam(':role', $role);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':route', $route);
                $stmt->bindParam(':full_name', $fullName);
                $stmt->bindParam(':grade', $grade);
                $stmt->bindParam(':assigned_bus', $assignedBus, PDO::PARAM_NULL);
                $stmt->bindParam(':otp_code', $otpCode);
                $stmt->bindParam(':phone_number', $phoneNumber);
                $stmt->execute();

                // Send OTP via WhatsApp
                if (sendOtpWhatsApp($phoneNumber, $otpCode)) {
                    
                    $_SESSION['successMessage'] = "Registration successful! Please enter your OTP to verify your phone number.";
                    header("Location: verify_otp.php");
                    exit();
                } else {
                    $errorMessage = "Unable to send OTP via WhatsApp. Please try again later.";
                }
                $errorMessage = "Unable to send OTP. Please try again later.";
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errorMessage = "An error occurred during registration. Please try again later.";
        }
    }
}

function sendOtpWhatsApp($phoneNumber, $otpCode) {
    $url = 'https://messages-sandbox.nexmo.com/v1/messages';
    $apiKey = '240ae5a2';
    $apiSecret = '5dLWgFY5NZqHSYD4';
    $from = '14157386102';
    
    $data = [
        'from' => $from,
        'to' => "whatsapp:$phoneNumber",
        'message_type' => 'text',
        'text' => "Your OTP code for Strive High School is: $otpCode",
        'channel' => 'whatsapp'
    ];

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => "$apiKey:$apiSecret",
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data)
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 202) {
        return true;
    } else {
        error_log("Failed to send WhatsApp message. Response: $response");
        return false;
    }
}
?>

<!-- Your existing HTML content remains the same -->

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your existing head content -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Your custom styles -->
    <link rel="stylesheet" href="assets/css/style.css">
<script>
function toggleFields() {
    const roleSelect = document.getElementById('role');
    const gradeField = document.getElementById('grade');
    const routeField = document.getElementById('route');
    
    if (roleSelect.value === 'Admin') {
        gradeField.disabled = true;
        routeField.disabled = true;
    } else {
        gradeField.disabled = false;
        routeField.disabled = false;
    }
}
</script>
</head>
<body>
    <!-- Navbar, similar to other pages -->
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
                    <li class="nav-item"><a class="nav-link active" href="register.php">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Registration Form -->
    <section class="main-section">
        <div class="container">
            <h2>Register for Bus Service</h2>

            <!-- Error/Success Messages -->
            <?php if ($successMessage): ?>
                <div class="alert alert-success"><?php echo $successMessage; ?></div>
            <?php elseif ($errorMessage): ?>
                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
            <?php endif; ?>

            <form action="" method="post">
                <!-- Role Dropdown -->
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required onchange="toggleFields()">
                        <option value="">Select Role</option>
                        <option value="Learner">Learner</option>
                        <option value="Father">Father</option>
                        <option value="Mother">Mother</option>
                        <option value="Guardian">Guardian</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>

                <!-- Full Name -->
                <div class="mb-3">
                    <label for="fullName" class="form-label">Full Name (of Learner or Admin)</label>
                    <input type="text" class="form-control" id="fullName" name="fullName" required>
                </div>

                <!-- Grade -->
                <div class="mb-3">
                    <label for="grade" class="form-label">Grade</label>
                    <input type="number" class="form-control" id="grade" name="grade" required>
                </div>

                <!-- Route -->
                <label for="route" class="form-label">Preferred Route</label>
                    <select class="form-select" id="route" name="route" required>
                        <option value="">Select Route</option>
                        <option value="Rooihuiskraal">Rooihuiskraal</option>
                        <option value="Wierdapark">Wierdapark</option>
                        <option value="Centurion">Centurion</option>
                    </select>

                <!-- Phone Number -->
                <div class="mb-3">
                    <label for="phoneNumber" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" required>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password (minimum 8 characters)</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Register</button>
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