<?php
include 'connection.php';
require_once 'phpqrcode/qrlib.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parent_id = trim($_POST['parent_id']);
    $child_name = trim($_POST['child_name']);
    $student_id = trim($_POST['student_id']);
    $child_teacher = trim($_POST['child_teacher']);
    $child_age = trim($_POST['child_age']);
    $child_grade = trim($_POST['child_grade']);
    $child_section = trim($_POST['child_section']);
    $child_address = trim($_POST['child_address']);
    $authorized_person = trim($_POST['authorized_person']);

    if (empty($parent_id) || !is_numeric($parent_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid Parent ID']);
        exit;
    }
    if (empty($child_name)) {
        echo json_encode(['success' => false, 'message' => 'Child Name is required']);
        exit;
    }
    if (empty($student_id)) {
        echo json_encode(['success' => false, 'message' => 'Student ID is required']);
        exit;
    }

    if (isset($_FILES['child_image']) && $_FILES['child_image']['error'] == UPLOAD_ERR_OK) {
        $image_tmp_name = $_FILES['child_image']['tmp_name'];
        $image_data = file_get_contents($image_tmp_name);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error in image upload or no image uploaded']);
        exit;
    }

    // Define the QR code save path
    $qr_save_path = 'C:\\xampp\\htdocs\\capstone-main\\qrcodes\\';

    // Ensure the directory exists
    if (!is_dir($qr_save_path)) {
        mkdir($qr_save_path, 0777, true);
    }

    // Generate the QR code URL
    $qr_url = "http://localhost/Teacher%20front/fetch_data.php?student_id=" . urlencode($student_id) . "&authorized_id=" . urlencode($authorized_person);


    // Save QR code as a PNG file
    $qr_file_path = $qr_save_path . $student_id . '.png';
    QRcode::png($qr_url, $qr_file_path, QR_ECLEVEL_M, 6, 2);

    // Capture QR code as binary data
    ob_start();
    QRcode::png($qr_url, null, QR_ECLEVEL_M, 6, 2);
    $qrimage_data = ob_get_clean();

    // Insert into database
    $child_sql = "INSERT INTO child_acc 
                  (parent_id, child_name, student_id, child_teacher, child_age, child_grade, child_section, child_address, child_image, authorized_id, qrimage) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($child_sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Database prepare failed: ' . htmlspecialchars($conn->error)]);
        exit;
    }

    $stmt->bind_param("ississsssss", $parent_id, $child_name, $student_id, $child_teacher, $child_age, $child_grade, $child_section, $child_address, $image_data, $authorized_person, $qrimage_data);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Child record created successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error inserting child record: ' . htmlspecialchars($stmt->error)]);
    }

    $stmt->close();
}

$conn->close();
?>
