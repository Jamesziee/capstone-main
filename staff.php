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
    <link href="css/staff.css" rel="stylesheet" />
</head>
<body>
<div class="sidebar">
        <img src="logo/logo/logo.png" alt="Logo">
        <a href="attendance.php">
            <i class="fas fa-calendar-check"></i> Attendance
        </a>
        <a href="student.php">
            <i class="fas fa-user-graduate"></i> Student Records
        </a>
        <a href="parent.php">
            <i class="fas fa-users"></i> Parent Records
        </a>
        <a href="staff.php"class="active">
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
        <button class="create-btn">CREATE</button>

    <!-- Popup overlay -->
    <div class="overlay"></div>

    <!-- Popup window -->
    <div class="create-panel">
    <button class="close-btn">&times;</button>
<form id="create-form" action="submit_form_staff.php" method="post" enctype="multipart/form-data">
    <h2>Admin/Staff Account Creation</h2>

    <label for="fullname">Full Name:</label>
    <input type="text" id="fullname" name="fullname" required>

    <label for="contact_number">Contact Number:</label>
    <input type="tel" id="contact_number" name="contact_number" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="address">Address:</label>
    <input type="text" id="address" name="address" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <label for="confirm_password">Confirm Password:</label>
    <input type="password" id="confirm_password" name="confirm_password" required>

    <label for="profile_image">Profile Image:</label>
    <input type="file" id="profile_image" name="profile" accept="image/*">

    <label for="role">Role:</label>
    <select id="role" name="role" required>
        <option value="Super Admin">Super Admin</option>
        <option value="Admin">Admin</option>
        <option value="Teacher">Teacher</option>
        <option value="Staff">Staff</option>
    </select><br><br>

    <button type="submit">Submit</button>
</form>

    </div>
    <script>
   document.addEventListener("DOMContentLoaded", function() {
    const createBtn = document.querySelector('.create-btn');
    const overlay = document.querySelector('.overlay');
    const createPanel = document.querySelector('.create-panel');
    const closeBtn = document.querySelector('.close-btn');

    // Function to open the popup
    function openPopup() {
        overlay.style.display = 'block';
        createPanel.classList.add('show');
    }

    // Function to close the popup
    function closePopup() {
        overlay.style.display = 'none';
        createPanel.classList.remove('show');
    }

    // Event listener for the "CREATE" button
    createBtn.addEventListener('click', openPopup);

    // Event listener for the close button
    closeBtn.addEventListener('click', closePopup);

    // Event listener for the overlay
    overlay.addEventListener('click', closePopup);
});

    </script> 
            <div class="search-bar">
            <input type="text" id="search" placeholder="Search..." onkeyup="performSearch(event)">
            </div>
        </div>
        <?php
include 'connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination variables
$itemsPerPage = 10; // Items per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page from the URL
$offset = ($currentPage - 1) * $itemsPerPage; // Offset for SQL query

// Query to select records from the admin_staff table with LIMIT for pagination
$sql = "SELECT id, fullname, contact_number, email, address, role FROM admin_staff LIMIT $offset, $itemsPerPage";
$result = $conn->query($sql);

// Count total records for pagination
$totalSql = "SELECT COUNT(*) as total FROM admin_staff";
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $itemsPerPage); // Calculate total pages

$userRole = $_SESSION['role'];

// Start the HTML table
echo '<table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact Number</th>
                <th>Email Address</th>
                <th>Address</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>';

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>
                        <td>' . htmlspecialchars($row['fullname']) . '</td>
                        <td>' . htmlspecialchars($row['contact_number']) . '</td>
                        <td>' . htmlspecialchars($row['email']) . '</td>
                        <td>' . htmlspecialchars($row['address']) . '</td>
                        <td>' . htmlspecialchars($row['role']) . '</td>
                        <td>';
        
                // Check if the user is a super admin
                if ($userRole === 'Super Admin') {
                    echo '<i class="fas fa-pen" title="Edit" onclick="location.href=\'edit_staff.php?id=' . $row['id'] . '\'"></i>
                          <i class="fas fa-trash" title="Delete" onclick="location.href=\'delete_staff.php?id=' . $row['id'] . '\'"></i>';
                }
                
                echo '</td>
                      </tr>';
            }
        } else {
            echo '<tr><td colspan="5">No records found</td></tr>';
        }
        
        echo '  </tbody>
              </table>';



// Close the database connection
$conn->close();
?>

            <hr>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>
<script>
    const totalItems = <?php echo $totalItems; ?>; 
const itemsPerPage = <?php echo $itemsPerPage; ?>; 
let currentPage = <?php echo $currentPage; ?>; 

function renderPagination() {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = ''; // Clear previous pagination

    const totalPages = Math.ceil(totalItems / itemsPerPage);

    // Previous button
    const prevLink = document.createElement('a');
    prevLink.innerHTML = '«';
    prevLink.className = currentPage === 1 ? 'disabled' : '';
    prevLink.onclick = function() {
        if (currentPage > 1) {
            currentPage--;
            updatePage();
        }
    };
    pagination.appendChild(prevLink);

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        const pageNumber = document.createElement('div');
        pageNumber.innerHTML = i;
        pageNumber.className = `page-number ${i === currentPage ? 'active' : ''}`;
        pageNumber.onclick = function() {
            currentPage = i;
            updatePage();
        };
        pagination.appendChild(pageNumber);
    }

    // Next button
    const nextLink = document.createElement('a');
    nextLink.innerHTML = '»';
    nextLink.className = currentPage === totalPages ? 'disabled' : '';
    nextLink.onclick = function() {
        if (currentPage < totalPages) {
            currentPage++;
            updatePage();
        }
    };
    pagination.appendChild(nextLink);
}

function updatePage() {
    window.location.href = '?page=' + currentPage; // Redirect to the correct page
}

// Initial rendering
renderPagination();
</script>
<!-- Preloader -->
<div id="preloader" style="display: none;">
    <div class="spinner"></div>
</div>

    <script src="script/script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</body>
</html>
