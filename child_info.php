<?php
include 'connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
ob_start();

$parent_id = isset($_GET['parent_id']) ? intval($_GET['parent_id']) : 0;

// Validate parent ID
if ($parent_id <= 0) {
    echo "Invalid parent ID.";
    exit;
}

try {
    // Check if parent_id exists in parent_acc
    $sql_check = "SELECT id FROM parent_acc WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $parent_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows === 0) {
        echo "Parent ID not found.";
        exit;
    }
    $stmt_check->close();

    // Fetch child information
    $sql = "SELECT c.*, a.fullname AS child_teacher
            FROM child_acc c 
            LEFT JOIN admin_staff a ON c.child_teacher = a.id 
            WHERE c.parent_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $parent_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "No child found for this parent.";
        exit;
    }

    $child = $result->fetch_assoc();
    $stmt->close();

    // Prepare image data
    if (!empty($child['child_image'])) {
        $image_data = base64_encode($child['child_image']);
        $image_src = 'data:image/jpeg;base64,' . $image_data; // Change 'jpeg' to the correct format if necessary
    } else {
        $image_src = '/placeholder.svg?height=100&width=100';
    }

    // Prepare HTML response
    $html = "
<div class='student-info'>
    <div class='student-photo'>
        <img src='" . htmlspecialchars($image_src) . "' alt='Child Image' class='child-image'>
    </div>
    <div class='info-field'>
        <label>Child Name</label>
        <input type='text' value='" . htmlspecialchars($child['child_name']) . "' readonly>
    </div>
    <div class='info-field'>
        <label>Student ID</label>
        <input type='text' value='" . htmlspecialchars($child['id']) . "' readonly>
    </div>
    <div class='info-field'>
        <label>Section</label>
        <input type='text' value='" . htmlspecialchars($child['child_section']) . "' readonly>
    </div>
    <div class='info-field'>
        <label>Grade</label>
        <input type='text' value='" . htmlspecialchars($child['child_grade']) . "' readonly>
    </div>
    <div class='info-field'>
        <label>Age</label>
        <input type='text' value='" . htmlspecialchars($child['child_age']) . "' readonly>
    </div>
    <div class='info-field'>
        <label>Address</label>
        <input type='text' value='" . htmlspecialchars($child['child_address']) . "' readonly>
    </div>
    <div class='info-field'>
        <label>Adviser</label>
        <input type='text' value='" . htmlspecialchars($child['child_teacher']) . "' readonly>
    </div>
    <div class='button-group'>
                    <button class='edit-btn'>Edit Info</button>
                </div>
</div>
";

echo $html;

} catch (Exception $e) {
    echo "An error occurred: " . htmlspecialchars($e->getMessage());
} finally {
    $conn->close();
}

// End output buffering and flush
ob_end_flush();
?>
