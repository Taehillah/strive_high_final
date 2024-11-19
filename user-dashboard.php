<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in and has the role 'Learner'
if (!isset($_SESSION['loggedIn']) || $_SESSION['role'] != 'Learner') {
    header("Location: login.php");
    exit();
}

try {
    // Fetch learner details
    $stmt = $pdo->prepare("
        SELECT 
            full_name, email, role, grade, route, assigned_bus, waiting_list_position
        FROM Users 
        WHERE User_ID = :userId
    ");
    $stmt->bindParam(':userId', $_SESSION['userId']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch bus details if assigned
    $busDetails = null;
    if ($user['assigned_bus']) {
        $stmt = $pdo->prepare("
            SELECT Bus_Registration, Bus_Route, Bus_Timings, capacity, service_status 
            FROM Buses 
            WHERE Bus_Registration = :assignedBus
        ");
        $stmt->bindParam(':assignedBus', $user['assigned_bus']);
        $stmt->execute();
        $busDetails = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
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
                    <li class="nav-item"><a class="nav-link active" href="user-dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container mt-5">
        <h2>Welcome, <?php echo htmlspecialchars($user['full_name']); ?></h2>
        
        <!-- Learner Information -->
        <h3>Learner Information</h3>
        <ul class="list-group">
            <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
            <li class="list-group-item"><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></li>
            <li class="list-group-item"><strong>Grade:</strong> <?php echo htmlspecialchars($user['grade']); ?></li>
            <li class="list-group-item"><strong>Route:</strong> <?php echo htmlspecialchars($user['route']); ?></li>
            <li class="list-group-item">
                <strong>Assigned Bus:</strong> 
                <?php echo $user['assigned_bus'] ?: "Waiting List (Position: " . $user['waiting_list_position'] . ")"; ?>
            </li>
        </ul>

        <!-- Bus Information -->
        <?php if ($busDetails): ?>
            <h3 class="mt-4">Bus Information</h3>
            <ul class="list-group">
                <li class="list-group-item"><strong>Bus Registration:</strong> <?php echo htmlspecialchars($busDetails['Bus_Registration']); ?></li>
                <li class="list-group-item"><strong>Bus Route:</strong> <?php echo htmlspecialchars($busDetails['Bus_Route']); ?></li>
                <li class="list-group-item"><strong>Bus Timings:</strong> <?php echo htmlspecialchars($busDetails['Bus_Timings']); ?></li>
                <li class="list-group-item"><strong>Capacity:</strong> <?php echo htmlspecialchars($busDetails['capacity']); ?> Passengers</li>
                <li class="list-group-item"><strong>Service Status:</strong> <?php echo htmlspecialchars($busDetails['service_status']); ?></li>
            </ul>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer mt-4">
        <div class="container text-center">
            <span>© <?php echo date("Y"); ?> Strive High School. All rights reserved.</span>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
