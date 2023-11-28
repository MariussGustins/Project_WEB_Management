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

// Retrieve the username from the session
$loggedInUser = $_SESSION['username'];

// Fetch all webpages from the database
$sqlWebpages = "SELECT id, name FROM webpages";
$resultWebpages = $conn->query($sqlWebpages);

// Fetch the username from the database
$sqlUsername = "SELECT username FROM users WHERE username = '$loggedInUser'";
$resultUsername = $conn->query($sqlUsername);

if ($resultUsername->num_rows > 0) {
    // Fetch the username from the result
    $row = $resultUsername->fetch_assoc();
    $usernameFromDatabase = $row['username'];
} else {
    $usernameFromDatabase = "Guest"; // Default to Guest if username not found
}

// Fetch data for the selected webpage
$selectedWebpage = isset($_GET['webpage']) ? $_GET['webpage'] : null;
if ($selectedWebpage) {
    // Fetch data related to the selected webpage from web_data table
    $sqlWebpageData = "SELECT id, hours, description FROM web_data WHERE webpage_id = '$selectedWebpage'";
    $resultWebpageData = $conn->query($sqlWebpageData);
} else {
    $resultWebpageData = null;
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
        <div class="dropdown">
            <span>WEB</span>
            <div class="dropdown-content">
                <?php
                // Display dropdown options from the database
                while ($row = $resultWebpages->fetch_assoc()) {
                    echo "<a href='#'>" . $row['name'] . "</a>";
                }
                ?>
            </div>
        </div>
        <a href="#">Files</a>
        <a href="#">HOURS</a>
        <!-- Add more links as needed -->
    </nav>
    <section>
        <h1>Welcome to your Homepage!</h1>

        <!-- Display selected webpage name -->
        <?php if ($selectedWebpage): ?>
            <h2>Webpage: <?php echo $selectedWebpage; ?></h2>
        <?php endif; ?>

        <!-- Display table with webpage data -->
        <table id="webpageTable" contenteditable="true">
            <tr>
                <th>ID</th>
                <th>Hours</th>
                <th>Description</th>
            </tr>
            <?php if ($resultWebpageData && $resultWebpageData->num_rows > 0): ?>
                <?php while ($row = $resultWebpageData->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['hours']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            <tr id="newRow">
                <td><input type="text" class="new-row-input" placeholder="ID" contenteditable="true"></td>
                <td><input type="text" class="new-row-input" placeholder="Hours" contenteditable="true"></td>
                <td><input type="text" class="new-row-input" placeholder="Description" contenteditable="true"></td>
            </tr>
        </table>
        <a href="#" class="add-files" onclick="addNewRow()">Add Data</a>
    </section>

    <script>
        function addNewRow() {
            // Get input values
            var id = document.getElementById("newRow").querySelectorAll("input")[0].value;
            var hours = document.getElementById("newRow").querySelectorAll("input")[1].value;
            var description = document.getElementById("newRow").querySelectorAll("input")[2].value;

            // Insert new row into the table
            var table = document.getElementById("webpageTable");
            var newRow = table.insertRow(table.rows.length - 1);

            // Insert cells into the new row
            var cellId = newRow.insertCell(0);
            var cellHours = newRow.insertCell(1);
            var cellDescription = newRow.insertCell(2);

            // Set values in the new row
            cellId.innerHTML = id;
            cellHours.innerHTML = hours;
            cellDescription.innerHTML = description;

            // Clear input values
            document.getElementById("newRow").querySelectorAll("input").forEach(function(input) {
                input.value = "";
            });
        }
    </script>
</main>
</body>
</html>

