<?php
// Include the database connection file
include 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $fullname = $_POST['fullname'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password']; // This is the confirm password field
    $role = $_POST['role'];

    // Handle profile image upload
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] == 0) {
        // Get the file data
        $image = $_FILES['profile'];

        // Ensure the file is an image (You can also validate the type further)
        $imageType = mime_content_type($image['tmp_name']);
        if (strpos($imageType, 'image') !== false) {
            // Read the image file into binary data
            $imageData = file_get_contents($image['tmp_name']);
        } else {
            // JavaScript alert for invalid file type
            echo "<script>alert('Invalid file type. Please upload an image.'); window.location.href = 'staff.php';</script>";
            exit();
        }
    } else {
        // JavaScript alert for missing profile image
        echo "<script>alert('Profile image is required.'); window.location.href = 'staff.php';</script>";
        exit();
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        // JavaScript alert for mismatched passwords
        echo "<script>alert('Passwords do not match!'); window.location.href = 'staff.php';</script>";
        exit();
    }

    // Check if the email is already in the database
    $stmt = $conn->prepare("SELECT email FROM admin_staff WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // JavaScript alert for duplicate email
        echo "<script>alert('Email already exists. Please use a different email.'); window.location.href = 'staff.php';</script>";
        exit();
    }

    // Email is unique, proceed with insertion
    $stmt->close();

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL query to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO admin_staff (fullname, contact_number, email, address, password, role, profile_image) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $fullname, $contact_number, $email, $address, $hashed_password, $role, $imageData);

    // Execute the prepared statement
    if ($stmt->execute()) {
        // JavaScript alert for success
        echo "<script>alert('Staff member added successfully!'); window.location.href = 'staff.php';</script>";
        exit();
    } else {
        // JavaScript alert for SQL error
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href = 'staff.php';</script>";
        exit();
    }

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();
}
?>
