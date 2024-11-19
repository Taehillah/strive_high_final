<?php
// db_connect.php
$host = 'strivehigh.database.windows.net';
$db = 'strive';
$user = 'Admin_43385508';
$pass = 'Ishmael@12345';

try {
    $pdo = new PDO("sqlsrv:server=$host;Database=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 //   echo "Connected to Azure SQL Database successfully!";
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}
?>
