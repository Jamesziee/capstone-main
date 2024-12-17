<?php
session_start(); // Start the session
include 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Retrieve user information from the session
$fullname = $_SESSION['fullname'];
$role = $_SESSION['role'];
$user_id = $_SESSION['id']; // Get the logged-in user's ID

$sql = "SELECT profile_image FROM admin_staff WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind the user ID to the query
$stmt->execute();
$stmt->bind_result($profile_image);
$stmt->fetch();
$stmt->close();

// If the profile image is empty, you can use a default image
if (empty($profile_image)) {
    $profile_image = 'path/to/default-image.jpg'; // Provide the path to a default image
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Records</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <link href="css/attendance.css" rel="stylesheet" />
</head>
<body>
    <div class="sidebar">
        <img src="logo/logo/logo.png" alt="Logo">
        <a href="attendance.php" class="active">
            <i class="fas fa-calendar-check"></i> Attendance
        </a>
        <a href="student.php">
            <i class="fas fa-user-graduate"></i> Student Records
        </a>
        <a href="parent.php">
            <i class="fas fa-users"></i> Parent Records
        </a>
        <a href="staff.php">
            <i class="fas fa-user-tie"></i> Admin/Staff Records
        </a>
        <a href="pick_up_records.php">
            <i class="fas fa-clipboard-list"></i> Pick-Up Records
        </a>
        <a href="events.php">
            <i class="fas fa-calendar-alt"></i> Events
        </a>
        <div class="bottom-links">
            <a href="#">
                <i class="fas fa-cog"></i> Settings
            </a>
            <a href="#">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    <div class="main-content">
            <div class="header">
                <div class="user-info">
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="vertical-rule"></div>
                    <div class="profile">
                        <!-- Dynamically display the profile image -->
                        <?php if (empty($profile_image)) : ?>
                            <img alt="User profile picture" height="40" src="<?php echo htmlspecialchars($profile_image); ?>" width="40"/>
                        <?php else: ?>
                            <img alt="User profile picture" height="40" src="data:image/jpeg;base64,<?php echo base64_encode($profile_image); ?>" width="40"/>
                        <?php endif; ?>
                        <div class="profile-text">
                            <span><?php echo htmlspecialchars($_SESSION['role']); ?></span><br>
                            <span><?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
    <hr/>
        <div class="table-container">
        <div class="search-bar-container">
</div>

<!-- Attendance table -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Grade</th>
                <th>Section</th>
                <th>Date</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php
        include 'connection.php';
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to get data from the attendance and child_acc tables
$sql = "
    SELECT
        c.child_name AS student_name,
        c.child_grade AS grade,
        c.child_section AS section,
        a.date AS date,
        a.time_in AS time_in,
        a.time_out AS time_out,
        a.status AS status
    FROM
        attendance a
    INNER JOIN
        child_acc c
    ON
        a.student_id = c.student_id
";

// Execute query and get result
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['student_name'] . "</td>";
        echo "<td>" . $row['grade'] . "</td>";
        echo "<td>" . $row['section'] . "</td>";
        echo "<td>" . $row['date'] . "</td>";
        echo "<td>" . $row['time_in'] . "</td>";
        echo "<td>" . $row['time_out'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No records found</td></tr>";
}

// Close connection
$conn->close();
?>




        </tbody>
    </table>
</div>





</body>

</html>