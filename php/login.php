<?php
// Connect to the database
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_web_management";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    // Query to fetch user from the database
    $sql = "SELECT username FROM users WHERE username = '$inputUsername' AND password = '$inputPassword'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Start session and store username
        session_start();
        $_SESSION['username'] = $inputUsername;

        // Redirect to the homepage
        header("Location: homepage.php");
        exit();
    } else {
        // Redirect to the login page with an error message
        header("Location: login.html?error=1");
        exit();
    }
}


$conn->close();
?>
