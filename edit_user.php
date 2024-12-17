<?php
session_start();
include 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $fullname = $_POST['fullname'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $password = $_POST['password'];

    // Prepare the SQL statement
    if (!empty($password)) {
        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE parent_acc SET fullname = ?, contact_number = ?, email = ?, address = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $fullname, $contact_number, $email, $address, $hashed_password, $user_id);
    } else {
        // If no password is provided, do not include it in the update
        $sql = "UPDATE parent_acc SET fullname = ?, contact_number = ?, email = ?, address = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $fullname, $contact_number, $email, $address, $user_id);
    }

    // Execute the statement
    if ($stmt->execute()) {
        // Return a success message
        echo json_encode(['success' => true, 'message' => 'User  information updated successfully.']);
    } else {
        // Return an error message
        echo json_encode(['success' => false, 'message' => 'Error updating user information: ' . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>