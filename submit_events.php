<?php
include 'connection.php';

// Check if form is submitted and if picture is uploaded
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $title = $_POST['title'];
    $byline = $_POST['byline'];
    $paragraph = $_POST['paragraph'];
    $date = $_POST['date'];

    // Handle the uploaded file
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        // Get the file content
        $picture = file_get_contents($_FILES['picture']['tmp_name']);

        // Prepare the SQL query to insert data, including the image as a binary (LONG BLOB)
        $stmt = $conn->prepare("INSERT INTO events (title, byline, paragraph, date, picture) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $byline, $paragraph, $date, $picture);

        // Execute the query
        if ($stmt->execute()) {
            echo '<script>alert("Event Recorded Successfully!"); window.location.href="events.php";</script>';
        } else {
            echo '<script>alert("Error occurred while recording the event!"); window.location.href="events.php";</script>';
        }

        // Close the statement
        $stmt->close();
    } else {
        echo '<script>alert("Please upload a valid image file!"); window.location.href="events.php";</script>';
    }

    // Close the database connection
    $conn->close();
}
?>
