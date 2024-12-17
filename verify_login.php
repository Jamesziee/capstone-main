<?php
include 'connection.php'; // Database connection file

// Check if connection to the database is successful
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . htmlspecialchars($conn->connect_error)]));
}

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the submitted email and password
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Sanitize email to avoid SQL Injection
    $email = $conn->real_escape_string($email);

    // Prepare the SQL query to fetch user details by email
    $sql = "SELECT id, password, fullname, role FROM admin_staff WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if a user with the given email exists
    if ($stmt->num_rows > 0) {
        // Bind the result
        $stmt->bind_result($id, $hashedPassword, $fullname, $role); // Fetch the hashed password, fullname, and role
        $stmt->fetch();

        // Compare the input password with the hashed password from the database
        if (password_verify($password, $hashedPassword)) {
            // Check if the role is 'admin' or 'super admin'
            if ($role === 'Admin' || $role === 'Super Admin') {
                // Password is correct, role is valid, user can be logged in
                session_start();

                // Store user info in session
                $_SESSION['id'] = $id;
                $_SESSION['fullname'] = $fullname;
                $_SESSION['role'] = $role;

                // Redirect to the parent page or dashboard
                header("Location: parent.php");
                exit();
            } else {
                // Invalid role
                echo "<script>alert('You do not have the required permissions to access this page.'); window.location.href='login.php';</script>";
            }
        } else {
            // Invalid password
            echo "<script>alert('Invalid password'); window.location.href='login.php';</script>";
        }
    } else {
        // No user found with the given email
        echo "<script>alert('No user found with this email'); window.location.href='login.php';</script>";
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
