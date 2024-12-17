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
    <link href="css/parent.css" rel="stylesheet" />

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
        <a href="parent.php" class="active">
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
        <button class="create-btn" id="create-btn">CREATE</button>
<!-- Parent form modal and Authorized Info form -->
 
<div class="overlay" id="overlay">

    <div class="create-panel" id="create-panel">
        <button class="close-btn" id="close-btn">&times;</button>
        <form id="parent-form" action="submit_form.php" method="post" enctype="multipart/form-data">
            <h2>Parent Account Creation</h2>
            <!-- Form Fields -->
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" required>
            <label for="contactnumber">Contact Number:</label>
            <input type="tel" id="contactnumber" name="contact_number" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <label for="parentimage">Parent's Image:</label>
            <input type="file" id="parentimage" name="parent_image" accept="image/*" required>
            <button type="submit">Submit</button>
        </form>
    </div>

    <!-- Authorized Info Form -->
    <div id="authorized-info" class="authorized-info hidden">
        <form id="authorized-form" action="authorized_submit_form.php" method="post" enctype="multipart/form-data">
            <h3>Authorized Pick-Up Person</h3>
            <!-- Form Fields -->
            <label for="authorized_fullname">Full Name:</label>
            <input type="text" id="authorized_fullname" name="authorized_fullname" required>

            <label for="authorized_address">Address:</label>
            <input type="text" id="authorized_address" name="authorized_address" required>

            <label for="authorized_age">Age:</label>
            <input type="number" id="authorized_age" name="authorized_age" required>

            <label for="authorized_image">Upload Authorized Image:</label>
            <input type="file" id="authorized_image" name="authorized_image" accept="image/*" required>

            <button type="submit">Submit Authorized Pick-Up</button>
        </form>
    </div>
</div>
<script>
// Show the loader
function showLoader() {
    document.getElementById('loader').style.display = 'flex'; // Ensure loader exists
}

// Hide the loader
function hideLoader() {
    document.getElementById('loader').style.display = 'none'; // Ensure loader is hidden properly
}

// Event listener for "CREATE" button
document.getElementById('create-btn').addEventListener('click', function() {
    document.getElementById('overlay').style.display = 'block';
    document.getElementById('create-panel').classList.add('show');
});

// Event listener for "CLOSE" button
document.getElementById('close-btn').addEventListener('click', function() {
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('create-panel').classList.remove('show');
    document.getElementById('parent-form').reset();
});

// Handle Parent Form Submission
document.getElementById('parent-form').addEventListener('submit', async function(event) {
    event.preventDefault(); // Prevent default form submission

    showLoader(); // Show loader while processing

    const formData = new FormData(this); // Collect form data

    try {
        const response = await fetch('submit_form.php', {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json(); // Parse JSON response
        console.log(data); // Add console log for debugging

        if (data.success) {
            // Successfully created the parent account
            alert(data.message || 'Parent account created successfully.');

            // Hide the Parent Form modal
            document.getElementById('create-panel').classList.remove('show');
            document.getElementById('overlay').style.display = 'none';  // Hide overlay

            // Reset the parent form
            this.reset();

            // Show the Authorized Info Form
            const authorizedInfo = document.getElementById('authorized-info');
            const overlay = document.getElementById('overlay');

            // Ensure the form and overlay are visible
            if (authorizedInfo) {
                authorizedInfo.classList.remove('hidden'); // Remove hidden class
                authorizedInfo.style.display = 'block'; // Ensure it's shown correctly
                console.log('Authorized Info Form should be visible now.');
            } else {
                console.error('Authorized Info Form not found!');
            }

            if (overlay) {
                overlay.style.display = 'block'; // Show the overlay
                console.log('Overlay should be visible now.');
            } else {
                console.error('Overlay not found!');
            }

            // Append the Parent ID to the Authorized Info Form as a hidden field
            const parentIdInput = document.createElement('input');
            parentIdInput.type = 'hidden';
            parentIdInput.name = 'parent_id';
            parentIdInput.value = data.parent_id;
            document.getElementById('authorized-form').appendChild(parentIdInput);

            // Scroll to the Authorized Info Form (optional)
            authorizedInfo.scrollIntoView({ behavior: 'smooth' });

        } else {
            alert(data.message || 'An error occurred while creating the parent account.');
            // Redirect to parent PHP page if parent creation failed
            window.location.href = 'parent.php'; // Replace with the correct URL for the parent page
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to create parent account. Please try again.');
        // Redirect to parent PHP page if there was an error in the parent form submission
        window.location.href = 'parent.php'; // Replace with the correct URL for the parent page
    } finally {
        hideLoader(); // Hide loader
    }
});

// Handle Authorized Info Form Submission (Assuming similar structure)
document.getElementById('authorized-form').addEventListener('submit', async function(event) {
    event.preventDefault(); // Prevent default form submission

    showLoader(); // Show loader while processing

    const formData = new FormData(this); // Collect form data

    try {
        const response = await fetch('authorized_submit_form.php', {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json(); // Parse JSON response
        console.log(data); // Add console log for debugging

        if (data.success) {
            // Successfully created the authorized info
            alert(data.message || 'Authorized person info saved successfully.');

            // Redirect to the parent PHP page after successful authorized info submission
            window.location.href = 'parent.php'; // Replace with the correct URL for the parent page
        } else {
            alert(data.message || 'An error occurred while saving authorized person info.');
            // Redirect to parent PHP page if the authorized person creation failed
            window.location.href = 'parent.php'; // Replace with the correct URL for the parent page
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to save authorized person info. Please try again.');
        // Redirect to parent PHP page if there was an error in the authorized info submission
        window.location.href = 'parent.php'; // Replace with the correct URL for the parent page
    } finally {
        hideLoader(); // Hide loader
    }
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
$currentPage = max(1, $currentPage); // Ensure current page is at least 1
$offset = ($currentPage - 1) * $itemsPerPage; // Offset for SQL query

$userRole = $_SESSION['role']; 

// Prepare the query for pagination
$stmt = $conn->prepare("SELECT id, fullname, contact_number, email, address FROM parent_acc LIMIT ?, ?");
$stmt->bind_param("ii", $offset, $itemsPerPage);
$stmt->execute();
$result = $stmt->get_result();

// Count total records for pagination
$totalResult = $conn->query("SELECT COUNT(*) as total FROM parent_acc");
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $itemsPerPage); // Calculate total pages

// HTML Table
echo '<table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact Number</th>
                <th>Email Address</th>
                <th>Address</th>
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
                <td>';

        // Check if the user is a super admin
        if ($userRole === 'Super Admin') {
            echo '<i class="fas fa-pen" title="Edit" onclick="openEditModal(' . $row['id'] . ', \'' . htmlspecialchars($row['fullname']) . '\', \'' . htmlspecialchars($row['contact_number']) . '\', \'' . htmlspecialchars($row['email']) . '\', \'' . htmlspecialchars($row['address']) . '\')"></i>
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
$stmt->close();
$conn->close();
?>
<div class="overlay" id="edit-overlay" style="display: none;">
    <div class="edit-panel" id="edit-panel">
        <button class="close-btn" id="edit-close-btn">&times;</button>
        <form id="edit-form" action="edit_user.php" method="post">
            <h2>Edit User Information</h2>
            <input type="hidden" id="edit_user_id" name="user_id">
            <label for="edit_fullname">Full Name:</label>
            <input type="text" id="edit_fullname" name="fullname" required>
            <label for="edit_contactnumber">Contact Number:</label>
            <input type="tel" id="edit_contactnumber" name="contact_number" required>
            <label for="edit_email">Email:</label>
            <input type="email" id="edit_email" name="email" required>
            <label for="edit_address">Address:</label>
            <input type="text" id="edit_address" name="address" required>
            <label for="edit_password">Password:</label>
            <input type="password" id="edit_password" name="password">
            <label for="edit_confirm_password">Confirm Password:</label>
            <input type="password" id="edit_confirm_password" name="confirm_password">
            <button type="submit">Update</button>
        </form>
    </div>
</div>
<div id="childInfoModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h3>Student Information</h3>
            <div class="student-info">
                
                <div class="button-group">
                    <button class="edit-btn">Edit Info</button>
                </div>
            </div>
        </div>
    </div>



<script>
    document.getElementById('edit-form').addEventListener('submit', async function(event) {
    event.preventDefault(); // Prevent default form submission

    showLoader(); // Show loader while processing

    const formData = new FormData(this); // Collect form data

    try {
        const response = await fetch('edit_user.php', {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json(); // Parse JSON response
        console.log(data); // Add console log for debugging

        if (data.success) {
            // Successfully updated the user information
            alert(data.message || 'User  information updated successfully.');

            // Hide the Edit Form modal
            document.getElementById('edit-overlay').style.display = 'none';
            document.getElementById('edit-form').reset();

            // Optionally, refresh the page or update the table to reflect changes
            window.location.reload(); // Reload the page to see the updated information
        } else {
            alert(data.message || 'An error occurred while updating user information.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to update user information. Please try again.');
    } finally {
        hideLoader(); // Hide loader
    }
});
    function openEditModal(userId, fullname, contactNumber, email, address) {
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('edit_fullname').value = fullname;
    document.getElementById('edit_contactnumber').value = contactNumber;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_address').value = address;

    document.getElementById('edit-overlay').style.display = 'block';
}

document.getElementById('edit-close-btn').addEventListener('click', function() {
    document.getElementById('edit-overlay').style.display = 'none';
    document.getElementById('edit-form').reset();
});

// Event listener for outside click
window.onclick = function(event) {
    const modal = document.getElementById('edit-overlay');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
};
function openModal() {
    const modal = document.getElementById('childInfoModal');
    modal.style.display = 'block'; // Set the modal to display
}

function closeModal() {
    const modal = document.getElementById('childInfoModal');
    modal.style.display = 'none'; // Hide the modal
}

// Event listener for outside click
window.onclick = function(event) {
    const modal = document.getElementById('childInfoModal');
    if (event.target === modal) {
        closeModal();
    }
};

// Debug fetch example
function showChildInfo(parentId) {
    console.log('Fetching data for parent ID:', parentId);
    fetch(`child_info.php?parent_id=${parentId}`)
        .then(response => response.text())
        .then(html => {
            const container = document.querySelector('.student-info');
            container.innerHTML = html || '<p>No data available</p>';
            openModal();
        })
        .catch(err => {
            console.error('Error fetching child info:', err);
            alert('Failed to load child information.');
        });
}


</script>


            <div class="pagination" id="pagination"></div>
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
            </div>
        </div>
    </div>

    <script src="script/script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<style>
/* Loader style */
.loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.spinner {
    border: 8px solid #f3f3f3; /* Light grey */
    border-top: 8px solid #3498db; /* Blue */
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 2s linear infinite;
}


</style>
<!-- Loading Spinner -->
<div id="loader" class="loader" style="display: none;">
    <div class="spinner"></div>
</div>



</body>
</html>
