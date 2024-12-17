<?php

ob_start();

header('Content-Type: application/json');

include 'connection.php';

// Check for database connection errors
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . htmlspecialchars($conn->connect_error)]);
    exit;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields (including image)
    $required_fields = ['parent_id', 'authorized_fullname', 'authorized_address', 'authorized_age'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
            exit;
        }
    }

    // Validate that an image is uploaded and is valid
    if (!isset($_FILES['authorized_image']) || $_FILES['authorized_image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Image is required. Please upload an image file.']);
        exit;
    }

    // Validate image file type (only allowing jpg, jpeg, png)
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $image_extension = strtolower(pathinfo($_FILES['authorized_image']['name'], PATHINFO_EXTENSION));

    if (!in_array($image_extension, $allowed_extensions)) {
        echo json_encode(['success' => false, 'message' => 'Invalid image format. Please upload a JPG or PNG image.']);
        exit;
    }

    // Handle the image file
    $image_tmp_name = $_FILES['authorized_image']['tmp_name'];
    
    // Read the image into a binary string (BLOB)
    $authorized_image_data = file_get_contents($image_tmp_name);

    // Validate if the image was read correctly
    if ($authorized_image_data === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to read image file.']);
        exit;
    }

    // Form data processing
    $parent_id = intval($_POST['parent_id']);  // Ensure parent_id is properly sanitized
    $fullname = trim($_POST['authorized_fullname']);
    $address = trim($_POST['authorized_address']);
    $age = trim($_POST['authorized_age']);

    // Validate that all fields are filled
    if (empty($fullname) || empty($address) || empty($age)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    // Insert authorized pick-up person data into the database
    $authorized_sql = "INSERT INTO authorized_persons (parent_id, fullname, address, age, authorized_image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($authorized_sql);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Database prepare failed: ' . htmlspecialchars($conn->error)]);
        exit;
    }

    $stmt->bind_param("issss", $parent_id, $fullname, $address, $age, $authorized_image_data);

    if ($stmt->execute()) {
        // Successful insertion, return success message
        echo json_encode(['success' => true, 'message' => 'Authorized pick-up person added successfully.']);
        exit;
    } else {
        // Error during insertion
        echo json_encode(['success' => false, 'message' => 'Error inserting authorized pick-up person: ' . htmlspecialchars($stmt->error)]);
        exit;
    }

    $stmt->close();
}

// Close the database connection
$conn->close();

ob_end_flush();
?>
