<?php
include 'connection.php'; // Include the database connection

// Assuming your `admin_staff` table has `email` and `role` columns and `id` for the admin user
// Define the reset link (You can adjust the URL based on your actual reset password page)
$resetLink = "http://yourwebsite.com/reset_password.php"; 

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email from the form submission
    $email = $_POST['email'];

    // Sanitize email to prevent SQL injection
    $email = $conn->real_escape_string($email);

    // Prepare the SQL query to check if the email exists and if the role is 'admin'
    $stmt = $conn->prepare("SELECT email, role FROM admin_staff WHERE email = ? AND role = 'Admin' OR role='Super Admin'");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameter and execute the query
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the email exists and the role is admin
    if ($stmt->num_rows > 0) {
        // The email exists and the role is admin
        // Send the password reset email

        // Send email (Note: In real-world usage, use an actual mail service)
        $subject = "Admin Password Reset Request";
        $message = "Hello Admin,\n\nPlease click the link below to reset your password:\n\n" . $resetLink;
        $headers = "From: no-reply@yourwebsite.com";

        // Simulate sending the email (use mail() or an email service in a real scenario)
        if (mail($email, $subject, $message, $headers)) {
            echo "<script>alert('A password reset link has been sent to your email!'); window.location.href='forgot_password.php';</script>";
        } else {
            echo "<script>alert('There was an error sending the reset email. Please try again.'); window.location.href='forgot_password.php';</script>";
        }
    } else {
        // If email does not exist or role is not admin
        echo "<script>alert('Invalid email address or you do not have admin privileges.'); window.location.href='forgot_password.php';</script>";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
