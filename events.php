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
    <link href="css/events.css" rel="stylesheet" />

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
        <a href="staff.php">
            <i class="fas fa-user-tie"></i> Admin/Staff Records
        </a>
        <a href="pick_up_records.php">
            <i class="fas fa-clipboard-list"></i> Pick-Up Records
        </a>
        <a href="events.php"class="active">
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
    <form id="create-form" action="submit_events.php" method="post" enctype="multipart/form-data">
        <h2>Event Creation</h2>
        
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br><br>

        <label for="byline">Byline:</label>
        <input type="tel" id="byline" name="byline" required><br><br>

        <label for="paragraph">Paragraph:</label>
        <input type="text" id="paragraph" name="paragraph" required><br><br>

        <label for="date" class="date-label">Date:</label>
        <input type="date" id="date" name="date" class="date-input" required><br><br>

        <label for="picture" class="picture-label">Upload Picture:</label>
        <input type="file" id="picture" name="picture" class="picture" accept="image/*" required><br><br>

        <div id="picture-preview" style="display:none;">
            <label>Preview:</label><br>
            <img id="preview-img" src="" alt="Image Preview" width="200"><br><br>
        </div>

        <button type="submit">Submit</button>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const createBtn = document.querySelector('.create-btn');
        const overlay = document.querySelector('.overlay');
        const createPanel = document.querySelector('.create-panel');
        const closeBtn = document.querySelector('.close-btn');
        const pictureInput = document.querySelector('#picture');
        const picturePreview = document.querySelector('#picture-preview');
        const previewImg = document.querySelector('#preview-img');

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

        // Event listener for picture upload to show a preview
        pictureInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();

                // Display the preview when file is selected
                reader.onload = function(e) {
                    picturePreview.style.display = 'block'; // Show the preview div
                    previewImg.src = e.target.result; // Set image source
                };
                reader.readAsDataURL(file);
            } else {
                picturePreview.style.display = 'none'; // Hide preview if no file selected
            }
        });
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

// Query to select records from the events table with LIMIT for pagination
$sql = "SELECT id, title, byline, paragraph, date FROM events LIMIT $offset, $itemsPerPage";
$result = $conn->query($sql);

// Count total records for pagination
$totalSql = "SELECT COUNT(*) as total FROM events";
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];

echo '<table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Byline</th>
                <th>Paragraph</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . htmlspecialchars($row['title']) . '</td>
                <td>' . htmlspecialchars($row['byline']) . '</td>
                <td>' . htmlspecialchars($row['paragraph']) . '</td>
                <td>' . htmlspecialchars($row['date']) . '</td>
                <td>
                    <i class="fas fa-pen" title="Edit" onclick="location.href=\'edit_event.php?id=' . $row['id'] . '\'"></i>
                    <i class="fas fa-trash" title="Delete" onclick="location.href=\'delete_event.php?id=' . $row['id'] . '\'"></i>
                </td>
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
    const totalItems = <?php echo $totalItems; ?>; // Total number of items from PHP
const itemsPerPage = <?php echo $itemsPerPage; ?>; // Items per page from PHP
let currentPage = <?php echo $currentPage; ?>; // Current page from PHP

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
    <script src="script/script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</body>
</html>

