<?php
// Include the database connection
include 'connection.php'; // Assuming $conn is defined here

// Check if parent_id is provided
if (isset($_GET['parent_id'])) {
    $parent_id = $_GET['parent_id'];

    // Query to get authorized persons based on the selected parent_id
    $query = "SELECT id, fullname FROM authorized_persons WHERE parent_id = ?";

    // Prepare the query and execute it
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch the results into an array
        $authorized_persons = [];
        while ($row = $result->fetch_assoc()) {
            $authorized_persons[] = $row;
        }

        // Return the results as JSON
        header('Content-Type: application/json');
        echo json_encode($authorized_persons);

        $stmt->close();
    } else {
        echo json_encode(["error" => "Query preparation failed"]);
    }

    $conn->close();
} else {
    // If parent_id is not provided, return an empty array
    header('Content-Type: application/json');
    echo json_encode([]);
}
?>
