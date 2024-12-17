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
    <link href="css/student.css" rel="stylesheet" />

</head>
<body>
<div class="sidebar">
        <img src="logo/logo/logo.png" alt="Logo">
        <a href="attendance.php">
            <i class="fas fa-calendar-check"></i> Attendance
        </a>
        <a href="student.php"class="active">
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
        <?php
// Include database connection
include 'connection.php';

// Fetch teachers from admin_staff table where the role is "teacher"
$teachers_sql = "SELECT id, fullname FROM admin_staff WHERE role = 'teacher'";
$teachers_result = $conn->query($teachers_sql);

// Fetch parent names from parent_acc table
$parents_sql = "SELECT id, fullname FROM parent_acc";
$parents_result = $conn->query($parents_sql);

// Fetch authorized persons from authorized_persons table
$authorized_persons_sql = "SELECT id, fullname FROM authorized_persons";
$authorized_persons_result = $conn->query($authorized_persons_sql);

// Check for database connection errors
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . htmlspecialchars($conn->connect_error)]));
}
?>
        <div class="table-container">
            <div class="search-bar-container">
            <button class="create-btn" id="create-btn">CREATE</button>

<!-- Overlay -->
<div class="overlay" id="overlay"></div>

<!-- Child Form Modal -->
<div class="create-panel" id="create-panel">
    <button class="close-btn" id="close-btn">&times;</button>
    
    <form id="child-form" action="child_submit_form.php" method="post" enctype="multipart/form-data">
    <h2>Create Child Record</h2>

    <!-- Parent ID -->
    <div>
        <label for="parent_id">Parent ID:</label>
        <select name="parent_id" id="parent_id" required onchange="fetchAuthorizedPersons()">
            <option value="">Select Parent</option>
            <?php while ($row = $parents_result->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['fullname']; ?></option>
            <?php } ?>
        </select>
    </div>

    <!-- Child Name -->
    <div>
        <label for="child_name">Child Name:</label>
        <input type="text" name="child_name" id="child_name" required>
    </div>

    <!-- Student ID -->
    <div>
        <label for="student_id">Student ID:</label>
        <input type="text" name="student_id" id="student_id" required>
    </div>

    <!-- Child Teacher -->
    <div>
        <label for="child_teacher">Teacher Name:</label>
        <select name="child_teacher" id="child_teacher" required>
            <option value="">Select Teacher</option>
            <?php while ($row = $teachers_result->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['fullname']; ?></option>
            <?php } ?>
        </select>
    </div>

    <!-- Child Age -->
    <div>
        <label for="child_age">Child Age:</label>
        <input type="number" name="child_age" id="child_age" required>
    </div>

    <!-- Grade -->
    <div>
        <label for="child_grade">Grade:</label>
        <input type="text" name="child_grade" id="child_grade" required>
    </div>

    <!-- Section -->
    <div>
        <label for="child_section">Section:</label>
        <input type="text" name="child_section" id="child_section" required>
    </div>

    <!-- Address -->
    <div>
        <label for="child_address">Address:</label>
        <textarea name="child_address" id="child_address" required></textarea>
    </div>

    <!-- Image Upload -->
    <div>
        <label for="child_image">Child Image:</label>
        <input type="file" name="child_image" id="child_image" accept="image/*" required>
    </div>

    <!-- Authorized Person -->
    <div>
        <label for="authorized_person">Authorized Person:</label>
        <select name="authorized_person" id="authorized_person" required>
            <option value="">Select Authorized Person</option>
        </select>
    </div>
    <!-- Submit Button -->
    <button type="submit" id="submit" class="submit-btn">Create</button>
</form>

</div>

<script>
  // JavaScript function to fetch authorized persons dynamically
  function fetchAuthorizedPersons() {
        const parent_id = document.getElementById('parent_id').value;
        
        // Ensure the parent_id is selected before making the request
        if (parent_id) {
            fetch('get_authorized_persons.php?parent_id=' + parent_id)
                .then(response => response.json())
                .then(data => {
                    // Get the 'authorized_person' select element
                    const authorizedPersonSelect = document.getElementById('authorized_person');
                    authorizedPersonSelect.innerHTML = ''; // Clear existing options
                    
                    // Add a default "Select Authorized Person" option
                    authorizedPersonSelect.innerHTML += '<option value="">Select Authorized Person</option>';
                    
                    // Check if the response contains authorized persons
                    if (data && data.length > 0) {
                        data.forEach(person => {
                            const option = document.createElement('option');
                            option.value = person.id;
                            option.text = person.fullname;
                            authorizedPersonSelect.appendChild(option);
                        });
                    } else {
                        authorizedPersonSelect.innerHTML += '<option value="">No authorized persons found</option>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching authorized persons:', error);
                });
        } else {
            // Clear the dropdown if no parent is selected
            document.getElementById('authorized_person').innerHTML = '<option value="">Select Authorized Person</option>';
        }
    }


document.addEventListener('DOMContentLoaded', () => {
    const createBtn = document.getElementById('create-btn');
    const closeBtn = document.getElementById('close-btn');
    const overlay = document.getElementById('overlay');
    const createPanel = document.getElementById('create-panel');
    const childForm = document.getElementById('child-form');
    const submitBtn = document.getElementById('submit');
    const loadingOverlay = document.getElementById('loading-overlay'); 
    
    // Open the modal when 'CREATE' button is clicked
    createBtn.addEventListener('click', () => {
        createPanel.style.display = 'block';
        overlay.style.display = 'block';
    });
    
    // Close the modal when 'close' button or overlay is clicked
    closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', closeModal);
    
    function closeModal() {
        createPanel.style.display = 'none';
        overlay.style.display = 'none';
    }
    
    // Handle the form submission using AJAX
    childForm.addEventListener('submit', function(event) {
        event.preventDefault();  // Prevent default form submission

        const formData = new FormData(childForm);
        
        // Disable the submit button to prevent multiple submissions
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';

        // Show the loading overlay
        loadingOverlay.style.display = 'flex';

        // Use the Fetch API to send the form data
        fetch(childForm.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text()) // Read response as text first for debugging
        .then(text => {
            console.log('Raw server response:', text);  // Log raw response to check its content
            try {
                const data = JSON.parse(text);  // Attempt to parse JSON
                if (data.success) {
                    alert(data.message); // Show success message
                    closeModal();  // Close the modal after success
                    location.reload();  // Reload the page to reflect the new record
                } else {
                    alert(data.message); // Show error message
                }
            } catch (error) {
                alert('Invalid JSON response from server');
                console.error(error);
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Create';
            alert('An error occurred: ' + error.message);
        })
        .finally(() => {
            // Hide the loading overlay when the AJAX request is complete
            loadingOverlay.style.display = 'none';
            // Re-enable the submit button
            submitBtn.disabled = false;
            submitBtn.textContent = 'Create';
        });
    });
});

</script>

<style>
/* Overlay styles */
.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;  /* Hidden by default */
    z-index: 999;
}

/* Create panel (modal) styles */
.create-panel {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #ffffff;
    padding: 30px;
    border-radius: 12px; /* More rounded corners */
    width: 400px;
    max-width: 90%;
    max-height: 80vh; /* Ensures the modal does not exceed 80% of the viewport height */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Softer, larger shadow */
    display: none;  /* Hidden by default */
    z-index: 1000;
    overflow-y: auto; /* Enables vertical scrolling */
    animation: fadeIn 0.4s ease-out; /* Smooth fade-in animation */
}

/* Close button (×) styles */
.close-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    font-size: 32px;
    color: #7c3aed; /* Icon color to match branding */
    cursor: pointer;
    transition: color 0.3s;
}

.close-btn:hover {
    color: #5a28b8; /* Hover color for close button */
}

/* Form input styles */
form input, form select, form textarea, form button {
    width: 100%;
    padding: 12px;
    margin: 12px 0;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
    box-sizing: border-box; /* Ensure padding doesn't affect width */
    transition: border-color 0.3s ease; /* Smooth transition on focus */
}

/* Form header styling */
form h2 {
    margin-bottom: 25px;
    text-align: center;
    color: #333;
    font-size: 22px;
    font-weight: bold;
    letter-spacing: 1px; /* Slightly spaced letters for style */
}

/* Button styles */
.create-btn {
    background-color: #7c3aed;
    color: white;
    font-size: 16px;
    cursor: pointer;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    transition: background-color 0.3s, transform 0.2s;
    margin-bottom: 3px;
}

.create-btn:hover {
    background-color: #5a28b8;
    transform: translateY(-2px); /* Subtle lift effect */
}

/* Submit button styles */
.submit-btn {
    background-color: #7c3aed;
    color: white;
    font-size: 16px;
    cursor: pointer;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    transition: background-color 0.3s, transform 0.2s;
}

.submit-btn:hover {
    background-color: #5a28b8;
    transform: translateY(-2px); /* Subtle lift effect */
}

/* Hover styles for inputs */
input:hover, select:hover, textarea:hover {
    border-color: #7c3aed;
}

/* Focus styles for inputs */
input:focus, select:focus, textarea:focus {
    border-color: #7c3aed;
    outline: none;
}

/* Custom Scrollbar Styling (Optional) */
.create-panel::-webkit-scrollbar {
    width: 8px;  /* Set the width of the scrollbar */
}

.create-panel::-webkit-scrollbar-thumb {
    background-color: #7c3aed;
    border-radius: 10px;
}

.create-panel::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

/* Animation for Modal */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

</style>
            
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
$sql = "SELECT id, child_name, student_id, child_grade, child_section, child_address FROM child_acc LIMIT $offset, $itemsPerPage";
$result = $conn->query($sql);

// Count total records for pagination
$totalSql = "SELECT COUNT(*) as total FROM parent_acc";
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $itemsPerPage); // Calculate total pages

$userRole = $_SESSION['role'];
// Start the HTML table
echo '<table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Grade</th>
                <th>Section</th>
                <th>Address</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>';

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>
                        <td>' . htmlspecialchars($row['student_id']) . '</td>
                        <td>' . htmlspecialchars($row['child_name']) . '</td>
                        <td>' . htmlspecialchars($row['child_grade']) . '</td>
                        <td>' . htmlspecialchars($row['child_section']) . '</td>
                        <td>' . htmlspecialchars($row['child_address']) . '</td>
                        <td>';
        
                // Check if the user is a super admin
                if ($userRole === 'Super Admin') {
                    echo '<i class="fas fa-pen" title="Edit" onclick="location.href=\'edit_staff.php?id=' . $row['id'] . '\'"></i>
                          <i class="fas fa-trash" title="Delete" onclick="location.href=\'delete_staff.php?id=' . $row['id'] . '\'"></i>';
                }
        
                // Always show the View button
                echo '<i class="fas fa-eye" title="View" data-parent-id="' . $row['id'] . '" onclick="showChildInfo(this.dataset.parentId)"></i>';
                
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
        <hr/>
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
    <script src="script/script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Loading Overlay (for showing when the page is reloading or submitting) -->
<div class="loading-overlay" id="loading-overlay">
    <div class="loader"></div>
</div>

<style>
    /* Overlay for loading screen */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none; /* Hidden by default */
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

/* Loader animation (a simple spinner) */
.loader {
    border: 5px solid #f3f3f3; /* Light background */
    border-top: 5px solid blue; 
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
}

/* Keyframes for spinning loader */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
i {
    cursor: pointer; /* Changes the cursor to a pointer */
    margin: 0 5px; /* Optional: Adds spacing between icons */
    transition: transform 0.2s; /* Optional: Adds a hover effect */
}

i:hover {
    transform: scale(1.1); /* Optional: Slightly enlarges the icon on hover */
}

</style>

</body>
</html>
