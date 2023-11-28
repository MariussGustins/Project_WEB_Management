<?php
// Connect to the database
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_web_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loggedInUser = $_SESSION['username'];

// Query to retrieve the username from the database
$sql = "SELECT username FROM users WHERE username = '$loggedInUser'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the username from the result
    $row = $result->fetch_assoc();
    $usernameFromDatabase = $row['username'];
} else {
    $usernameFromDatabase = "Guest"; // Default to Guest if username not found
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
    <link rel="stylesheet" href="/css/Homepage.css">
</head>
<body>
<header>
    <?php echo "Welcome, $usernameFromDatabase!"; ?>
</header>
<main>
    <nav>
        <a href="#">WEB</a>
        <a href="#">Files</a>
        <a href="#">HOURS</a>
        <!-- Add more links as needed -->
    </nav>
    <section>
        <!-- Content for the selected option will go here -->
        <h1>Welcome to your Homepage!</h1>
    </section>
</main>
</body>
</html>

