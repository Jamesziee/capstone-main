<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$studentId = $data['studentId']; // Get studentId from the request body
$authorizedId = $data['authorizedId']; // Get authorizedId (authorized person's ID)

// Check if required parameters are set
if (empty($studentId) || empty($authorizedId)) {
    echo json_encode(["message" => "Student ID and Authorized ID are required."]);
    exit;
}

// Database connection
include 'connection.php';

// Ensure connection was successful
if ($conn->connect_error) {
    echo json_encode(["message" => "Failed to connect to the database."]);
    exit;
}

// Query to fetch the full name of the authorized person
$query = "SELECT fullname FROM authorized_persons WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["message" => "Failed to prepare statement."]);
    exit;
}

$stmt->bind_param("i", $authorizedId);
$stmt->execute();
$result = $stmt->get_result();

// Check if the authorized person exists
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $authorizedPersonName = $row['fullname'];

    // Get the current timestamp for the time_out field
    $currentTime = date('Y-m-d H:i:s');

    // Update the attendance table to mark the status as "picked up" and set the time_out
    $updateQuery = "UPDATE attendance SET status = 'picked up', authorized_person_name = ?, time_out = ? 
                    WHERE student_id = ? AND status != 'picked up'"; // Ensures we don't overwrite existing "picked up" status
    $updateStmt = $conn->prepare($updateQuery);

    if (!$updateStmt) {
        echo json_encode(["message" => "Failed to prepare update statement."]);
        exit;
    }

    $updateStmt->bind_param("ssi", $authorizedPersonName, $currentTime, $studentId);
    $updateStmt->execute();

    if ($updateStmt->affected_rows > 0) {
        // Success
        echo json_encode(["message" => "Time-out successfully recorded. Pickup person: $authorizedPersonName"]);
    } else {
        // Failure (if no rows are affected, it means no matching student or already marked "picked up")
        echo json_encode(["message" => "Failed to update attendance. Either student not found or already marked as picked up."]);
    }

    $updateStmt->close();
} else {
    // No authorized person found
    echo json_encode(["message" => "No authorized person found."]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
