<?php 
ob_start();

header('Content-Type: application/json');

include 'connection.php';

// Check database connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . htmlspecialchars($conn->connect_error)]);
    exit;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields
    $required_fields = ['fullname', 'contact_number', 'email', 'address', 'password', 'confirm_password'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
            exit;
        }
    }

    // Form data processing
    $fullname = trim($_POST['fullname']);
    $contact_number = trim($_POST['contact_number']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Password mismatch check
    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
        exit;
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Validate and sanitize contact number (only 11 digits)
    if (!preg_match('/^\d{11}$/', $contact_number)) {
        echo json_encode(['success' => false, 'message' => 'Contact number must be exactly 11 digits.']);
        exit;
    }

    // Check if the email is already in the database
    $email_check_sql = "SELECT email FROM parent_acc WHERE email = ?";
    $stmt = $conn->prepare($email_check_sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Database prepare failed: ' . htmlspecialchars($conn->error)]);
        exit;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists. Please use a different email.']);
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Handle image upload
    $image_data = null;
    if (isset($_FILES['parent_image']) && $_FILES['parent_image']['error'] == UPLOAD_ERR_OK) {
        $parent_image = $_FILES['parent_image']['tmp_name'];
        $image_data = file_get_contents($parent_image);
    }

    // Insert parent data into the database
    $parent_sql = "INSERT INTO parent_acc (fullname, contact_number, email, address, parent_image, password) 
                   VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($parent_sql);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Database prepare failed: ' . htmlspecialchars($conn->error)]);
        exit;
    }

    $stmt->bind_param("ssssss", $fullname, $contact_number, $email, $address, $image_data, $password_hash);

    if ($stmt->execute()) {
        $parent_id = $stmt->insert_id;
        echo json_encode(['success' => true, 'message' => 'Parent information added successfully!', 'parent_id' => $parent_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error inserting parent information: ' . htmlspecialchars($stmt->error)]);
    }

    $stmt->close();
}

$conn->close();

ob_end_flush();
?>
