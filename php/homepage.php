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
$selectedWebpage = $_GET['webpage'] ?? 1; // Set a default value (e.g., 1) when 'webpage' is not present

// Initialize $selectedWebpageName with a default value
$selectedWebpageName = "Unknown Webpage";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch data from the form
    $hours = $_POST['hours'];
    $description = $_POST['description'];
    $files = $_POST['files'];

    // Insert data into the database
    $sqlInsert = "INSERT INTO web_data (hours, description, files, webpage_id) VALUES ('$hours', '$description', '$files', '$selectedWebpage')";

    if ($conn->query($sqlInsert) === TRUE) {
        $message = "New record created successfully";
        echo "<script>
            setTimeout(function() {
                document.getElementById('successMessage').style.display = 'none';
            }, 3000);
         </script>";
    } else {
        $message = "Error: " . $sqlInsert . "<br>" . $conn->error;
    }

    echo "<div id='successMessage'>$message</div>";
}

// Fetch data related to the selected webpage from web_data table
$sqlWebpageData = "SELECT id, hours, description, files FROM web_data WHERE webpage_id = '$selectedWebpage'";
$resultWebpageData = $conn->query($sqlWebpageData);

// Initialize an empty array
$rows = [];

// Check if there are rows in the result set
if ($resultWebpageData && $resultWebpageData->num_rows > 0) {
    // Fetch data into the array
    while ($row = $resultWebpageData->fetch_assoc()) {
        $rows[] = $row;
    }
    // Close the result set after fetching data
    $resultWebpageData->close();
}
// Fetch the name of the selected webpage
$sqlWebpageName = "SELECT name FROM webpages WHERE id = '$selectedWebpage'";
$resultWebpageName = $conn->query($sqlWebpageName);

// Update $selectedWebpageName if the result set is not empty
if ($resultWebpageName && $resultWebpageName->num_rows > 0) {
    $row = $resultWebpageName->fetch_assoc();
    $selectedWebpageName = $row['name'];
}

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
                $resultWebpages = $conn->query("SELECT id, name FROM webpages");
                if ($resultWebpages) {
                    // Display dropdown options from the database
                    while ($row = $resultWebpages->fetch_assoc()) {
                        $webpageId = $row['id'];
                        $webpageName = $row['name'];
                        echo "<a href='?webpage=$webpageId'>$webpageName</a>";
                    }
                    // Close the result set after using it
                    $resultWebpages->close();
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
            <h2>Webpage: <?php echo $selectedWebpageName; ?></h2>

<!--             Display form to add data-->
            <form method="post" action="">
                <label for="hours">Hours:</label>
                <input type="text" id="hours" name="hours" required>

                <label for="description">Description:</label>
                <input type="text" id="description" name="description" required>

                <label for="files">Files:</label>
                <input type="file" id="files" name="files" required>

                <input type="submit" value="Add Data">
            </form>

            <!-- Display table with webpage data -->
            <table id="webpageTable" contenteditable="true">
                <tr>
                    <th>ID</th>
                    <th>Hours</th>
                    <th>Description</th>
                    <th>Files</th>
                </tr>
                <?php if (!empty($rows)): ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['hours']; ?></td>
                            <td><?php echo $row['description']; ?></td>
                            <td><?php echo $row['files']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
<!--                <tr id="newRow">-->
<!--                    <td></td>-->
<!--                    <td><input type="text" class="new-row-input" placeholder="Hours" contenteditable="true"></td>-->
<!--                    <td><input type="text" class="new-row-input" placeholder="Description" contenteditable="true"></td>-->
<!--                    <td><input type="text" class="new-row-input" placeholder="Files" contenteditable="true"></td>-->
<!--                </tr>-->
            </table>
<!--            <a href="#" class="add-files" onclick="addNewRow()">Add Data</a>-->
        <?php endif; ?>
    </section>

<!--    <script>-->
<!--        function addNewRow() {-->
<!--            // Get input values-->
<!--            var id = document.getElementById("newRow").querySelectorAll("input")[0].value;-->
<!--            var hours = document.getElementById("newRow").querySelectorAll("input")[1].value;-->
<!--            var description = document.getElementById("newRow").querySelectorAll("input")[2].value;-->
<!---->
<!--            // Insert new row into the table-->
<!--            var table = document.getElementById("webpageTable");-->
<!--            var newRow = table.insertRow(table.rows.length - 1);-->
<!---->
<!--            // Insert cells into the new row-->
<!--            var cellId = newRow.insertCell(0);-->
<!--            var cellHours = newRow.insertCell(1);-->
<!--            var cellDescription = newRow.insertCell(2);-->
<!---->
<!--            // Set values in the new row-->
<!--            cellId.innerHTML = id;-->
<!--            cellHours.innerHTML = hours;-->
<!--            cellDescription.innerHTML = description;-->
<!---->
<!--            // Clear input values-->
<!--            document.getElementById("newRow").querySelectorAll("input").forEach(function(input) {-->
<!--                input.value = "";-->
<!--            });-->
<!--        }-->
<!--    </script>-->
</main>
</body>
</html>
<?php
// Close the database connection after fetching all necessary data
$conn->close();
?>
