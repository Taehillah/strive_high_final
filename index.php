<?php


// Set dynamic title
$title = "Strive High School - Home";


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strive High School - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Make sure the page fills the entire screen */
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        /* Navbar styling */
        .navbar {
            background-color: #f8f9fa;
        }

        /* Welcome Section Styling */
        .welcome-section {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url('assets/images/hero.jpg'); /* Background cover image */
            background-size: cover;
            background-position: center;
            color: white;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
            padding: 60px 15px;
            text-align: center;
        }

        /* Typography styling */
        h1 {
            font-size: 3em;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        p.lead {
            font-size: 1.3em;
            max-width: 600px;
            margin: 0 auto 30px;
            color: #ddd;
        }

        /* Button styling */
        .btn-primary {
            padding: 10px 20px;
            font-size: 1.1em;
            font-weight: bold;
            border-radius: 8px;
        }

        /* Footer styling */
        .footer {
            background-color: #333;
            color: #f8f9fa;
            text-align: center;
            padding: 15px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">Strive High School</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Welcome Section -->
    <section class="welcome-section">
        <div class="container">
            <h1>Welcome to Strive High School Bus Registration System</h1>
            <p class="lead">Securely register for convenient and safe school transportation.</p>
            <a href="register.php" class="btn btn-primary mt-3">Enter Here</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <span>© 2024 Strive High School. All Rights Reserved.</span>
        </div>
    </footer>

    <!-- Optional JavaScript for Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
