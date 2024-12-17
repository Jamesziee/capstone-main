<?php
// Include the database connection
include('connection.php');

// Set the response header to inform the client that the response is in JSON format
header('Content-Type: application/json');

// Get the raw POST data
$rawData = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($rawData, true);

// Check if the data was successfully decoded
if ($data === null) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

// Extract the student_id from the decoded JSON
$studentId = $data['studentId'] ?? null;

// Check if the studentId is provided
if (!$studentId) {
    echo json_encode(['success' => false, 'message' => 'Student ID is required']);
    exit;
}

// Prepare the SQL query to insert attendance data
$sql = "INSERT INTO attendance (student_id, time_in, date, status) VALUES (?, NOW(), CURDATE(), 'Present')";
$stmt = $conn->prepare($sql);

// Check if the statement was prepared successfully
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Error preparing the SQL statement']);
    exit;
}

// Bind the parameter to the prepared statement
$stmt->bind_param('i', $studentId); // 'i' for integer type (student_id is an integer)

// Execute the query
if ($stmt->execute()) {
    // Check if the insert was successful
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Attendance recorded successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No rows affected.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error executing query: ' . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
